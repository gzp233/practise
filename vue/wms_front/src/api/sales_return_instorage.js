import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/sales_return_instorage/index',
    method: 'post',
    params: query
  })
}

export function getById(data) {
  return request({
    url: '/sales_return_instorage/getById',
    method: 'post',
    data
  })
}

export function hasStocked(data) {
  return request({
    url: '/sales_return_instorage/hasStocked',
    method: 'post',
    data
  })
}

export function stockIn(data) {
  return request({
    url: '/sales_return_instorage/stockIn',
    method: 'post',
    data
  })
}

export function confirmRe(data) {
  return request({
    url: '/sales_return_instorage/confirmRe',
    method: 'post',
    data
  })
}
