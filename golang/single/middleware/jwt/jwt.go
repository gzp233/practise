package jwt

import (
	"net/http"
	"single/pkg/app"
	"single/pkg/e"
	"single/pkg/util"
	"single/services/userservice"
	"strings"

	"github.com/dgrijalva/jwt-go"
	"github.com/gin-gonic/gin"
)

// JWT is jwt middleware
func JWT() gin.HandlerFunc {
	return func(c *gin.Context) {
		var code int
		var data interface{}
		appG := app.Gin{C: c}

		code = e.SUCCESS
		Authorization := c.GetHeader("Authorization")
		token := strings.Split(Authorization, " ")
		if len(token) != 2 {
			appG.Response(http.StatusUnauthorized, code, data)
			c.Abort()
			return
		}

		if Authorization == "" {
			code = e.ERROR_AUTH
		} else {
			claim, err := util.ParseToken(token[1])
			if err != nil {
				switch err.(*jwt.ValidationError).Errors {
				case jwt.ValidationErrorExpired:
					code = e.ERROR_AUTH_CHECK_TOKEN_TIMEOUT
				default:
					code = e.ERROR_AUTH_CHECK_TOKEN_FAIL
				}
			} else {
				// set user to context
				userService := userservice.User{ID: claim.Id}
				if user, err := userService.GetByID(); err != nil {
					code = e.ERROR_AUTH
				} else {
					c.Set(e.JWT_AUTHED_USER_KEY, user)
				}
			}
		}

		if code != e.SUCCESS {
			appG.Response(http.StatusUnauthorized, code, data)
			c.Abort()
			return
		}

		c.Next()
	}
}
