package dbops

import (
	"database/sql"
	"log"
	"time"
	"video_server/api/defs"
	"video_server/api/utils"
)

func AddUserCredential(loginName string, pwd string) error {
	stmtIns, err := dbConn.Prepare("insert into users(login_name, pwd) values(?, ?)")
	if err != nil {
		return err
	}
	defer stmtIns.Close()
	_, err = stmtIns.Exec(loginName, pwd)
	if err != nil {
		return err
	}

	return nil
}

func GetUserCredential(loginName string) (string, error) {
	stmtOut, err := dbConn.Prepare("select pwd from users where login_name=?")
	if err != nil {
		log.Printf("%s", err)
		return "", err
	}
	defer stmtOut.Close()

	var pwd string
	err = stmtOut.QueryRow(loginName).Scan(&pwd)
	if err != nil && err != sql.ErrNoRows {
		return "", err
	}

	return pwd, nil
}

func DeleteUser(loginName string, pwd string) error {
	stmtDel, err := dbConn.Prepare("delete from users where login_name=? and pwd=?")
	if err != nil {
		log.Printf("%s", err)
		return err
	}
	defer stmtDel.Close()

	_, err = stmtDel.Exec(loginName, pwd)
	if err != nil {
		return err
	}

	return nil
}

func AddNewVideo(aid int, name string) (*defs.VideoInfo, error) {
	// create uuid
	vid, err := utils.NewUUID()
	if err != nil {
		return nil, err
	}

	t := time.Now()
	ctime := t.Format("2006年01月02日 15时04分05秒")
	stmtIns, err := dbConn.Prepare(`insert into video_info(id, author_id, name, display_ctime)
		values(?,?,?,?)`)
	if err != nil {
		return nil, err
	}
	defer stmtIns.Close()

	_, err = stmtIns.Exec(vid, aid, name, ctime)
	if err != nil {
		return nil, err
	}

	res := &defs.VideoInfo{Id: vid, AuthorId: aid, Name: name, DisplayCtime: ctime}

	return res, nil
}

func GetVideoInfo(vid string) (*defs.VideoInfo, error) {
	stmtOut, err := dbConn.Prepare("select author_id, name, display_ctime from video_info where id=?")
	if err != nil {
		return nil, err
	}
	defer stmtOut.Close()
	var aid int
	var dct string
	var name string

	err = stmtOut.QueryRow(vid).Scan(&aid, &name, &dct)
	if err != nil && err != sql.ErrNoRows {
		return nil, err
	}
	if err == sql.ErrNoRows {
		return nil, nil
	}

	res := &defs.VideoInfo{Id: vid, AuthorId: aid, Name: name, DisplayCtime: dct}

	return res, nil
}

func DeleteVideoInfo(vid string) error {
	stmtIns, err := dbConn.Prepare("delete from video_info where id=?")
	if err != nil {
		return err
	}
	defer stmtIns.Close()
	_, err = stmtIns.Exec(vid)
	if err != nil {
		return err
	}

	return nil
}

func AddNewComments(vid string, aid int, content string) error {
	id, err := utils.NewUUID()
	if err != nil {
		return err
	}

	stmtIns, err := dbConn.Prepare("insert into comments(id, video_id, author_id, content) values(?,?,?,?)")
	if err != nil {
		return err
	}
	defer stmtIns.Close()

	_, err = stmtIns.Exec(id, vid, aid, content)
	if err != nil {
		return err
	}

	return nil
}

func ListComments(vid string, from, to int) ([]*defs.Comment, error) {
	stmtOut, err := dbConn.Prepare(`select c.id, u.login_name, c.content from comments c inner join 
		users u on c.author_id=u.id where c.video_id=? and c.time > from_unixtime(?) and c.time <=from_unixtime(?)`)
	if err != nil {
		return nil, err
	}
	defer stmtOut.Close()
	var comments []*defs.Comment
	rows, err := stmtOut.Query(vid, from, to)
	if err != nil {
		return comments, err
	}
	for rows.Next() {
		var id, name, comment string
		if err := rows.Scan(&id, &name, &comment); err != nil {
			return comments, err
		}
		c := &defs.Comment{Id: id, VideoId: vid, Author: name, Content: comment}
		comments = append(comments, c)
	}

	return comments, nil
}
