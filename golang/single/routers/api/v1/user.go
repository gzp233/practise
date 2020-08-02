package v1

import (
	"net/http"
	"single/pkg/app"
	"single/pkg/e"
	"single/pkg/logging"
	"single/pkg/rbac"
	"single/pkg/setting"
	"single/pkg/util"
	"single/services/userservice"

	"github.com/unknwon/com"

	"github.com/gin-gonic/gin"
	"github.com/jinzhu/copier"
)

// @Summary get user list
// @Tags  users
// @Accept json
// @Produce  json
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/users  [GET]
func GetUsers(c *gin.Context) {
	appG := app.Gin{C: c}
	userService := userservice.User{
		PageNum:  util.GetPage(c),
		PageSize: setting.AppSetting.PageSize,
	}

	total, err := userService.Count()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_COUNT_FAIL, nil)
		return
	}

	users, err := userService.GetList()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_GET_FAIL, nil)
		return
	}

	data := make(map[string]interface{})
	data["lists"] = users
	data["total"] = total

	appG.Response(http.StatusOK, e.SUCCESS, data)
}

type addUserForm struct {
	Username   string `valid:"Required;MinSize(2);MaxSize(32)" chn:"昵称" json:"username"`
	Email      string `valid:"Required; MinSize(6); MaxSize(128);Email" chn:"邮箱" json:"email"`
	Password   string `valid:"Required; MinSize(6); MaxSize(20)" chn:"密码" json:"password"`
	Avatar     string `valid:"MaxSize(255)" chn:"头像" json:"avatar"`
	SocialId   string `valid:"MaxSize(128)" json:"social_id"`
	SocialType string `valid:"MaxSize(32)" json:"social_type"`
	RoleIds    []int  `json:"role_ids"`
}

// @Summary add a user
// @Tags  users
// @Accept json
// @Produce  json
// @Param   body body addUserForm true "Email, Username and password is required"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/users  [POST]
func AddUser(c *gin.Context) {
	appG := app.Gin{C: c}
	var a addUserForm
	if ok, errMsg := app.BindAndValid(c, &a); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}

	userService := userservice.User{}
	_ = copier.Copy(&userService, &a)

	exists, err := userService.ExistByEmail()
	if err != nil {
		logging.Error("userService.ExistByEmail error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if exists {
		appG.Response(http.StatusBadRequest, e.ERROR_EXIST, "该邮箱已被使用")
		return
	}
	id, err := userService.Add()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	// 权限刷新
	err = rbac.RbacDefault.Rbac.UserAPI.LoadPolicy(id)
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_EDIT_FAIL, nil)
		return
	}
	appG.Response(http.StatusOK, e.SUCCESS, nil)
}

type editUserForm struct {
	ID         int    `valid:"Required;Min(1)"`
	Username   string `valid:"Required;MinSize(2);MaxSize(32)" chn:"昵称" json:"username"`
	Email      string `valid:"Required; MinSize(6); MaxSize(128);Email" chn:"邮箱" json:"email"`
	Password   string `valid:"Required; MinSize(6); MaxSize(20)" chn:"密码" json:"password"`
	Avatar     string `valid:"MaxSize(255)" chn:"头像" json:"avatar"`
	SocialId   string `valid:"MaxSize(128)" json:"social_id"`
	SocialType string `valid:"MaxSize(32)" json:"social_type"`
	RoleIds    []int  `json:"role_ids"`
}

// @Summary update a user
// @Tags  users
// @Accept json
// @Produce  json
// @Param   id path int true "user Id"
// @Param   body body editUserForm true "Email, Username and password is required"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/users/{id}  [PUT]
func EditUser(c *gin.Context) {
	appG := app.Gin{C: c}
	var ef editUserForm
	if ok, errMsg := app.BindAndValid(c, &ef); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}

	userService := userservice.User{}
	_ = copier.Copy(&userService, &ef)

	exists, err := userService.ExistByEmail()
	if err != nil {
		logging.Error("userService.ExistByEmail error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if exists {
		appG.Response(http.StatusBadRequest, e.ERROR_EXIST, "该邮箱已被使用")
		return
	}
	if exists, err = userService.ExistByID(); err != nil {
		logging.Error("userService.ExistByID error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if !exists {
		appG.Response(http.StatusBadRequest, e.ERROR_NOT_EXIST, "该用户不存在")
		return
	}
	if err := userService.Edit(); err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	// 权限刷新
	err = rbac.RbacDefault.Rbac.UserAPI.LoadPolicy(ef.ID)
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_EDIT_FAIL, nil)
		return
	}
	appG.Response(http.StatusOK, e.SUCCESS, nil)
}

// @Summary delete a user
// @Tags  users
// @Accept json
// @Produce  json
// @Param   id path int true "user Id"
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/users/{id}  [DELETE]
func DeleteUser(c *gin.Context) {
	appG := app.Gin{C: c}
	id := com.StrTo(c.Param("id")).MustInt()
	if id <= 0 {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, nil)
		return
	}

	userService := userservice.User{ID: id}
	user, err := userService.GetByID()
	if err != nil {
		logging.Error("userService.GetByID error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if user.ID <= 0 {
		appG.Response(http.StatusBadRequest, e.ERROR_NOT_EXIST, "该用户不存在")
		return
	}
	if err := userService.Delete(); err != nil {
		logging.Error("userService.Delete error:", err)
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	// 删除原来的权限
	rbac.RbacDefault.Enforcer.DeleteUser(user.Email)

	appG.Response(http.StatusOK, e.SUCCESS, nil)
}
