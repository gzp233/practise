import Cookies from 'js-cookie'
import axios from 'axios'
import {
  login,
  logout,
  getUserInfo
} from '@/api/user'
import {
  getToken,
  setToken,
  removeToken
} from '@/utils/auth'

const user = {
  state: {
    token: getToken(),
    username: '',
    roles: [],
    permissions: []
  },

  mutations: {
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_NAME: (state, name) => {
      state.username = name
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_PERMISSIONS: (state, permissions) => {
      state.permissions = permissions
    }
  },

  actions: {
    // 手机号登录
    Login({
      commit
    }, userInfo) {
      return new Promise((resolve, reject) => {
        login(userInfo).then(response => {
          const data = response.data
          commit('SET_TOKEN', data.access_token)
          setToken(data.access_token)
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },
    // 获取用户信息
    GetUserInfo({
      commit
    }) {
      return new Promise((resolve, reject) => {
        getUserInfo().then(response => {
          if (!response.data) {
            reject('error')
          }
          const data = response.data
          Cookies.set('USER_INFO_ID', data.id)
          if (data.roles && data.roles.length > 0) { // 验证返回的roles是否是一个非空数组
            commit('SET_ROLES', data.roles)
            commit('SET_PERMISSIONS', data.permissions)
          } else {
            reject('角色必须为一个数组!')
          }

          commit('SET_NAME', data.username)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },
    // 将刷新的 token 保存至本地
    RefreshToken({
      commit
    }, token) {
      return new Promise(function () {
        commit('SET_TOKEN', token)
        setToken(token)
      })
    },

    // 登出
    LogOut({
      commit,
      state
    }) {
      return new Promise((resolve, reject) => {
        logout().then(() => {
          commit('SET_TOKEN', '')
          commit('SET_ROLES', [])
          removeToken()
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLogOut({
      commit
    }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        removeToken()
        resolve()
      })
    }
  }
}

export default user
