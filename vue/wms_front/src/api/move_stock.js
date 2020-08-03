import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/move_stock/index',
    method: 'post',
    params: query
  })
}

export function getGoodsByIds(query) {
  return request({
    url: '/move_stock/getGoodsByIds',
    method: 'post',
    params: query
  })
}


export function stockIn(data) {
  return request({
    url: '/move_stock/stockIn',
    method: 'post',
    data
  })
}