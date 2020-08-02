package userservice

import (
	"single/models"
	"single/pkg/logging"

	"github.com/casbin/casbin/v2"

	"golang.org/x/crypto/bcrypt"
)

type User struct {
	ID         int
	Username   string
	Email      string
	Password   string
	Avatar     string
	SocialId   string
	SocialType string
	Status     int
	RoleIds    []int

	PageNum  int
	PageSize int

	Enforcer *casbin.SyncedEnforcer `inject:""`
}

func (u *User) ExistByEmail() (bool, error) {
	user, err := models.GetUserByEmail(u.Email)
	if err != nil {
		return true, err
	}
	if user.ID <= 0 || (u.ID > 0 && user.ID == u.ID) {
		return false, nil
	}

	return true, nil
}

func (u *User) ExistByID() (bool, error) {
	user, err := models.GetUserById(u.ID)
	if err != nil {
		return false, err
	}
	if user.ID <= 0 {
		return false, nil
	}

	return true, nil
}

func (u *User) GetByID() (*models.User, error) {
	return models.GetUserById(u.ID)
}

func (u *User) Count() (int, error) {
	return models.GetUserTotal(u.getQueryMaps())
}

func (u *User) GetList() ([]*models.User, error) {
	user, err := models.GetUsers(u.PageNum, u.PageSize, u.getQueryMaps())
	if err != nil {
		return nil, err
	}
	return user, nil
}

func (u *User) Add() (int, error) {
	data, err := u.getMapData()
	if err != nil {
		return 0, err
	}

	return models.AddUser(data)
}

func (u *User) Edit() error {
	data, err := u.getMapData()
	if err != nil {
		return err
	}

	return models.EditUser(u.ID, data)
}

func (u *User) Delete() error {
	return models.DeleteUser(u.ID)
}

func (u *User) getQueryMaps() map[string]interface{} {
	maps := make(map[string]interface{})
	maps["deleted_at"] = nil
	return maps
}

func (u *User) getMapData() (map[string]interface{}, error) {
	hashPwd, err := bcrypt.GenerateFromPassword([]byte(u.Password), bcrypt.DefaultCost)
	if err != nil {
		logging.Error("Fail to encrypt password fail, error:", err)
		return nil, err
	}

	data := map[string]interface{}{
		"username":    u.Username,
		"email":       u.Email,
		"password":    string(hashPwd),
		"avatar":      u.Avatar,
		"social_id":   u.SocialId,
		"social_type": u.SocialType,
		"role_ids":    u.RoleIds,
	}

	return data, nil
}

// LoadAllPolicy 加载所有的用户策略
func (u *User) LoadAllPolicy() error {
	users, err := models.GetAllUsers()
	if err != nil {
		return err
	}
	for _, user := range users {
		if len(user.Roles) != 0 {
			err = u.LoadPolicy(user.ID)
			if err != nil {
				return err
			}
		}
	}
	logging.Info("加载所有用户角色关系", u.Enforcer.GetGroupingPolicy())

	return nil
}

// LoadPolicy 加载用户权限策略
func (u *User) LoadPolicy(id int) error {

	user, err := models.GetUserById(id)
	if err != nil {
		return err
	}

	u.Enforcer.DeleteRolesForUser(user.Email)

	for _, role := range user.Roles {
		u.Enforcer.AddRoleForUser(user.Email, role.Name)
	}

	return nil
}
