package worker

import (
	"context"
	"crontab/common"
	"time"

	"go.mongodb.org/mongo-driver/mongo/options"

	"go.mongodb.org/mongo-driver/mongo"
)

// mongodb存储日志
type LogSink struct {
	client         *mongo.Client
	logCollection  *mongo.Collection
	logChan        chan *common.JobLog
	autoCommitChan chan *common.LogBatch
}

var (
	// 单例
	G_logSink *LogSink
)

// 批量写入日志
func (logSink *LogSink) saveLogs(batch *common.LogBatch) {
	logSink.logCollection.InsertMany(context.TODO(), batch.Logs)
}

// 日志存储协程
func (logSink *LogSink) writeLoop() {
	var (
		log          *common.JobLog
		logBatch     *common.LogBatch // 当前的批次
		commitTimer  *time.Timer
		timeoutBatch *common.LogBatch // 超时批次
	)

	for {
		select {
		case log = <-logSink.logChan:
			if logBatch == nil {
				logBatch = &common.LogBatch{}
				// 让这个批次超时自动提交(给1秒的时间）
				commitTimer = time.AfterFunc(
					time.Duration(G_config.JobLogCommitTimeout)*time.Millisecond,
					func(batch *common.LogBatch) func() {
						return func() {
							logSink.autoCommitChan <- batch
						}
					}(logBatch),
				)
			}

			// 把新日志追加到批次中
			logBatch.Logs = append(logBatch.Logs, log)

			// 如果批次满了, 就立即发送
			if len(logBatch.Logs) >= G_config.JobLogBatchSize {
				// 发送日志
				logSink.saveLogs(logBatch)
				// 清空logBatch
				logBatch = nil
				// 取消定时器
				commitTimer.Stop()
			}
		case timeoutBatch = <-logSink.autoCommitChan: // 过期的批次
			// 判断过期批次是否仍旧是当前的批次
			if timeoutBatch != logBatch {
				continue // 跳过已经被提交的批次
			}
			// 把批次写入到mongo中
			logSink.saveLogs(timeoutBatch)
			// 清空logBatch
			logBatch = nil
		}
	}
}

// 发送日志
func (logSink *LogSink) Append(jobLog *common.JobLog) {
	select {
	case logSink.logChan <- jobLog:
	default:
		// 队列满了就丢弃
	}
}

// 初始化
func InitLogSink() (err error) {
	var (
		client *mongo.Client
		ctx    context.Context
		cancel context.CancelFunc
	)

	// 建立mongodb连接
	ctx, cancel = context.WithTimeout(context.Background(), time.Duration(G_config.MongodbConnectTimeout)*time.Millisecond)
	defer cancel()
	if client, err = mongo.Connect(
		ctx,
		options.Client().ApplyURI(G_config.MongodbUri),
	); err != nil {
		return
	}

	//   选择db和collection
	G_logSink = &LogSink{
		client:         client,
		logCollection:  client.Database("cron").Collection("log"),
		logChan:        make(chan *common.JobLog, 1000),
		autoCommitChan: make(chan *common.LogBatch, 1000),
	}

	// 启动一个mongodb处理协程
	go G_logSink.writeLoop()
	return
}