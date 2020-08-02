package main

import (
	"crawler/frontend/controller"
	"net/http"
)

func main() {
	http.Handle("/", http.FileServer(
		http.Dir("E:/personal/go/src/crawler/frontend/view")))
	http.Handle("/search",
		controller.CreateSearchResultHandler("E:/personal/go/src/crawler/frontend/view/template.html"))
	err := http.ListenAndServe(":8888", nil)
	if err != nil {
		panic(err)
	}
}
