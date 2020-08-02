package dbops

import (
	"database/sql"
	"log"
	"strconv"
	"sync"
	"video_server/api/defs"
)

func InsertSession(sid string, ttl int64, uname string) error {
	ttlstr := strconv.FormatInt(ttl, 10)
	stmtIns, err := dbConn.Prepare("insert into sessions(session_id, ttl, login_name) values(?,?,?)")
	if err != nil {
		return err
	}
	defer stmtIns.Close()
	_, err = stmtIns.Exec(sid, ttlstr, uname)
	if err != nil {
		return err
	}

	return nil
}

func RetrieveSession(sid string) (*defs.SimpleSession, error) {
	ss := &defs.SimpleSession{}
	stmtOut, err := dbConn.Prepare("select ttl, login_name from sessions where session_id=?")
	if err != nil {
		return nil, err
	}
	defer stmtOut.Close()

	var ttl, uname string
	err = stmtOut.QueryRow(sid).Scan(&ttl, &uname)
	if err != nil && err != sql.ErrNoRows {
		return nil, err
	}

	if ttlint, err := strconv.ParseInt(ttl, 10, 64); err == nil {
		ss.TTL = ttlint
		ss.Username = uname
	} else {
		return nil, err
	}

	return ss, nil
}

func RetrieveAllSessions() (*sync.Map, error) {
	m := &sync.Map{}
	stmtOut, err := dbConn.Prepare("select * from sessions")
	if err != nil {
		log.Printf("%s", err)
		return nil, err
	}
	defer stmtOut.Close()

	rows, err := stmtOut.Query()
	for rows.Next() {
		var id, ttlstr, login_name string
		if err = rows.Scan(&id, &ttlstr, &login_name); err != nil {
			log.Printf("retrieve sessions error:%s", err)
			break
		}

		if ttl, err := strconv.ParseInt(ttlstr, 10, 64); err == nil {
			ss := &defs.SimpleSession{Username: login_name, TTL: ttl}
			m.Store(id, ss)
			log.Printf("session id:%s, ttl: %d", id, ss.TTL)
		}
	}

	return m, nil
}

func DeleteSession(sid string) error {
	stmtOut, err := dbConn.Prepare("delete from sessions where session_id=?")
	if err != nil {
		log.Printf("%s", err)
		return err
	}
	defer stmtOut.Close()

	if _, err = stmtOut.Query(sid); err != nil {
		return err
	}

	return nil
}
