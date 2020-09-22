import request from '@/utils/request'

export function login(data) {
    return request({
      url: '/auth/login',
      method: 'post',
      data
    })
  }
  
  export function logout() {
    return request({
      url: '/auth/logout',
      method: 'post'
    })
  }
  
  export function getUserInfo() {
    return request({
      url: '/auth/me',
      method: 'post'
    })
  }
  
  export function createUser(data) {
    return request({
      url: '/user/create',
      method: 'post',
      params: data
    })
  }

  export function fetchList(query) {
    return request({
      url: '/user/index',
      method: 'post',
      params: query
    })
  }

  export function updateUser(data) {
    return request({
      url: '/user/update',
      method: 'post',
      params: data
    })
  }
  
  export function deleteUser(data) {
    return request({
      url: '/user/delete',
      method: 'post',
      params: data
    })
  }

  export function getById(data) {
    return request({
      url: '/user/getById',
      method: 'post',
      data
    })
  }
  