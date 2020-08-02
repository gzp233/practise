package worker

import (
	"crontab/common"
	"math/rand"
	"os/exec"
	"time"
)

// 任务执行器
type Executor struct {
}

var (
	G_executor *Executor
)

// 执行一个任务
func (executor *Executor) ExecuteJob(info *common.JobExecuteInfo) {
	go func() {
		var (
			cmd     *exec.Cmd
			err     error
			output  []byte
			result  *common.JobExecuteResult
			jobLock *JobLock
		)

		// 记录结果
		result = &common.JobExecuteResult{
			ExecuteInfo: info,
			Output:      make([]byte, 0),
		}

		result.StartTime = time.Now()
		// 首先获取分布式锁
		jobLock = G_jobMgr.CreateJobLock(info.Job.Name)
		time.Sleep(time.Duration(rand.Intn(1000)) * time.Millisecond)
		err = jobLock.TryLock()
		defer jobLock.Unlock()

		if err != nil {
			result.Err = err
			result.EndTime = time.Now()
		} else {
			result.StartTime = time.Now()
			// 执行shell命令
			cmd = exec.CommandContext(info.CancelCtx, "/bin/bash", "-c", info.Job.Command)
			// 执行并捕获输出,结果返回shceduler,从executingTable删除掉执行记录
			output, err = cmd.CombinedOutput()
			result.Output = output
			result.Err = err
			result.EndTime = time.Now()
		}
		G_scheduler.PushJobResult(result)
	}()
}

// 初始化执行器
func InitExecutor() (err error) {
	G_executor = &Executor{}
	return
}
