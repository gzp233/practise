import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/jihuo/index',
    method: 'post',
    params: query
  })
}

export function getByNo(data) {
    return request({
      url: '/jihuo/getByNo',
      method: 'post',
      data
    })
  }
export function getJihuoStockList(data) {
  return request({
    url: '/jihuo/getJihuoStockList',
    method: 'post',
    data
  })
}
export function getJihuoStock(data) {
  return request({
    url: '/jihuo/getJihuoStock',
    method: 'post',
    data
  })
}
export function doJihuo(data) {
  return request({
    url: '/jihuo/doJihuo',
    method: 'post',
    data
  })
}
export function doJihuoStock(data) {
  return request({
    url: '/jihuo/doJihuoStock',
    method: 'post',
    data
  })
}