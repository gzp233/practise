package worker

import (
	"context"
	"crontab/common"
	"time"

	"github.com/coreos/etcd/mvcc/mvccpb"

	"go.etcd.io/etcd/clientv3"
)

// 任务管理器
type JobMgr struct {
	client  *clientv3.Client
	kv      clientv3.KV
	lease   clientv3.Lease
	watcher clientv3.Watcher
}

var (
	// 单例
	G_jobMgr *JobMgr
)

// 监听任务变化
func (jobMgr *JobMgr) watchJobs() (err error) {
	var (
		getResp           *clientv3.GetResponse
		kvpair            *mvccpb.KeyValue
		job               *common.Job
		watchStartRevison int64
		watchChan         clientv3.WatchChan
		watchResp         clientv3.WatchResponse
		watchEvent        *clientv3.Event
		jobName           string
		jobEvent          *common.JobEvent
	)
	// get一下所有任务，冰获取当前集群的revision
	if getResp, err = jobMgr.kv.Get(context.TODO(), common.JOB_SAVE_DIR, clientv3.WithPrefix()); err != nil {
		return
	}
	for _, kvpair = range getResp.Kvs {
		if job, err = common.UnpackJob(kvpair.Value); err == nil {
			jobEvent = common.BuildJobEvent(common.JOB_EVENT_SAVE, job)
			// 把job同步给schedule
			G_scheduler.PushJobEvent(jobEvent)
		}
	}

	// 从该revision向后监听事件变化
	go func() {
		watchStartRevison = getResp.Header.Revision + 1
		watchChan = jobMgr.watcher.Watch(context.TODO(), common.JOB_SAVE_DIR, clientv3.WithRev(watchStartRevison), clientv3.WithPrefix())
		// 处理监听事件
		for watchResp = range watchChan {
			for _, watchEvent = range watchResp.Events {
				switch watchEvent.Type {
				case mvccpb.PUT: // 任务保存了
					if job, err = common.UnpackJob(watchEvent.Kv.Value); err != nil {
						continue
					}
					// 构建一个更新Event
					jobEvent = common.BuildJobEvent(common.JOB_EVENT_SAVE, job)
				case mvccpb.DELETE: // 任务删除了
					jobName = common.ExtractJobName(string(watchEvent.Kv.Key))
					// 构建一个删除Event
					job = &common.Job{Name: jobName}
					jobEvent = common.BuildJobEvent(common.JOB_EVENT_DELETE, job)
				}
				//推给scheduler
				G_scheduler.PushJobEvent(jobEvent)
			}
		}
	}()
	return
}

// 监听强杀任务
func (jobMgr *JobMgr) watchKiller() {
	var (
		job        *common.Job
		watchChan  clientv3.WatchChan
		watchResp  clientv3.WatchResponse
		watchEvent *clientv3.Event
		jobName    string
		jobEvent   *common.JobEvent
	)

	// 监听强杀任务
	go func() {
		watchChan = jobMgr.watcher.Watch(context.TODO(), common.JOB_SAVE_DIR, clientv3.WithPrefix())
		// 处理监听事件
		for watchResp = range watchChan {
			for _, watchEvent = range watchResp.Events {
				switch watchEvent.Type {
				case mvccpb.PUT: // 杀死任务事件
					jobName = common.ExtractKillerName(string(watchEvent.Kv.Key))
					job = &common.Job{Name: jobName}
					jobEvent = common.BuildJobEvent(common.JOB_EVENT_KILL, job)
					// 事件推给scheduler
					G_scheduler.PushJobEvent(jobEvent)
				case mvccpb.DELETE: // killer标记过期，被自动删除
				}
				//推给scheduler
				G_scheduler.PushJobEvent(jobEvent)
			}
		}
	}()
	return
}

// 初始化管理器
func InitJobMgr() (err error) {
	var (
		config  clientv3.Config
		client  *clientv3.Client
		kv      clientv3.KV
		lease   clientv3.Lease
		watcher clientv3.Watcher
	)
	config = clientv3.Config{
		Endpoints:   G_config.EtcdEndpoints,
		DialTimeout: time.Duration(G_config.EtcdDialTimeout) * time.Millisecond,
	}

	// 建立连接
	if client, err = clientv3.New(config); err != nil {
		return
	}

	// 得到kv和lease
	kv = clientv3.NewKV(client)
	lease = clientv3.NewLease(client)
	watcher = clientv3.NewWatcher(client)

	// 赋值单例
	G_jobMgr = &JobMgr{
		client:  client,
		kv:      kv,
		lease:   lease,
		watcher: watcher,
	}

	// 启动任务监听
	G_jobMgr.watchJobs()

	// 监听强杀任务
	G_jobMgr.watchKiller()

	return
}

// 创建任务执行锁
func (jobMgr *JobMgr) CreateJobLock(jobName string) (jobLock *JobLock) {
	// 返回一把锁
	jobLock = InitJobLock(jobName, jobMgr.kv, jobMgr.lease)
	return
}
