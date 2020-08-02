package persist

import (
	"crawler/engine"
	"crawler/model"
	"encoding/json"
	"testing"

	"golang.org/x/net/context"

	"gopkg.in/olivere/elastic.v5"
)

func TestItemSave(t *testing.T) {
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

	const index = "dating_test"

	// TODO: try to start up elastic search
	// here using docker go client
	client, err := elastic.NewClient(elastic.SetSniff(false))
	if err != nil {
		panic(err)
	}

	err = save(client, expected, index)
	if err != nil {
		panic(err)
	}

	resp, err := client.Get().
		Index("dating_profile").Type(expected.Type).Id(expected.Id).Do(context.Background())

	if err != nil {
		panic(err)
	}

	var actual engine.Item
	err = json.Unmarshal(*resp.Source, &actual)

	if err != nil {
		panic(err)
	}

	actualProfile, err := model.FromJsonObj(actual.Payload)
	actual.Payload = actualProfile

	if actual != expected {
		t.Errorf("got %v;expected %v", actual, expected)
	}
}
