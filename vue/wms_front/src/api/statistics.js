import request from '@/utils/request'

export function enter(query) {
  return request({
    url: '/statistics/enter',
    method: 'post',
    params: query
  })
}

export function appear(query) {
  return request({
    url: '/statistics/appear',
    method: 'post',
    params: query
  })
}
