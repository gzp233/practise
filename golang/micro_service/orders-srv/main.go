package main

import (
	"fmt"
	"micro_service/basic"
	"micro_service/basic/common"
	"micro_service/basic/config"
	"micro_service/orders-srv/handler"
	"micro_service/orders-srv/model"
	"micro_service/orders-srv/subscriber"
	tracer "micro_service/plugins/tracer/jaeger"
	"os"
	"time"

	"github.com/opentracing/opentracing-go"

	"github.com/micro/go-plugins/config/source/grpc"

	"github.com/micro/cli"
	"github.com/micro/go-micro"
	"github.com/micro/go-micro/registry"
	"github.com/micro/go-micro/registry/etcd"
	"github.com/micro/go-micro/util/log"

	proto "micro_service/orders-srv/proto/orders"

	openTrace "github.com/micro/go-plugins/wrapper/trace/opentracing"
)

var (
	appName = "orders_srv"
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
			// 初始化模型层
			model.Init()
			// 初始化handler
			handler.Init()
			// 初始化sub
			subscriber.Init()
		}),
	)

	// 侦听订单支付消息
	err = micro.RegisterSubscriber(common.TopicPaymentDone, service.Server(), subscriber.PayOrder)
	if err != nil {
		log.Fatal(err)
	}

	// 注册服务
	err = proto.RegisterOrdersHandler(service.Server(), new(handler.Orders))
	if err != nil {
		log.Fatal(err)
	}

	// 启动服务
	if err = service.Run(); err != nil {
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
	configAddr := os.Getenv("MICRO_BOOK_CONFIG_GRPC_ADDR")
	source := grpc.NewSource(
		grpc.WithAddress(configAddr),
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
