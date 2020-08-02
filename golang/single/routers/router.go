package routers

import (
	"single/middleware/cors"
	"single/middleware/jwt"
	"single/middleware/rbac"
	"single/pkg/setting"
	"single/routers/api"
	v1 "single/routers/api/v1"

	_ "single/docs"

	"github.com/swaggo/gin-swagger/swaggerFiles"

	"github.com/gin-gonic/gin"
	ginSwagger "github.com/swaggo/gin-swagger"
)

// InitRouter initialize routing information
func InitRouter() *gin.Engine {
	r := gin.New()

	r.Use(gin.Logger())   // logger
	r.Use(gin.Recovery()) // recovery
	r.Use(cors.Cors())    // cors
	gin.SetMode(setting.ServerSetting.RunMode)

	r.POST("/api/auth", api.Auth)
	r.GET("/swagger/*any", ginSwagger.WrapHandler(swaggerFiles.Handler)) // swagger

	apiV1 := r.Group("/api/v1")
	apiV1.Use(jwt.JWT()) // jwt
	apiV1.GET("/allRoles", api.GetAllRoles)
	apiV1.GET("/userInfo", api.GetUserInfo)
	apiV1.Use(rbac.RBAC()) // rbac

	{
		apiV1.GET("/users", v1.GetUsers)
		apiV1.POST("/users", v1.AddUser)
		apiV1.PUT("/users/:id", v1.EditUser)
		apiV1.DELETE("/users/:id", v1.DeleteUser)

		apiV1.GET("/roles", v1.GetRoles)
		apiV1.POST("/roles", v1.AddRole)
		apiV1.PUT("/roles/:id", v1.EditRole)
		apiV1.DELETE("/roles/:id", v1.DeleteRole)

		apiV1.GET("/permissions/tree", v1.PermissionTree)
		apiV1.GET("/permissions", v1.GetPermissions)
		apiV1.POST("/permissions", v1.AddPermission)
		apiV1.PUT("/permissions/:id", v1.EditPermission)
		apiV1.DELETE("/permissions/:id", v1.DeletePermission)
	}
	return r
}
