package jwt

import (
	"ginb/pkg/e"
	"ginb/pkg/util"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
)

// JWT is jwt middleware
func JWT() gin.HandlerFunc {
	return func(c *gin.Context) {
		var (
			code int
			data interface{}
		)

		code = e.SUCCESS
		token := c.Query("token")
		if token == "" {
			code = e.INVALID_PARAMS
		} else {
			if claims, err := util.ParseToken(token); err != nil {
				code = e.ERROR_AUTH_CHECK_TOKEN_FAIL
			} else if time.Now().Unix() > claims.ExpiresAt {
				code = e.ERROR_AUTH_CHECK_TOKEN_TIMEOUT
			}
		}

		if code != e.SUCCESS {
			c.JSON(http.StatusUnauthorized, gin.H{
				"code": code,
				"msg":  e.GetMsg(code),
				"data": data,
			})
			c.Abort()
			return
		}
		c.Next()
	}
}
