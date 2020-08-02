package roleservice

import (
	"single/models"
	"single/pkg/logging"

	"github.com/casbin/casbin/v2"
)

type Role struct {
	ID            int
	Name          string
	PermissionIds []int

	PageNum  int
	PageSize int

	Enforcer *casbin.SyncedEnforcer `inject:""`
}

func (r *Role) ExistByID() (bool, error) {
	role, err := models.GetRoleByID(r.ID)
	if err != nil {
		return true, err
	}

	if role.ID > 0 {
		return true, nil
	}

	return false, nil
}

func (r *Role) ExistByName() (bool, error) {
	role, err := models.GetRoleByName(r.Name)
	if err != nil {
		return true, err
	}

	if role.ID <= 0 || role.ID == r.ID {
		return false, nil
	}

	return true, nil
}

func (r *Role) Add() (id int, err error) {
	if id, err = models.AddRole(map[string]interface{}{
		"name":           r.Name,
		"permission_ids": r.PermissionIds,
	}); err != nil {
		return 0, err
	}

	return
}

func (r *Role) Edit() error {
	if err := models.EditRole(r.ID, map[string]interface{}{
		"name":           r.Name,
		"permission_ids": r.PermissionIds,
	}); err != nil {
		return err
	}

	return nil
}

func (r *Role) Delete() error {
	return models.DeleteRole(r.ID)
}

func (r *Role) GetByID() (*models.Role, error) {
	return models.GetRoleByID(r.ID)
}

func (r *Role) Count() (int, error) {
	return models.GetRoleTotal(r.getQueryMaps())
}

func (r *Role) GetList() ([]*models.Role, error) {
	return models.GetRoles(r.PageNum, r.PageSize, r.getQueryMaps())
}

func (r *Role) GetAll() ([]*models.Role, error) {
	return models.GetAllRoles()
}

func (r *Role) getQueryMaps() map[string]interface{} {
	maps := make(map[string]interface{})
	maps["deleted_at"] = nil
	return maps
}

// LoadAllPolicy 加载所有的角色策略
func (r *Role) LoadAllPolicy() error {
	roles, err := models.GetAllRoles()
	if err != nil {
		return err
	}

	for _, role := range roles {
		err = r.LoadPolicy(role.ID)
		if err != nil {
			return err
		}
	}
	logging.Info("加载所有角色权限关系", r.Enforcer.GetPolicy())

	return nil
}

// LoadPolicy 加载角色权限策略
func (r *Role) LoadPolicy(id int) error {

	role, err := models.GetRoleByID(id)
	if err != nil {
		return err
	}
	r.Enforcer.DeleteRole(role.Name)

	for _, permission := range role.Permissions {
		if permission.Path == "" || permission.Method == "" {
			continue
		}
		r.Enforcer.AddPermissionForUser(role.Name, permission.Path, permission.Method)
	}

	return nil
}
