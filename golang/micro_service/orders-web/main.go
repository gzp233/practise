package main

import (
	"fmt"
	"micro_service/basic"
	"micro_service/basic/common"
	"micro_service/basic/config"
	tracer "micro_service/plugins/tracer/jaeger"
	"micro_service/plugins/tracer/opentracing/std2micro"
	"net/http"
	"time"

	"github.com/opentracing/opentracing-go"

	"github.com/micro/go-plugins/config/source/grpc"

	"github.com/micro/cli"
	"github.com/micro/go-micro/registry"
	"github.com/micro/go-micro/registry/etcd"
	"github.com/micro/go-micro/util/log"

	"micro_service/orders-web/handler"

	"github.com/micro/go-micro/web"
)

var (
	appName = "orders_web"
	cfg     = &appCfg{}
)

type appCfg struct {
	common.AppCfg
}

func main() {
	// 初始化配置、数据库等信息
	initCfg()

	// 使用etcd注册
	micReg := etcd.NewRegistry(registryOptions)
	t, io, err := tracer.NewTracer(cfg.Name, "")
	if err != nil {
		log.Fatal(err)
	}
	defer io.Close()

	opentracing.SetGlobalTracer(t)

	// 创建新服务
	service := web.NewService(
		web.Name(cfg.Name),
		web.Version(cfg.Version),
		web.RegisterTTL(time.Second*30),
		web.RegisterInterval(time.Second*20),
		web.Registry(micReg),
		web.Address(cfg.Addr()),
	)

	// 初始化服务
	if err := service.Init(
		web.Action(
			func(c *cli.Context) {
				// 初始化handler
				handler.Init()
			}),
	); err != nil {
		log.Fatal(err)
	}

	// 新建订单接口
	authHandler := http.HandlerFunc(handler.New)
	service.Handle("/orders/new", std2micro.TracerWrapper(handler.AuthWrapper(authHandler)))
	service.Handle("/", std2micro.TracerWrapper(http.HandlerFunc(handler.Hello)))

	// 运行服务
	if err := service.Run(); err != nil {
		log.Fatal(err)
	}
}

func registryOptions(ops *registry.Options) {
	etcdCfg := &common.Etcd{}
	err := config.C().App("etcd", etcdCfg)
	if err != nil {
		panic(err)
	}
	ops.Addrs = []string{fmt.Sprintf("%s:%d", etcdCfg.Host, etcdCfg.Port)}
}

func initCfg() {
	source := grpc.NewSource(
		grpc.WithAddress("127.0.0.1:9600"),
		grpc.WithPath("micro"),
	)

	basic.Init(config.WithSource(source))

	err := config.C().App(appName, cfg)
	if err != nil {
		panic(err)
	}

	log.Logf("[initCfg] 配置，cfg：%v", cfg)

	return
}
