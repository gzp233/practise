package app

import (
	"reflect"
	"single/pkg/e"
	"strings"

	"github.com/gin-gonic/gin"

	"github.com/astaxie/beego/validation"
)

// Set message to chinese
func setVerifyMessage() {
	var MessageTmpls = map[string]string{
		"Required":     "不能为空",
		"Min":          "最小值为%d",
		"Max":          "最大值为%d",
		"Range":        "范围为%d到%d",
		"MinSize":      "最短长度为%d",
		"MaxSize":      "最大长度为%d",
		"Length":       "长度必须为%d",
		"Alpha":        "必须是有效的字母",
		"Numeric":      "必须是有效的数字",
		"AlphaNumeric": "必须是有效的字母或数字",
		"Match":        "必须匹配%s",
		"NoMatch":      "必须不匹配%s",
		"AlphaDash":    "必须是有效的字母、数字或连接符号(-_)",
		"Email":        "必须是有效的电子邮件地址",
		"IP":           "必须是有效的IP地址",
		"Base64":       "必须是有效的base64字符",
		"Mobile":       "必须是有效的手机号码",
		"Tel":          "必须是有效的电话号码",
		"Phone":        "必须是有效的电话或移动电话号码",
		"ZipCode":      "必须是有效的邮政编码",
	}

	validation.SetDefaultMessage(MessageTmpls)
}

// BindAndValid binds and validates data
func BindAndValid(c *gin.Context, input interface{}) (bool, string) {
	setVerifyMessage()
	if err := c.BindJSON(&input); err != nil {
		return false, e.GetMsg(e.INVALID_PARAMS)
	}
	valid := validation.Validation{}
	ok, err := valid.Valid(input)
	if err != nil {
		return false, err.Error()
	}
	if !ok {
		arr := strings.Split(valid.Errors[0].Key, ".")
		st := reflect.TypeOf(input).Elem()
		field, _ := st.FieldByName(arr[0])
		name := arr[0]
		if field.Tag.Get("chn") != "" {
			name = field.Tag.Get("chn")
		}
		return false, name + valid.Errors[0].Message
	}

	return true, ""
}
