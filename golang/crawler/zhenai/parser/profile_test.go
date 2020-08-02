package parser

import (
	"crawler/engine"
	"crawler/model"
	"io/ioutil"
	"testing"
)

func TestParseProfile(t *testing.T) {
	contents, err := ioutil.ReadFile("profile_test_data.html")

	if err != nil {
		panic(err)
	}

	result := ParseProfile(contents, "http://album.zhenai.com/u/1289258517", "静候美好")

	if len(result.Items) != 1 {
		t.Errorf("Items should contain 1 "+"element;but was %v", result.Items)
	}

	actual := result.Items[0]

	expected := engine.Item{
		Url:  "http://album.zhenai.com/u/1289258517",
		Type: "zhenai",
		Id:   "1289258517",
		Payload: model.Profile{
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

	if actual != expected {
		t.Errorf("expected %v; but was $v", expected, actual)
	}

}
