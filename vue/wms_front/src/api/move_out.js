import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/move_out/index',
    method: 'post',
    params: query
  })
}
export function getByNo(data) {
  return request({
    url: '/move_out/getByNo',
    method: 'post',
    data
  })
}

export function getBySee(data) {
  return request({
    url: '/move_out/getBySee',
    method: 'post',
    data
  })
}

export function stockOut(data) {
  return request({
    url: '/move_out/stockOut',
    method: 'post',
    data
  })
}

export function rollback(data) {
  return request({
    url: '/move_out/rollback',
    method: 'post',
    data
  })
}
export function pxList(data) {
  return request({
    url: '/move_out/pxList',
    method: 'post',
    data
  })
}
export function backNo(data) {
  return request({
    url: '/move_out/backNo',
    method: 'post',
    data
  })
}
