package parser

import (
	"io/ioutil"
	"testing"
)

func TestParseCity(t *testing.T) {
	contents, err := ioutil.ReadFile("city_test_data.html")

	if err != nil {
		panic(err)
	}

	result := ParseCity(contents)

	const resultSize = 74
	expectedUrls := []string{
		"http://album.zhenai.com/u/1995815593",
		"http://album.zhenai.com/u/1314495053",
		"http://album.zhenai.com/u/1626200317",
	}

	if len(result.Requests) != resultSize {
		t.Errorf("result should have %d "+"requests;but had %d", resultSize, len(result.Requests))
	}

	for i, url := range expectedUrls {
		if result.Requests[i].Url != url {
			t.Errorf("expected url #%d: %s "+";but was %s", i, url, result.Requests[i].Url)
		}
	}

}
