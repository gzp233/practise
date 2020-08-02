package v1

import (
	"net/http"
	"single/pkg/app"
	"single/pkg/e"
	"single/pkg/logging"
	"single/pkg/rbac"
	"single/pkg/setting"
	"single/pkg/util"
	"single/services/permissionservice"

	"github.com/unknwon/com"

	"github.com/jinzhu/copier"

	"github.com/gin-gonic/gin"
)

// @Summary get permission list
// @Tags  permissions
// @Accept json
// @Produce  json
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/permissions  [GET]
func GetPermissions(c *gin.Context) {
	appG := app.Gin{C: c}
	permissionService := permissionservice.Permission{
		PageNum:  util.GetPage(c),
		PageSize: setting.AppSetting.PageSize,
	}

	total, err := permissionService.Count()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_COUNT_FAIL, nil)
		return
	}

	users, err := permissionService.GetList()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_GET_FAIL, nil)
		return
	}

	data := make(map[string]interface{})
	data["lists"] = users
	data["total"] = total

	appG.Response(http.StatusOK, e.SUCCESS, data)
}

// @Summary get permission tree
// @Tags  permissions
// @Accept json
// @Produce  json
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/permissions/tree  [GET]
func PermissionTree(c *gin.Context) {
	appG := app.Gin{C: c}
	permissionService := permissionservice.Permission{}
	permissions, err := permissionService.GetPermissionTree()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}

	appG.Response(http.StatusOK, e.SUCCESS, permissions)
}

var (
	types   = map[string]int{"menu": 1, "directory": 1, "button": 1}
	methods = map[string]int{"GET": 1, "POST": 1, "PUT": 1, "DELETE": 1}
)

type addPermissionForm struct {
	ParentId int    `valid:"Required; Min(0)" chn:"父权限" json:"parent_id"`
	Name     string `valid:"Required; MaxSize(64)" chn:"资源名" json:"name"`
	Method   string `valid:"Required; MaxSize(128)" chn:"访问方式" json:"method"`
	Path     string `valid:"Required; MaxSize(100)" chn:"访问路径" json:"path"`
	Type     string `valid:"Required; MaxSize(255)" chn:"资源类型" json:"type"`
}

// @Summary add a permission
// @Tags  permissions
// @Accept json
// @Produce  json
// @Param   body body addPermissionForm true "required"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/permissions  [POST]
func AddPermission(c *gin.Context) {
	appG := app.Gin{C: c}
	var a addPermissionForm
	if ok, errMsg := app.BindAndValid(c, &a); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}
	if _, ok := types[a.Type]; !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, "资源类型错误")
		return
	}
	if _, ok := methods[a.Method]; !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, "访问方式错误")
		return
	}

	permissionService := permissionservice.Permission{}
	_ = copier.Copy(&permissionService, &a)

	if _, err := permissionService.Add(); err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	appG.Response(http.StatusOK, e.SUCCESS, nil)
}

type editPermissionForm struct {
	ID       int    `valid:"Required;Min(1)"`
	ParentId int    `valid:"Required; Min(0)" chn:"父权限" json:"parent_id"`
	Name     string `valid:"Required; MaxSize(64)" chn:"资源名" json:"name"`
	Method   string `valid:"Required; MaxSize(128)" chn:"访问方式" json:"method"`
	Path     string `valid:"Required; MaxSize(100)" chn:"访问路径" json:"path"`
	Type     string `valid:"Required; MaxSize(255)" chn:"资源类型" json:"type"`
}

// @Summary update a permission
// @Tags  permissions
// @Accept json
// @Produce  json
// @Param   id path int true "permission Id"
// @Param   body body editPermissionForm true "editPermissionForm"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/permissions/{id}  [PUT]
func EditPermission(c *gin.Context) {
	appG := app.Gin{C: c}
	var ef editPermissionForm
	if ok, errMsg := app.BindAndValid(c, &ef); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}
	if _, ok := types[ef.Type]; !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, "资源类型错误")
		return
	}
	if _, ok := methods[ef.Method]; !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, "访问方式错误")
		return
	}

	permissionService := permissionservice.Permission{}
	_ = copier.Copy(&permissionService, &ef)

	permission, err := permissionService.GetByID()
	if err != nil {
		logging.Error("permissionService.GetByID error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if permission.ID <= 0 {
		appG.Response(http.StatusBadRequest, e.ERROR_NOT_EXIST, "该资源不存在")
		return
	}
	if err := permissionService.Edit(); err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}

	appG.Response(http.StatusOK, e.SUCCESS, nil)
}

// @Summary delete a permission
// @Tags  permissions
// @Accept json
// @Produce  json
// @Param   id path int true "permission Id"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/permissions/{id}  [DELETE]
func DeletePermission(c *gin.Context) {
	appG := app.Gin{C: c}
	id := com.StrTo(c.Param("id")).MustInt()
	if id <= 0 {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, nil)
		return
	}

	permissionService := permissionservice.Permission{ID: id}
	permission, err := permissionService.GetByID()
	if err != nil {
		logging.Error("permissionService.GetByID error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if permission.ID <= 0 {
		appG.Response(http.StatusBadRequest, e.ERROR_NOT_EXIST, "该资源不存在")
		return
	}
	if err := permissionService.Delete(); err != nil {
		logging.Error("permissionService.Delete error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	_, _ = rbac.RbacDefault.Enforcer.DeletePermission(permission.Path, permission.Method)

	appG.Response(http.StatusOK, e.SUCCESS, nil)
}
