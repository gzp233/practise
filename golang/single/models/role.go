package models

import "github.com/jinzhu/gorm"

type Role struct {
	Model
	Name        string        `json:"name"`
	Permissions []*Permission `json:"permissions" gorm:"many2many:role_permission;"`
}

func GetRoleByID(id int) (*Role, error) {
	role := &Role{}
	if err := db.Preload("Permissions").Where("id = ? AND deleted_at is null", id).Find(role).Error; err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return role, nil
}

func GetRoleByName(name string) (*Role, error) {
	role := &Role{}
	if err := db.Where("name = ? AND deleted_at is null", name).Find(role).Error; err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return role, nil
}

func GetAllRoles() ([]*Role, error) {
	var roles []*Role
	err := db.Preload("Permissions").Where("deleted_at is null").Find(&roles).Error
	if err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return roles, nil
}

func GetRoleTotal(maps interface{}) (int, error) {
	var count int
	if err := db.Model(&Role{}).Where(maps).Count(&count).Error; err != nil {
		return 0, err
	}

	return count, nil
}

func GetRoles(pageNum, pageSize int, maps interface{}) ([]*Role, error) {
	var roles []*Role
	err := db.Preload("Permissions").Where(maps).Offset(pageNum).Limit(pageSize).Find(&roles).Error
	if err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return roles, nil
}

func AddRole(data map[string]interface{}) (id int, err error) {
	role := Role{
		Name: data["name"].(string),
	}
	var permissions []*Permission
	if err = db.Where("id in (?)", data["permission_ids"]).Find(&permissions).Error; err != nil {
		return 0, err
	}
	if err = db.Create(&role).Association("Permissions").Append(permissions).Error; err != nil {
		return 0, err
	}

	return role.ID, nil
}

func EditRole(id int, data map[string]interface{}) error {
	var (
		role        Role
		permissions []*Permission
		err         error
	)
	if err = db.First(&role, id).Error; err != nil {
		return err
	}
	if err = db.Where("id in (?)", data["permission_ids"]).Find(&permissions).Error; err != nil {
		return err
	}
	if err = db.Model(&role).Association("Permissions").Replace(permissions).Error; err != nil {
		return err
	}
	if err = db.Model(&role).Update(data).Error; err != nil {
		return err
	}

	return nil
}

func DeleteRole(id int) error {
	var role Role
	if err := db.First(&role, id).Error; err != nil {
		return err
	}
	if err := db.Model(&role).Association("Permissions").Delete().Error; err != nil {
		return err
	}
	if err := db.Where("id = ? AND deleted_at is null", id).Delete(Role{}).Error; err != nil {
		return err
	}

	return nil
}
