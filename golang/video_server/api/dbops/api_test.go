package dbops

import (
	"fmt"
	"strconv"
	"testing"
	"time"
)

func clearTables() {
	dbConn.Exec("truncate users")
	dbConn.Exec("truncate video_info")
	dbConn.Exec("truncate comments")
	dbConn.Exec("truncate sessions")
}

func TestMain(m *testing.M) {
	clearTables()
	m.Run()
	clearTables()
}

func TestUserWorkFlow(t *testing.T) {
	t.Run("Add", SubTestAddUser)
	t.Run("Get", SubTestGetUser)
	t.Run("Delete", SubTestDeleteUser)
	t.Run("Reget", SubTestRegetUser)
}

func SubTestAddUser(t *testing.T) {
	err := AddUserCredential("test_user", "123")
	if err != nil {
		t.Errorf("Error of AddUser:%v", err)
	}
}

func SubTestGetUser(t *testing.T) {
	pwd, err := GetUserCredential("test_user")
	if pwd != "123" || err != nil {
		t.Errorf("Error of GetUser")
	}
}

func SubTestDeleteUser(t *testing.T) {
	err := DeleteUser("test_user", "123")
	if err != nil {
		t.Errorf("Error of DeleteUser:%v", err)
	}
}

func SubTestRegetUser(t *testing.T) {
	pwd, err := GetUserCredential("test_user")
	if err != nil {
		t.Errorf("Error of RegetUser:%v", err)
	}
	if pwd != "" {
		t.Errorf("Deleting user failed")
	}
}

var tempvid string

func TestVideoWorkFlow(t *testing.T) {
	clearTables()
	t.Run("PrepareUser", SubTestAddUser)
	t.Run("AddVideo", SubTestAddVideo)
	t.Run("GetVideo", SubTestGetVideo)
	t.Run("DeleteVideo", SubTestDeleteVideo)
	t.Run("RegetVideo", SubTestRegetVideo)
}

func SubTestAddVideo(t *testing.T) {
	vi, err := AddNewVideo(1, "my-video")
	if err != nil {
		t.Errorf("Error of AddVideo:%v", err)
	}

	tempvid = vi.Id
}

func SubTestGetVideo(t *testing.T) {
	_, err := GetVideoInfo(tempvid)
	if err != nil {
		t.Errorf("Error of GetVideo")
	}
}

func SubTestDeleteVideo(t *testing.T) {
	err := DeleteVideoInfo(tempvid)
	if err != nil {
		t.Errorf("Error of DeleteVideo:%v", err)
	}
}

func SubTestRegetVideo(t *testing.T) {
	vi, err := GetVideoInfo(tempvid)
	if err != nil || vi != nil {
		t.Errorf("Error of RegetVideo:%v", err)
	}
}

func TestComments(t *testing.T) {
	clearTables()
	t.Run("PrepareUser", SubTestAddUser)
	t.Run("AddComments", SubTestAddComments)
	t.Run("ListComments", SubTestListComments)
}

func SubTestAddComments(t *testing.T) {
	vid := "12345"
	aid := 1
	content := "i like this video"
	err := AddNewComments(vid, aid, content)
	if err != nil {
		t.Errorf("Error of AddComments:%v", err)
	}
}

func SubTestListComments(t *testing.T) {
	vid := "12345"
	from := 1514764444
	to, _ := strconv.Atoi(strconv.FormatInt(time.Now().UnixNano()/1000000000, 10))
	res, err := ListComments(vid, from, to)
	if err != nil {
		t.Errorf("Error of ListComments:%v", err)
	}
	for i, ele := range res {
		fmt.Printf("comments:%d, %v \n", i, ele)
	}
}
