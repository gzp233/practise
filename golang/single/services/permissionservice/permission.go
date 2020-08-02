package permissionservice

import (
	"single/models"

	"github.com/casbin/casbin/v2"
)

type Permission struct {
	ID       int
	ParentId int
	Name     string
	Path     string
	Type     string
	Method   string

	PageNum  int
	PageSize int

	Enforcer *casbin.SyncedEnforcer `inject:""`
}

func (p *Permission) ExistByID() (bool, error) {
	permission, err := models.GetPermissionByID(p.ID)
	if err != nil {
		return true, err
	}
	if permission.ID > 0 {
		return true, nil
	}

	return false, nil
}

func (p *Permission) Add() (id int, err error) {
	permission := map[string]interface{}{
		"parent_id": p.ParentId,
		"name":      p.Name,
		"type":      p.Type,
		"path":      p.Path,
		"method":    p.Method,
	}
	if id, err = models.AddPermission(permission); err != nil {
		return 0, err
	}

	return
}

func (p *Permission) Edit() error {
	if err := models.EditPermission(p.ID, map[string]interface{}{
		"parent_id": p.ParentId,
		"name":      p.Name,
		"type":      p.Type,
		"path":      p.Path,
		"method":    p.Method,
	}); err != nil {
		return err
	}

	return nil
}

func (p *Permission) Delete() error {
	if err := models.DeletePermission(p.ID); err != nil {
		return err
	}

	return nil
}

func (p *Permission) GetByID() (*models.Permission, error) {
	permission, err := models.GetPermissionByID(p.ID)
	if err != nil {
		return nil, err
	}

	return permission, nil
}

func (p *Permission) Count() (int, error) {
	return models.GetPermissionTotal(p.getQueryMaps())
}

func (p *Permission) GetList() ([]*models.Permission, error) {
	permissions, err := models.GetPermissions(p.PageNum, p.PageSize, p.getQueryMaps())
	if err != nil {
		return nil, err
	}

	return permissions, nil
}

func (p *Permission) GetPermissionTree() ([]*models.Permission, error) {
	permissions, err := models.GetAllPermissions()
	if err != nil {
		return nil, err
	}

	result := &models.Permission{}
	getTree(permissions, result)

	return result.Children, nil
}

func getTree(permissions []*models.Permission, result *models.Permission) {
	tmp := []*models.Permission{}
	for _, permission := range permissions {
		if permission.ParentId == result.ID {
			result.Children = append(result.Children, permission)
		} else {
			tmp = append(tmp, permission)
		}
	}

	if len(result.Children) > 0 {
		for _, v := range result.Children {
			getTree(tmp, v)
		}
	}

}

func (p *Permission) getQueryMaps() map[string]interface{} {
	maps := make(map[string]interface{})
	maps["deleted_at"] = nil
	return maps
}
