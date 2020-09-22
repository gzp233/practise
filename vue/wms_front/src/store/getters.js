const getters = {
  token: state => state.user.token,
  username: state => state.user.username,
  roles: state => state.user.roles,
  permissions: state => state.user.permissions,
  permission_routers: state => state.permission.routers,
  addRouters: state => state.permission.addRouters,
}
export default getters
