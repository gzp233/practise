import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/adjust_in/index',
    method: 'post',
    params: query
  })
}

export function getById(data) {
  return request({
    url: '/adjust_in/getById',
    method: 'post',
    data
  })
}

export function hasStocked(data) {
  return request({
    url: '/adjust_in/hasStocked',
    method: 'post',
    data
  })
}

export function stockIn(data) {
  return request({
    url: '/adjust_in/stockIn',
    method: 'post',
    data
  })
}

export function confirmRe(data) {
  return request({
    url: '/adjust_in/confirmRe',
    method: 'post',
    data
  })
}
