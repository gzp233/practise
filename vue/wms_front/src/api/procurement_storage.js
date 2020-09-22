import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/procurement_storage/index',
    method: 'post',
    params: query
  })
}

export function getById(data) {
  return request({
    url: '/procurement_storage/getById',
    method: 'post',
    data
  })
}

export function hasStocked(data) {
  return request({
    url: '/procurement_storage/hasStocked',
    method: 'post',
    data
  })
}

export function stockIn(data) {
  return request({
    url: '/procurement_storage/stockIn',
    method: 'post',
    data
  })
}

export function confirmRe(data) {
  return request({
    url: '/procurement_storage/confirmRe',
    method: 'post',
    data
  })
}
export function getByNo(data) {
  return request({
    url: '/procurement_storage/getByNo',
    method: 'post',
    data
  })
}