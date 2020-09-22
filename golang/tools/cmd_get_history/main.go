package main

import (
	"fmt"
	"io"
	"log"
	"os"
	"os/exec"
	"runtime"
	"sync"
	"time"
)

func GetBetweenDates(sdate, edate string) []string {
	var d []string
	timeFormatTpl := "20060102"
	if len(timeFormatTpl) != len(sdate) {
		timeFormatTpl = timeFormatTpl[0:len(sdate)]
	}
	date, err := time.Parse(timeFormatTpl, sdate)
	if err != nil {
		// 时间解析，异常
		return d
	}
	date2, err := time.Parse(timeFormatTpl, edate)
	if err != nil {
		// 时间解析，异常
		return d
	}
	if date2.Before(date) {
		// 如果结束时间小于开始时间，异常
		return d
	}
	// 输出日期格式固定
	timeFormatTpl = "20060102"
	date2Str := date2.Format(timeFormatTpl)
	d = append(d, date.Format(timeFormatTpl))
	for {
		date = date.AddDate(0, 0, 1)
		dateStr := date.Format(timeFormatTpl)
		d = append(d, dateStr)
		if dateStr == date2Str {
			break
		}
	}
	return d
}

// 执行命令
func getReport(date string, w *sync.WaitGroup) {
	defer w.Done()
	fmt.Printf("正在拉取 %s 的报告...\n", date)
	//c := exec.Command("php", "d:\\code\\work\\ads-app\\artisan", "get:history", date)
	c := exec.Command("/usr/bin/php", "/www/wwwroot/ads-app/artisan", "get:history", date)
	d, err := c.CombinedOutput()
	fmt.Println(string(d))
	if err != nil {
		fmt.Println(date+" Error:", err)
	}
	<-limiter
	l.Println(date + " " + string(d))
}

var l *log.Logger
var limiter chan struct{}

func main() {
	runtime.GOMAXPROCS(runtime.NumCPU())
	dates := GetBetweenDates("20200913", "20200916")
	f, err := os.OpenFile("get_history.log", os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0666)
	if err != nil {
		fmt.Println("日志文件打开错误...")
		os.Exit(1)
	}
	defer f.Close()
	l = log.New(io.Writer(f), "INFO: ", log.Ldate|log.Ltime|log.Lshortfile)
	w := sync.WaitGroup{}
	// 限制一下goroutine数量
	limiter = make(chan struct{}, 4)

	fmt.Println("开始拉取报告...")
	startTime := time.Now()
	for _, date := range dates {
		w.Add(1)
		limiter <- struct{}{}
		go getReport(date, &w)
	}
	w.Wait()
	fmt.Printf("拉取完成，耗时%s秒", time.Now().Sub(startTime).String())
}
