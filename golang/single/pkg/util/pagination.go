package util

import (
	"single/pkg/setting"

	"github.com/gin-gonic/gin"
	"github.com/unknwon/com"
)

// GetPage get page parameters
func GetPage(c *gin.Context) int {
	result := 0
	if page, _ := com.StrTo(c.Query("page")).Int(); page > 0 {
		result = (page - 1) * setting.AppSetting.PageSize
	}

	return result
}
