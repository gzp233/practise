import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/permission/index',
    method: 'post',
    params: query
  })
}

export function createPermission(data) {
  return request({
    url: '/permission/create',
    method: 'post',
    data
  })
}

export function updatePermission(data) {
  return request({
    url: '/permission/update',
    method: 'post',
    data
  })
}

export function deletePermission(data) {
  return request({
    url: '/permission/delete',
    method: 'post',
    data
  })
}

export function getById(data) {
  return request({
    url: '/permission/getById',
    method: 'post',
    data
  })
}

export function getAll() {
  return request({
    url: '/permission/getList',
    method: 'post'
  })
}

export function getTree() {
  return request({
    url: 'permission/getTree',
    method: 'post'
  })
}
