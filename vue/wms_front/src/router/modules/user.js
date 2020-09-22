import layout from '@/components/common/layout'

const userRouter = {
  path: '/users',
  name: '用户配置',
  meta: {
    roles: ['admin'],
    permission: '3EF5724B5FD1086F43D9A0894819439A'
  },
  component: layout,
  redirect: '/users/user',
  children: [{
    path: "/users/user",
    name: "用户管理",
    meta: {
      roles: ['admin'],
      permission: '7F63625130EB64094A931192F1EA491A'
    },
    component: () => import('@/components/users/user/user')
  }, {
    path: "/users/user/add",
    name: "新增用户",
    meta: {
      roles: ['admin'],
      permission: '63B86B5E5DB53E0B2038A9151603174E'
    },
    component: () => import('@/components/users/user/userEdit')
  }, {
    path: "/users/user/edit/:userid",
    name: "编辑用户",
    meta: {
      roles: ['admin'],
      permission: '14C890C6D8E2AB735687A18933D68AA5'
    },
    component: () => import('@/components/users/user/userEdit')
  }, {
    path: "/users/role",
    name: "角色管理",
    meta: {
      roles: ['admin'],
      permission: '1CF7FBB0CCE48D04E357AA78C6D74293'
    },
    component: () => import('@/components/users/role/role')
  }, {
    path: "/users/role/add",
    name: "新增角色",
    meta: {
      roles: ['admin'],
      permission: '2DB0D9B81C0C713C2451C82A94F7668D'
    },
    component: () => import('@/components/users/role/roleEdit')
  }, {
    path: "/users/role/edit/:roleid",
    name: "编辑角色",
    meta: {
      roles: ['admin'],
      permission: '8C423D24C03328E771DACBE57C101543'
    },
    component: () => import('@/components/users/role/roleEdit')
  }, {
    path: "/users/role/permission/:roleid",
    name: "分配权限",
    meta: {
      roles: ['admin'],
      permission: '5C3CF5870413DA87FC33056735877A21'
    },
    component: () => import('@/components/users/role/permission')
  }, {
    path: "/users/permission",
    name: "权限管理",
    meta: {
      roles: ['admin'],
      permission: '80007654A3C9B46B95F9516F1308A5F1'
    },
    component: () => import('@/components/users/permission/permission')
  }, {
    path: "/users/permission/add",
    name: "新增权限",
    meta: {
      roles: ['admin'],
      permission: '47FDB3F9A601ACCC630EBEEE07AE2877'
    },
    component: () => import('@/components/users/permission/permissionEdit')
  }, {
    path: "/users/permission/edit/:permissionid",
    name: "编辑权限",
    meta: {
      roles: ['admin'],
      permission: 'E0F3BDEB379E8C7ADBAE5FB3F7EE7922'
    },
    component: () => import('@/components/users/permission/permissionEdit')
  }]
}

export default userRouter
