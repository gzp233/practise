package parser

import (
	"crawler/engine"
	"crawler/model"
	"regexp"
	"strconv"
)

var (
	ageRe        = regexp.MustCompile(`<td><span class="label">年龄：</span>([\d]+)岁</td>`)
	marriageRe   = regexp.MustCompile(`<td><span class="label">婚况：</span>([^<]+)</td>`)
	occupationRe = regexp.MustCompile(`<td><span class="label">职业： </span>([^<]+)</td>`)
	genderRe     = regexp.MustCompile(`<td><span class="label">性别：</span><span field="">([^<]+)</span></td>`)
	heightRe     = regexp.MustCompile(`<td><span class="label">身高：</span>([\d]+)CM</td>`)
	incomeRe     = regexp.MustCompile(`<td><span class="label">月收入：</span>([^<]+)</td>`)
	weightRe     = regexp.MustCompile(`<td><span class="label">体重：</span><span field="">([\d]+)KG</span></td>`)
	educationRe  = regexp.MustCompile(`<td><span class="label">学历：</span>([^<]+)</td>`)
	houseRe      = regexp.MustCompile(`<td><span class="label">住房条件：</span><span field="">([^<]+)</span></td>`)
	carRe        = regexp.MustCompile(`<td><span class="label">是否购车：</span><span field="">([^<]+)</span></td>`)
	hukouRe      = regexp.MustCompile(`<td><span class="label">籍贯：</span>([^<]+)</td>`)
	xinzuoRe     = regexp.MustCompile(`<td><span class="label">星座：</span>([^<]+)</td>`)
	idUrlRe      = regexp.MustCompile(`http://album.zhenai.com/u/([\d]+)`)
	guessRe      = regexp.MustCompile(`<a class="exp-user-name"[^>]*href="(http://album.zhenai.com/u/[\d]+)">([^<]+)</a>`)
)

func ParseProfile(contents []byte, url string, name string) engine.ParseResult {
	profile := model.Profile{}

	if age, err := strconv.Atoi(extractString(contents, ageRe)); err == nil {
		profile.Age = age
	}
	if height, err := strconv.Atoi(extractString(contents, heightRe)); err == nil {
		profile.Height = height
	}
	if weight, err := strconv.Atoi(extractString(contents, weightRe)); err == nil {
		profile.Weight = weight
	}

	profile.Marriage = extractString(contents, marriageRe)
	profile.Gender = extractString(contents, genderRe)
	profile.Xinzuo = extractString(contents, xinzuoRe)
	profile.Car = extractString(contents, carRe)
	profile.Education = extractString(contents, educationRe)
	profile.Income = extractString(contents, incomeRe)
	profile.Hukou = extractString(contents, hukouRe)
	profile.House = extractString(contents, houseRe)
	profile.Occupation = extractString(contents, occupationRe)
	profile.Name = name

	result := engine.ParseResult{
		Items: []engine.Item{
			{
				Url:     url,
				Type:    "zhenai",
				Id:      extractString([]byte(url), idUrlRe),
				Payload: profile,
			},
		},
	}
	matches := guessRe.FindAllSubmatch(contents, -1)
	for _, m := range matches {
		result.Requests = append(result.Requests, engine.Request{
			Url:        string(m[1]),
			ParserFunc: ProfileParser(string(m[2])),
		})
	}

	return result

}

func extractString(contents []byte, re *regexp.Regexp) string {
	match := re.FindSubmatch(contents)
	if len(match) >= 2 {
		return string(match[1])
	} else {
		return ""
	}
}

func ProfileParser(name string) engine.ParserFunc {
	return func(c []byte, url string) engine.ParseResult {
		return ParseProfile(c, url, name)
	}
}
