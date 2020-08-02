package main

import (
	"context"
	"fmt"
	"ginb/models"
	"ginb/pkg/logging"
	"ginb/pkg/setting"
	"ginb/routers"
	"log"
	"net/http"
	"os"
	"os/signal"
	"time"
)

func main() {
	setting.Setup()
	models.Setup()
	logging.Setup()

	s := &http.Server{
		Addr:           fmt.Sprintf(":%d", setting.ServerSetting.HttpPort),
		Handler:        routers.InitRouter(),
		ReadTimeout:    setting.ServerSetting.ReadTimeout,
		WriteTimeout:   setting.ServerSetting.WriteTimeout,
		MaxHeaderBytes: 1 << 20,
	}

	go func() {
		if err := s.ListenAndServe(); err != nil && err != http.ErrServerClosed {
			log.Fatalf("listen:%s\n", err)
		}
	}()

	quit := make(chan os.Signal)
	signal.Notify(quit, os.Interrupt)
	<-quit
	log.Println("Shutdown Server ...")

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	if err := s.Shutdown(ctx); err != nil {
		log.Fatal("Server Shutdown:", err)
	}
	log.Println("Server exiting")
}
