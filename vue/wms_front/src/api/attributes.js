import request from '@/utils/request'

export function getIndex(query) {
  return request({
    url: 'attributes/index',
    method: 'post',
    data: query
  })
}

export function save(query) {
  return request({
    url: 'attributes/save',
    method: 'post',
    data: query
  })
}

export function del(query) {
  return request({
    url: 'attributes/del',
    method: 'post',
    data: query
  })
}

export function getAll() {
  return request({
    url: '/attributes/getList',
    method: 'post'
  })
}
