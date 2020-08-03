import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/adjust/index',
    method: 'post',
    params: query
  })
}

export function getByNo(data) {
    return request({
      url: '/adjust/getByNo',
      method: 'post',
      data
    })
  }

export function getBySee(data) {
  return request({
    url: '/adjust/getBySee',
    method: 'post',
    data
  })
}

  export function stockOut(data) {
    return request({
      url: '/adjust/stockOut',
      method: 'post',
      data
    })
  }

export function rollback(data) {
  return request({
    url: '/adjust/rollback',
    method: 'post',
    data
  })
}
export function pxList(data) {
  return request({
    url: '/adjust/pxList',
    method: 'post',
    data
  })
}
export function backNo(data) {
  return request({
    url: '/adjust/backNo',
    method: 'post',
    data
  })
}