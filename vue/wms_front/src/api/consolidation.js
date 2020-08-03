import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/consolidation/index',
    method: 'post',
    params: query
  })
}
export function getByNo(data) {
  return request({
    url: '/consolidation/getByNo',
    method: 'post',
    data
  })
}
export function getList(data) {
  return request({
    url: '/consolidation/getList',
    method: 'post',
    data
  })
}
export function goodsList(query) {
  return request({
    url: '/consolidation/show',
    method: 'post',
    params: query
  })
}
export function del(data) {
  return request({
    url: '/consolidation/del',
    method: 'post',
    data
  })
}

