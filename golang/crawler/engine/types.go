package engine

type Request struct {
	Url        string
	ParserFunc ParserFunc
}

type ParseResult struct {
	Requests []Request
	Items    []Item
}

type Item struct {
	Url     string
	Id      string
	Type    string
	Payload interface{}
}

type ParserFunc func(contents []byte, url string) ParseResult