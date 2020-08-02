package v1

import (
	"net/http"
	"single/pkg/app"
	"single/pkg/e"
	"single/pkg/logging"
	"single/pkg/rbac"
	"single/pkg/setting"
	"single/pkg/util"
	"single/services/roleservice"

	"github.com/unknwon/com"

	"github.com/jinzhu/copier"

	"github.com/gin-gonic/gin"
)

// @Summary get role list
// @Tags  roles
// @Accept json
// @Produce  json
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/roles  [GET]
func GetRoles(c *gin.Context) {
	appG := app.Gin{C: c}
	roleService := roleservice.Role{
		PageNum:  util.GetPage(c),
		PageSize: setting.AppSetting.PageSize,
	}

	total, err := roleService.Count()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_COUNT_FAIL, nil)
		return
	}

	roles, err := roleService.GetList()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_GET_FAIL, nil)
		return
	}

	data := make(map[string]interface{})
	data["lists"] = roles
	data["total"] = total

	appG.Response(http.StatusOK, e.SUCCESS, data)
}

type addRoleForm struct {
	Name          string `valid:"Required; MaxSize(32)" chn:"角色名称" json:"name"`
	PermissionIds []int  `json:"permission_ids"`
}

// @Summary add a role
// @Tags  roles
// @Accept json
// @Produce  json
// @Param   body body addRoleForm true "name is required"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/roles  [POST]
func AddRole(c *gin.Context) {
	appG := app.Gin{C: c}
	var a addRoleForm
	if ok, errMsg := app.BindAndValid(c, &a); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}

	roleService := roleservice.Role{}
	_ = copier.Copy(&roleService, &a)

	exists, err := roleService.ExistByName()
	if err != nil {
		logging.Error("roleService.ExistByName error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if exists {
		appG.Response(http.StatusBadRequest, e.ERROR_EXIST, "该名称已存在")
		return
	}
	id, err := roleService.Add()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	// 权限刷新
	err = rbac.RbacDefault.Rbac.RoleAPI.LoadPolicy(id)
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_EDIT_FAIL, nil)
		return
	}
	appG.Response(http.StatusOK, e.SUCCESS, nil)
}

type editRoleForm struct {
	ID            int    `valid:"Required;Min(1)"`
	Name          string `valid:"Required; MaxSize(32)" chn:"角色名称" json:"name"`
	PermissionIds []int  `json:"permission_ids"`
}

// @Summary update a role
// @Tags  roles
// @Accept json
// @Produce  json
// @Param   id path int true "role Id"
// @Param   body body editRoleForm true "id and name is required"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/roles/{id}  [PUT]
func EditRole(c *gin.Context) {
	appG := app.Gin{C: c}
	var ef editRoleForm
	if ok, errMsg := app.BindAndValid(c, &ef); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}

	roleService := roleservice.Role{}
	_ = copier.Copy(&roleService, &ef)

	exists, err := roleService.ExistByName()
	if err != nil {
		logging.Error("roleService.ExistByName error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if exists {
		appG.Response(http.StatusBadRequest, e.ERROR_EXIST, "该名称已存在")
		return
	}
	if exists, err = roleService.ExistByID(); err != nil {
		logging.Error("roleService.ExistByID error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if !exists {
		appG.Response(http.StatusBadRequest, e.ERROR_NOT_EXIST, "该角色不存在")
		return
	}
	if err := roleService.Edit(); err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	// 权限刷新
	err = rbac.RbacDefault.Rbac.RoleAPI.LoadPolicy(ef.ID)
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_EDIT_FAIL, nil)
		return
	}
	appG.Response(http.StatusOK, e.SUCCESS, nil)
}

// @Summary delete a role
// @Tags  roles
// @Accept json
// @Produce  json
// @Param   id path int true "role Id"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/roles/{id}  [DELETE]
func DeleteRole(c *gin.Context) {
	appG := app.Gin{C: c}
	id := com.StrTo(c.Param("id")).MustInt()
	if id <= 0 {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, nil)
		return
	}

	roleService := roleservice.Role{ID: id}
	role, err := roleService.GetByID()
	if err != nil {
		logging.Error("roleService.GetByID error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if role.ID <= 0 {
		appG.Response(http.StatusBadRequest, e.ERROR_NOT_EXIST, "该角色不存在")
		return
	}
	if err := roleService.Delete(); err != nil {
		logging.Error("roleService.Delete error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	rbac.RbacDefault.Enforcer.DeleteRole(role.Name)
	appG.Response(http.StatusOK, e.SUCCESS, nil)
}
