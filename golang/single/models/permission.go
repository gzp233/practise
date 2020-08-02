package models

import "github.com/jinzhu/gorm"

type Permission struct {
	Model
	ParentId int           `json:"parent_id"`
	Name     string        `json:"name"`
	Type     string        `json:"type"`
	Path     string        `json:"path"`
	Method   string        `json:"method"`
	Roles    []*Role       `json:"roles" gorm:"many2many:role_permission;"`
	Children []*Permission `gorm:"-"`
}

func GetPermissionByID(id int) (*Permission, error) {
	permission := &Permission{}
	if err := db.Where("id = ? AND deleted_at is null", id).Find(permission).Error; err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return permission, nil
}

func GetPermissionTotal(maps interface{}) (int, error) {
	var count int
	if err := db.Model(&Permission{}).Where(maps).Count(&count).Error; err != nil {
		return 0, err
	}

	return count, nil
}

func GetAllPermissions() ([]*Permission, error) {
	var permissions []*Permission
	if err := db.Where("deleted_at is null").Find(&permissions).Error; err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return permissions, nil
}

func GetPermissions(pageNum, pageSize int, maps interface{}) ([]*Permission, error) {
	var permissions []*Permission
	err := db.Where(maps).Offset(pageNum).Limit(pageSize).Find(&permissions).Error
	if err != nil && err != gorm.ErrRecordNotFound {
		return nil, err
	}

	return permissions, nil
}

func AddPermission(data map[string]interface{}) (id int, err error) {
	permission := Permission{
		ParentId: data["parent_id"].(int),
		Name:     data["name"].(string),
		Path:     data["path"].(string),
		Method:   data["method"].(string),
		Type:     data["type"].(string),
	}
	if err = db.Create(&permission).Error; err != nil {
		return 0, err
	}

	return permission.ID, nil
}

func EditPermission(id int, data map[string]interface{}) error {
	if err := db.Model(&Permission{}).Where("id = ? AND deleted_at is null", id).Update(data).Error; err != nil {
		return err
	}

	return nil
}

func DeletePermission(id int) error {
	var permission Permission
	if err := db.First(&permission, id).Error; err != nil {
		return err
	}
	if err := db.Model(&permission).Association("Roles").Clear().Error; err != nil {
		return err
	}
	if err := db.Delete(&permission).Error; err != nil {
		return err
	}

	return nil
}
