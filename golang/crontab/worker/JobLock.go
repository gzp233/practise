package worker

import (
	"context"
	"crontab/common"

	"go.etcd.io/etcd/clientv3"
)

// 分布式锁（TXN事务）
type JobLock struct {
	kv         clientv3.KV
	lease      clientv3.Lease
	jobName    string
	cancelFunc context.CancelFunc // 终止自动续租
	leaseId    clientv3.LeaseID
	isLocked   bool
}

// 初始化
func InitJobLock(jobName string, kv clientv3.KV, lease clientv3.Lease) (jobLock *JobLock) {
	jobLock = &JobLock{
		jobName: jobName,
		kv:      kv,
		lease:   lease,
	}
	return
}

// 尝试上锁
func (jobLock *JobLock) TryLock() (err error) {
	var (
		leaseGrantResp *clientv3.LeaseGrantResponse
		cancelCtx      context.Context
		cancelFunc     context.CancelFunc
		leaseId        clientv3.LeaseID
		keepRespChan   <-chan *clientv3.LeaseKeepAliveResponse
		txn            clientv3.Txn
		lockKey        string
		txnResp        *clientv3.TxnResponse
	)

	// 创建租约（5秒）
	if leaseGrantResp, err = jobLock.lease.Grant(context.TODO(), 5); err != nil {
		return
	}
	// 自动续租
	cancelCtx, cancelFunc = context.WithCancel(context.TODO())
	leaseId = leaseGrantResp.ID
	if keepRespChan, err = jobLock.lease.KeepAlive(cancelCtx, leaseId); err != nil {
		goto FAIL
	}

	go func() {
		var (
			keepResp *clientv3.LeaseKeepAliveResponse
		)
		for {
			select {
			case keepResp = <-keepRespChan: // 自动续租的应答
				if keepResp == nil {
					goto END
				}
			}
		}
	END:
	}()

	//创建事务txn
	txn = jobLock.kv.Txn(context.TODO())
	lockKey = common.JOB_LOCK_DIR + jobLock.jobName
	//事务抢锁
	txn.If(clientv3.Compare(clientv3.CreateRevision(lockKey), "=", 0)).
		Then(clientv3.OpPut(lockKey, "", clientv3.WithLease(leaseId))).
		Else(clientv3.OpGet(lockKey))
	if txnResp, err = txn.Commit(); err != nil {
		goto FAIL
	}
	//成功返回，失败释放租约
	if !txnResp.Succeeded { // 锁被占用
		err = common.ERR_LOCK_ALREADY_REQUIRED
		goto FAIL
	}
	// 抢锁成功
	jobLock.leaseId = leaseId
	jobLock.cancelFunc = cancelFunc
	jobLock.isLocked = true

	return

FAIL:
	cancelFunc() // 取消自动续租
	jobLock.lease.Revoke(context.TODO(), leaseId)
	return
}

func (jobLock *JobLock) Unlock() {
	if jobLock.isLocked {
		jobLock.cancelFunc() // 取消协程
		jobLock.lease.Revoke(context.TODO(), jobLock.leaseId)
	}
}
