package main

import (
	"fmt"
	"micro_service/auth/handler"
	"micro_service/auth/model"
	"micro_service/basic"
	"micro_service/basic/common"
	"micro_service/basic/config"
	tracer "micro_service/plugins/tracer/jaeger"
	z "micro_service/plugins/zap"
	"time"

	"github.com/opentracing/opentracing-go"

	"go.uber.org/zap"

	"github.com/micro/go-plugins/config/source/grpc"

	"github.com/micro/cli"
	"github.com/micro/go-micro/registry/etcd"

	"github.com/micro/go-micro"
	"github.com/micro/go-micro/registry"
	openTrace "github.com/micro/go-plugins/wrapper/trace/opentracing"

	s "micro_service/auth/proto/auth"
)

var (
	log     = z.GetLogger()
	appName = "auth_srv"
	cfg     = &authCfg{}
)

type authCfg struct {
	common.AppCfg
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

	basic.Init(
		config.WithSource(source),
		config.WithApp(appName),
	)

	err := config.C().App(appName, cfg)
	if err != nil {
		panic(err)
	}

	log.Info("[initCfg] 配置", zap.Any("cfg", cfg))

	return
}

func main() {
	// 初始化配置、数据库等信息
	initCfg()

	// 使用etcd注册
	micReg := etcd.NewRegistry(registryOptions)

	t, io, err := tracer.NewTracer(cfg.Name, "")
	if err != nil {
		log.Error("[tracer] error")
		panic(err)
	}
	defer io.Close()
	opentracing.SetGlobalTracer(t)

	// 新建服务
	service := micro.NewService(
		micro.Name(cfg.Name),
		micro.RegisterTTL(time.Second*30),
		micro.RegisterInterval(time.Second*20),
		micro.Registry(micReg),
		micro.Version(cfg.Version),
		micro.Address(cfg.Addr()),
		micro.WrapHandler(openTrace.NewHandlerWrapper(opentracing.GlobalTracer())),
	)

	// 服务初始化
	service.Init(
		micro.Action(func(c *cli.Context) {
			// 初始化handler
			model.Init()
			// 初始化handler
			handler.Init()
		}),
	)

	// 注册服务
	s.RegisterServiceHandler(service.Server(), new(handler.Service))

	// 启动服务
	if err := service.Run(); err != nil {
		log.Error("[main] error")
		panic(err)
	}
}
