package api

import (
	"net/http"
	"single/pkg/app"
	"single/pkg/e"
	"single/pkg/util"
	"single/services/authservice"
	"single/services/roleservice"

	"github.com/gin-gonic/gin"
)

type authForm struct {
	Email    string `valid:"Required; MinSize(6); MaxSize(128);Email" chn:"邮箱" json:"email"`
	Password string `valid:"Required; MinSize(6); MaxSize(20)" chn:"密码" json:"password"`
}

// @Summary Get Auth
// @Tags auth
// @Accept json
// @Produce  json
// @Param body body authForm true "Email and password is required"
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/auth  [POST]
func Auth(c *gin.Context) {
	appG := app.Gin{C: c}
	var a authForm
	if ok, errMsg := app.BindAndValid(c, &a); !ok {
		appG.Response(http.StatusBadRequest, e.INVALID_PARAMS, errMsg)
		return
	}

	authService := &authservice.Auth{Email: a.Email, Password: a.Password}
	ok, err := authService.Check()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}
	if !ok {
		appG.Response(http.StatusUnauthorized, e.UNAUTHORIZED, nil)
		return
	}
	token, err := util.GenerateToken(authService.ID, a.Email, a.Password)
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR_AUTH_TOKEN, nil)
		return
	}

	appG.Response(http.StatusOK, e.SUCCESS, map[string]string{
		"token": "Bearer " + token,
	})
}

// @Summary get all roles
// @Tags  auth
// @Accept json
// @Produce  json
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 400 {object} app.Response
// @Router /api/v1/allRoles  [GET]
func GetAllRoles(c *gin.Context) {
	appG := app.Gin{C: c}
	roleService := roleservice.Role{}
	roles, err := roleService.GetAll()
	if err != nil {
		appG.Response(http.StatusInternalServerError, e.ERROR, nil)
		return
	}

	appG.Response(http.StatusOK, e.SUCCESS, roles)
}

// @Summary   Get user info
// @Tags  auth
// @Accept json
// @Produce  json
// @Security ApiKeyAuth
// @Success 200 {object} app.Response
// @Failure 401 {object} app.Response
// @Router /api/v1/userInfo  [GET]
func GetUserInfo(c *gin.Context) {
	appG := app.Gin{C: c}
	user, _ := c.Get(e.JWT_AUTHED_USER_KEY)
	appG.Response(http.StatusOK, e.SUCCESS, user)
}
