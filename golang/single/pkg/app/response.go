package app

import (
	"single/pkg/e"

	"github.com/gin-gonic/gin"
)

type Gin struct {
	C *gin.Context
}

type Response struct {
	Code int         `json:"code"`
	Msg  string      `json:"msg"`
	Data interface{} `json:"data"`
}

// Response setting gin.JSON
func (g *Gin) Response(httpCode, code int, data interface{}) {
	rsp := &Response{httpCode, e.GetMsg(code), data}
	g.C.JSON(httpCode, rsp)
}
