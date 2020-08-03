import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/role/index',
    method: 'post',
    params: query
  })
}

export function getAll() {
  return request({
    url: '/role/getList',
    method: 'post'
  })
}

export function createRole(data) {
  return request({
    url: '/role/create',
    method: 'post',
    data
  })
}

export function getById(data) {
    return request({
      url: '/role/getById',
      method: 'post',
      data
    })
  }

export function updateRole(data) {
  return request({
    url: '/role/update',
    method: 'post',
    data
  })
}

export function deleteRole(data) {
  return request({
    url: '/role/delete',
    method: 'post',
    data
  })
}

export function changePermission(data) {
  return request({
    url: '/role/changePermission',
    method: 'post',
    data
  })
}

export function getPermissions(data) {
  return request({
    url: '/role/getPermissions',
    method: 'post',
    data
  })
}
