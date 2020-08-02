package master

import (
	"crontab/common"
	"encoding/json"
	"net"
	"net/http"
	"strconv"
	"time"
)

// 任务的http接口
type ApiServer struct {
	httpServer *http.Server
}

var (
	// 单例对象
	G_apiServer *ApiServer
)

// 保存任务接口
// POST job={"name":"job1","command":"echo hello","cronExpr":"* * * * *"}
func handlerJobSave(w http.ResponseWriter, r *http.Request) {
	var (
		err     error
		postJob string
		job     common.Job
		oldJob  *common.Job
		bytes   []byte
	)
	// 解析post表单
	if err = r.ParseForm(); err != nil {
		goto ERR
	}
	postJob = r.PostForm.Get("job")
	if err = json.Unmarshal([]byte(postJob), &job); err != nil {
		goto ERR
	}
	if oldJob, err = G_jobMgr.SaveJob(&job); err != nil {
		goto ERR
	}
	// 返回应答
	if bytes, err = common.BuildResponse(0, "success", oldJob); err == nil {
		w.Write(bytes)
	}

	return

ERR:
	if bytes, err = common.BuildResponse(-1, err.Error(), nil); err == nil {
		w.Write(bytes)
	}

}

// 删除任务接口
// POST /job/delete name=job1
func handlerJobDelete(w http.ResponseWriter, r *http.Request) {
	var (
		err    error
		name   string
		oldJob *common.Job
		bytes  []byte
	)
	if err = r.ParseForm(); err != nil {
		goto ERR
	}
	name = r.PostForm.Get("name")
	if oldJob, err = G_jobMgr.DeleteJob(name); err != nil {
		goto ERR
	}

	if bytes, err = common.BuildResponse(0, "success", oldJob); err == nil {
		w.Write(bytes)
	}

	return

ERR:
	if bytes, err = common.BuildResponse(-1, err.Error(), nil); err == nil {
		w.Write(bytes)
	}
}

// 任务列表接口
// GET
func handlerJobList(w http.ResponseWriter, r *http.Request) {
	var (
		jobList []*common.Job
		err     error
		bytes   []byte
	)

	if jobList, err = G_jobMgr.ListJobs(); err != nil {
		goto ERR
	}
	if bytes, err = common.BuildResponse(0, "success", jobList); err == nil {
		w.Write(bytes)
	}
	return

ERR:
	if bytes, err = common.BuildResponse(-1, err.Error(), nil); err == nil {
		w.Write(bytes)
	}
}

// 强制杀死某个任务
func handlerJobKill(w http.ResponseWriter, r *http.Request) {
	var (
		err   error
		name  string
		bytes []byte
	)

	if err = r.ParseForm(); err != nil {
		goto ERR
	}
	name = r.PostForm.Get("name")
	if err = G_jobMgr.KillJob(name); err != nil {
		goto ERR
	}
	if bytes, err = common.BuildResponse(0, "success", nil); err == nil {
		w.Write(bytes)
	}
	return

ERR:
	if bytes, err = common.BuildResponse(-1, err.Error(), nil); err == nil {
		w.Write(bytes)
	}
}

// 日志查询
func handlerJobLog(w http.ResponseWriter, r *http.Request) {
	var (
		err        error
		name       string
		skipParam  string // 从第几条开始
		limitParam string // 返回多少条
		skip       int
		limit      int
		logArr     []*common.JobLog
		bytes      []byte
	)

	if err = r.ParseForm(); err != nil {
		goto ERR
	}
	// 获取请求参数 /job/log?name=job10&skip=0&limit=10
	name = r.Form.Get("name")
	skipParam = r.Form.Get("skip")
	limitParam = r.Form.Get("limit")
	if skip, err = strconv.Atoi(skipParam); err != nil {
		skip = 0
	}
	if limit, err = strconv.Atoi(limitParam); err != nil {
		limit = 20
	}
	if logArr, err = G_logMgr.ListLog(name, skip, limit); err != nil {
		goto ERR
	}
	if bytes, err = common.BuildResponse(0, "success", logArr); err == nil {
		w.Write(bytes)
	}
	return

ERR:
	if bytes, err = common.BuildResponse(-1, err.Error(), nil); err == nil {
		w.Write(bytes)
	}
}

// 获取worker结点
func handlerWorkerList(w http.ResponseWriter, r *http.Request) {
	var (
		workerArr []string
		err error
		bytes []byte
	)

	if workerArr, err = G_workerMgr.ListWorkers(); err != nil {
		goto ERR
	}
	if bytes, err = common.BuildResponse(0, "success", workerArr); err == nil {
		w.Write(bytes)
	}
	return

ERR:
	if bytes, err = common.BuildResponse(-1, err.Error(), nil); err == nil {
		w.Write(bytes)
	}
}

// 初始化服务
func InitApiServer() (err error) {
	var (
		mux           *http.ServeMux
		listener      net.Listener
		httpServer    *http.Server
		staticDir     http.Dir     // 静态文件根目录
		staticHandler http.Handler // 静态文件处理回调
	)

	// 配置路由
	mux = http.NewServeMux()
	mux.HandleFunc("/job/save", handlerJobSave)
	mux.HandleFunc("/job/delete", handlerJobDelete)
	mux.HandleFunc("/job/list", handlerJobList)
	mux.HandleFunc("/job/kill", handlerJobKill)
	mux.HandleFunc("/job/log", handlerJobLog)
	mux.HandleFunc("/worker/list", handlerWorkerList)

	// 静态文件目录
	staticDir = http.Dir(G_config.WebRoot)
	staticHandler = http.FileServer(staticDir)
	mux.Handle("/", http.StripPrefix("/", staticHandler))

	// 启动TCP监听
	if listener, err = net.Listen("tcp", ":"+strconv.Itoa(G_config.ApiPort)); err != nil {
		return
	}
	// 创建HTTP服务
	httpServer = &http.Server{
		ReadTimeout:  time.Duration(G_config.ApiReadTimeout) * time.Millisecond,
		WriteTimeout: time.Duration(G_config.ApiWirteTimeout) * time.Millisecond,
		Handler:      mux,
	}
	// 赋值单例
	G_apiServer = &ApiServer{
		httpServer: httpServer,
	}
	// 启动服务端
	go httpServer.Serve(listener)

	return
}
