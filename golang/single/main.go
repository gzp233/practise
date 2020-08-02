package main

import (
	"fmt"
	"net/http"
	"single/models"
	"single/pkg/gredis"
	"single/pkg/logging"
	"single/pkg/rbac"
	"single/pkg/setting"
	"single/routers"
)

// @title single
// @version 0.1
// @description single

// @contact.name jilu
// @contact.url https://github.com/gzp199301
// @contact.email 2627427007@qq.com

// @securityDefinitions.apikey ApiKeyAuth
// @in header
// @name Authorization

// @license.name MIT
// @license.url https://github.com/gzp199301/single/blob/master/LICENSE

// @host localhost:6628
// @BasePath /
func main() {
	AppSetup()
	router := routers.InitRouter()
	endPoint := fmt.Sprintf(":%d", setting.ServerSetting.HttpPort)
	maxHeaderBytes := 1 << 20

	server := &http.Server{
		Addr:           endPoint,
		Handler:        router,
		ReadTimeout:    setting.ServerSetting.ReadTimeout,
		WriteTimeout:   setting.ServerSetting.WriteTimeout,
		MaxHeaderBytes: maxHeaderBytes,
	}

	logging.Info("start http server listening ", endPoint)

	if err := server.ListenAndServe(); err != nil {
		logging.Fatal("启动server失败：", err)
	}
}

// init app
func AppSetup() {
	setting.Setup()
	logging.Setup()
	models.Setup()
	gredis.Setup()
	rbac.Setup()
}
