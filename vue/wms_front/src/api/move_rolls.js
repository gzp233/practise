import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/move_rolls/index',
    method: 'post',
    params: query
  })
}

export function getGoodsByIds(query) {
  return request({
    url: '/move_rolls/getGoodsByIds',
    method: 'post',
    params: query
  })
}


export function stockIn(data) {
  return request({
    url: '/move_rolls/stockIn',
    method: 'post',
    data
  })
}