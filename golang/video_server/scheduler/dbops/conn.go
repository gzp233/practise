package dbops

import (
	"database/sql"

	_ "github.com/go-sql-driver/mysql"
)

var (
	dbConn *sql.DB
	err    error
)

func init() {
	dbConn, err = sql.Open("mysql", "video:gzp111@tcp(120.55.58.162:3306)/video?charset=utf8")
	if err != nil {
		panic(err.Error())
	}
}
