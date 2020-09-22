import request from '@/utils/request'

export function create(query) {
  return request({
    url: '/unit/create',
    method: 'post',
    data: query
  })
}

export function update(query) {
  return request({
    url: '/unit/update',
    method: 'post',
    data: query
  })
}

export function del(query) {
  return request({
    url: '/unit/del',
    method: 'post',
    data: query
  })
}

