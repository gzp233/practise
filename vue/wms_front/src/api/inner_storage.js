import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/inner_storage/index',
    method: 'post',
    params: query
  })
}

export function getById(data) {
  return request({
    url: '/inner_storage/getById',
    method: 'post',
    data
  })
}

export function hasStocked(data) {
  return request({
    url: '/inner_storage/hasStocked',
    method: 'post',
    data
  })
}

export function stockIn(data) {
  return request({
    url: '/inner_storage/stockIn',
    method: 'post',
    data
  })
}

export function confirmRe(data) {
  return request({
    url: '/inner_storage/confirmRe',
    method: 'post',
    data
  })
}
