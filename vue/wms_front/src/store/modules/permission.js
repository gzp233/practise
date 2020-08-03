import { asyncRouterMap, constantRouterMap } from '@/router'
import store from '../../store'

/**
 * 通过meta.permission判断
 * @param permissions
 * @param route
 */
function hasPermission(permissions, route) {
  if (route.meta && route.meta.permission) {
    return permissions.some(permission => route.meta.permission === permission)
  } else {
    return true
  }
}

/**
 * 递归过滤异步路由表，返回符合用户角色权限的路由表
 * @param asyncRouterMap
 * @param permissions
 */
function filterAsyncRouter(asyncRouterMap, permissions) {
  const accessedRouters = asyncRouterMap.filter(route => {
    if (hasPermission(permissions, route)) {
      if (route.children && route.children.length) {
        route.children = filterAsyncRouter(route.children, permissions)
      }
      return true
    }
    return false
  })
  return accessedRouters
}

const permission = {
  state: {
    routers: constantRouterMap,
    addRouters: []
  },
  mutations: {
    SET_ROUTERS: (state, routers) => {
      state.addRouters = routers
      state.routers = constantRouterMap.concat(routers)
    }
  },
  actions: {
    GenerateRoutes({ commit }, data) {
      return new Promise(resolve => {
        const { permissions } = data
        let accessedRouters
        if (store.getters.roles.indexOf('admin') >= 0) {
          accessedRouters = asyncRouterMap
        } else {
          accessedRouters = filterAsyncRouter(asyncRouterMap, permissions)
        }
        commit('SET_ROUTERS', accessedRouters)
        resolve()
      })
    }
  }
}

export default permission
