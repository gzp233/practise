package view

import (
	"crawler/engine"
	"crawler/frontend/model"
	common "crawler/model"
	"os"
	"testing"
)

func TestSearchResultView_Render(t *testing.T) {
	view := CreateSearchResultView("template.html")

	out, err := os.Create("template.test.html")
	page := model.SearchResult{}
	page.Hits = 123
	item := engine.Item{
		Url:  "http://album.zhenai.com/u/1289258517",
		Type: "zhenai",
		Id:   "1289258517",
		Payload: common.Profile{
			Age:        26,
			Height:     162,
			Weight:     42,
			Income:     "3000元以下",
			Gender:     "女",
			Name:       "静候美好",
			Marriage:   "未婚",
			Education:  "高中及以下",
			Hukou:      "四川阿坝",
			Xinzuo:     "天蝎座",
			House:      "",
			Car:        "未购车",
			Occupation: "经销商",
		},
	}

	for i := 0; i < 10; i++ {
		page.Items = append(page.Items, item)
	}
	err = view.Render(out, page)
	if err != nil {
		panic(err)
	}
}
