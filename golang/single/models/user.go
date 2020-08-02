package models

import "github.com/jinzhu/gorm"

type User struct {
	Model
	Username   string  `json:"username"`
	Email      string  `json:"email"`
	Password   string  `json:"-"`
	Avatar     string  `json:"avatar"`
	SocialId   string  `json:"social_id"`
	SocialType string  `json:"social_type"`
	Status     int     `json:"status"`
	Roles      []*Role `json:"roles" gorm:"many2many:user_role;"`
}

const (
	STATUS_DISABLED = iota
	STATUS_ENABLED
)

// get user by email
func GetUserByEmail(email string) (*User, error) {
	user := &User{}
	if err := db.Where("email = ? AND deleted_at is null ", email).First(user).Error; err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return user, nil
}

// get user by id
func GetUserById(id int) (*User, error) {
	user := &User{}
	if err := db.Preload("Roles").Where("id = ? AND deleted_at is null ", id).First(user).Error; err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return user, nil
}

func GetAllUsers() ([]*User, error) {
	var users []*User
	err := db.Preload("Roles").Where("deleted_at is null").Find(&users).Error
	if err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return users, nil
}

// get total user
func GetUserTotal(maps interface{}) (int, error) {
	var count int
	if err := db.Model(&User{}).Where(maps).Count(&count).Error; err != nil {
		return 0, err
	}

	return count, nil
}

// get a list of users based on paging and contains
func GetUsers(pageNum int, pageSize int, maps interface{}) ([]*User, error) {
	var users []*User
	err := db.Preload("Roles").Where(maps).Offset(pageNum).Limit(pageSize).Find(&users).Error
	if err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return users, nil
}

// add a user
func AddUser(data map[string]interface{}) (id int, err error) {
	user := User{
		Email:      data["email"].(string),
		Username:   data["username"].(string),
		Password:   data["password"].(string),
		Avatar:     data["avatar"].(string),
		SocialId:   data["social_id"].(string),
		SocialType: data["social_type"].(string),
		Status:     1,
	}
	var roles []*Role
	if err := db.Where("id in (?)", data["role_ids"]).Find(&roles).Error; err != nil {
		return 0, nil
	}
	if err := db.Create(&user).Association("Roles").Append(roles).Error; err != nil {
		return 0, err
	}
	return user.ID, nil
}

// delete a user
func DeleteUser(id int) error {
	var user User
	if err := db.First(&user, id).Error; err != nil {
		return err
	}
	if err := db.Model(&user).Association("Roles").Delete().Error; err != nil {
		return err
	}
	return db.Where("id = ?", id).Delete(&user).Error
}

// edit a user
func EditUser(id int, data map[string]interface{}) error {
	var (
		roles []*Role
		user  User
		err   error
	)
	if err = db.Where("id in (?)", data["role_ids"]).Find(&roles).Error; err != nil {
		return err
	}
	if err = db.Where("id = ? AND deleted_at is null ", id).Find(&user).Error; err != nil {
		return err
	}
	if err = db.Model(&user).Association("Roles").Replace(roles).Error; err != nil {
		return err
	}
	if err = db.Model(&user).Update(data).Error; err != nil {
		return err
	}

	return nil
}
