package main

import (
	"encoding/csv"
	"errors"
	"flag"
	"fmt"
	"io"
	"os"
	"sort"
	"strconv"
	"strings"
	"time"
)

// 定义参数
var sourcePath, sortColumn string
var h bool

func init() {
	flag.BoolVar(&h, "h", false, "帮助")

	flag.StringVar(&sourcePath, "f", "", "请输入文件路径")
	flag.StringVar(&sortColumn, "c", "", "请输入排序字段")

	flag.Usage = usage
}

// 打印使用说明
func usage() {
	fmt.Fprintf(os.Stderr, `Usage: sort -f fileName -c sortColumn`)
	flag.PrintDefaults()
}

// 判断文件是否存在
func checkFileExist(filename string) bool {
	if _, err := os.Stat(filename); os.IsNotExist(err) {
		return false
	}
	return true
}

var errs []string
var errLines []string

// 检查错误
func check(err error) {
	if errType, ok := err.(*csv.ParseError); ok {
		if errType.Err == csv.ErrFieldCount || errType.Err == csv.ErrQuote {
			errs = append(errs, err.Error())
			return
		}
	}

	if err != nil && err != io.EOF {
		fmt.Printf("发生错误：%s\n", err.Error())
		os.Exit(1)
	}
}

// 获取字段所在下标
func getColumnIndex(line []string) int {
	index := -1
	for k, column := range line {
		if column == sortColumn {
			index = k
			break
		}
	}

	return index
}

var tmpIndex = 0
var tmpFiles []string
var tmpHandler []*os.File
var tmpReaders []*csv.Reader
var tmpValues [][]string

// 对数据排序并写入到文件中,从小到大排序
func sortAndSave(lines [][]string, index int) {
	sort.Slice(lines, func(i, j int) bool {
		return lines[i][index] < lines[j][index]
	})
	tmpIndex++
	tmpPath := "result" + strconv.Itoa(tmpIndex) + ".csv"
	tmpFiles = append(tmpFiles, tmpPath)
	handler, err := os.OpenFile(tmpPath, os.O_RDWR|os.O_CREATE, 0666)
	check(err)
	writer := csv.NewWriter(handler)
	for _, v := range lines {
		err = writer.Write(v)
		check(err)
	}
	handler.Seek(0, io.SeekStart)
	tmpHandler = append(tmpHandler, handler)
}

// 清除数组指定元素
func clear(k int) {
	tmpHandler[k].Close()
	err := os.Remove(tmpFiles[k])
	check(err)
	if k == len(tmpValues)-1 {
		tmpValues = tmpValues[:k]
		tmpReaders = tmpReaders[:k]
		tmpHandler = tmpHandler[:k]
		tmpFiles = tmpFiles[:k]
		return
	}
	tmpValues = append(tmpValues[:k], tmpValues[k+1:]...)
	tmpReaders = append(tmpReaders[:k], tmpReaders[k+1:]...)
	tmpHandler = append(tmpHandler[:k], tmpHandler[k+1:]...)
	tmpFiles = append(tmpFiles[:k], tmpFiles[k+1:]...)
}

func main() {
	flag.Parse()
	if h || sourcePath == "" || sortColumn == "" {
		flag.Usage()
		os.Exit(1)
	}

	if !checkFileExist(sourcePath) {
		check(errors.New("文件不存在"))
	}

	f, err := os.Open(sourcePath)
	check(err)
	defer f.Close()

	r := csv.NewReader(f)
	line, err := r.Read()
	check(err)
	header := append([]string{"sort"}, line...)
	index := getColumnIndex(line)
	if index == -1 {
		check(errors.New("排序的字段不存在文件中"))
	}
	fmt.Println("开始排序...")
	start := time.Now()

	// 开始切割文件
	var tmpLines [][]string
	var count int
	for {
		count++
		line, err = r.Read()
		check(err)
		if err == io.EOF {
			break
		}
		tmpLines = append(tmpLines, line)
		if count == 50000 {
			sortAndSave(tmpLines, index)
			count = 0
			tmpLines = [][]string{}
		}
	}
	sortAndSave(tmpLines, index)

	// 重新排序
	resFile, err := os.OpenFile("result.csv", os.O_CREATE|os.O_WRONLY, 0666)
	check(err)
	defer resFile.Close()
	resWriter := csv.NewWriter(resFile)
	err = resWriter.Write(header)
	check(err)
	// 初始化
	for k, v := range tmpHandler {
		tmpReaders = append(tmpReaders, csv.NewReader(v))
		line, err = tmpReaders[k].Read()
		check(err)
		tmpValues = append(tmpValues, line)
	}

	sortCount := 0
	for len(tmpValues) > 0 {
		i := -1
		for k, v := range tmpValues {
			if i == -1 {
				i = k
				continue
			}
			if v[index] < tmpValues[i][index] {
				i = k
			}
		}
		// 写入文件
		sortCount++
		sortCountStr := strconv.Itoa(sortCount)
		err = resWriter.Write(append([]string{sortCountStr}, tmpValues[i]...))
		check(err)
		for {
			line, err = tmpReaders[i].Read()
			check(err)
			if err == io.EOF {
				clear(i)
				break
			}
			if len(line) < index+1 {
				errLines = append(errLines, strings.Join(line, "|"))
				continue
			}
			tmpValues[i] = line
			break
		}

	}

	// 报错
	for _, v := range errs {
		fmt.Println("错误：" + v)
	}

	for _, v := range errLines {
		fmt.Println("行：" + v)
	}
	fmt.Println("结束...耗时：" + (time.Now().Sub(start)).String() + "秒")
}
