package rbac

import (
	"net/http"
	"single/models"
	"single/pkg/app"
	"single/pkg/e"
	"single/pkg/rbac"

	"github.com/gin-gonic/gin"
)

func RBAC() gin.HandlerFunc {
	return func(c *gin.Context) {
		var user *models.User
		appG := app.Gin{C: c}
		u, _ := c.Get(e.JWT_AUTHED_USER_KEY)
		user = u.(*models.User)
		if b, err := rbac.RbacDefault.Enforcer.Enforce(user.Email, c.Request.URL.Path, c.Request.Method); err != nil {
			appG.Response(http.StatusInternalServerError, e.ERROR, nil)
			c.Abort()
			return
		} else if !b {
			appG.Response(http.StatusForbidden, e.FORBIDDEN, nil)
			c.Abort()
			return
		}

		c.Next()
	}
}
