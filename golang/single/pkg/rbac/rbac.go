package rbac

import (
	"path"
	"single/pkg/logging"
	"single/services/rbac"

	"github.com/casbin/casbin/v2"
	"github.com/facebookgo/inject"
)

type Rbac struct {
	Rbac     *rbac.Rbac
	Enforcer *casbin.SyncedEnforcer
}

var RbacDefault *Rbac

func Setup() {
	g := new(inject.Graph)
	enforcer, err := casbin.NewSyncedEnforcer(path.Join("conf", "rbac.conf"), false)
	if err != nil {
		logging.Fatal("初始化权限策略失败,error:", err)
		return
	}
	g.Provide(&inject.Object{Value: enforcer})
	r := new(rbac.Rbac)
	g.Provide(&inject.Object{Value: r})
	if err := g.Populate(); err != nil {
		panic("初始化依赖注入发生错误：" + err.Error())
	}
	RbacDefault = &Rbac{
		Rbac:     r,
		Enforcer: enforcer,
	}

	if err := RbacDefault.Rbac.RoleAPI.LoadAllPolicy(); err != nil {
		logging.Fatal("初始化角色策略失败, error:", err)
		return
	}
	if err := RbacDefault.Rbac.UserAPI.LoadAllPolicy(); err != nil {
		logging.Fatal("初始化用户策略失败, error:", err)
	}
}
