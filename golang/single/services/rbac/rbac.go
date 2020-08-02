package rbac

import (
	"single/services/permissionservice"
	"single/services/roleservice"
	"single/services/userservice"
)

type Rbac struct {
	UserAPI       *userservice.User             `inject:""`
	RoleAPI       *roleservice.Role             `inject:""`
	PermissionAPI *permissionservice.Permission `inject:""`
}
