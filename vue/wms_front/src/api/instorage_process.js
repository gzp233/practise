import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/instorage_process/index',
    method: 'post',
    params: query
  })
}

export function getAllGoods(query) {
  return request({
    url: '/instorage_process/getAllGoods',
    method: 'post',
    params: query
  })
}

export function getGoodsByIds(query) {
  return request({
    url: '/instorage_process/getGoodsByIds',
    method: 'post',
    params: query
  })
}

export function getDetailById(query) {
  return request({
    url: '/instorage_process/getDetailById',
    method: 'post',
    params: query
  })
}

export function move(data) {
  return request({
    url: '/instorage_process/move',
    method: 'post',
    data
  })
}

export function getAllProducts() {
  return request({
    url: '/instorage_process/getAllProducts',
    method: 'post'
  })
}

export function stockIn(data) {
  return request({
    url: '/instorage_process/stockIn',
    method: 'post',
    data
  })
}