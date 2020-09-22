import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/goods/index',
    method: 'post',
    data: query
  })
}

export function unfreezeIndex(query) {
  return request({
    url: '/goods/unfreezeIndex',
    method: 'post',
    data: query
  })
}


export function getGoodsByProductId(data) {
  return request({
    url: '/goods/getGoodsByProductId',
    method: 'post',
    data
  })
}

export function getByNo(data) {
  return request({
    url: '/goods/getByNo',
    method: 'post',
    data
  })
}

export function relieve(data) {
  return request({
    url: '/goods/relieve',
    method: 'post',
    data
  })
}

export function goodsList(query) {
  return request({
    url: '/goods/goodsList',
    method: 'post',
    data: query
  })
}

export function getAllGoods(query) {
  return request({
    url: '/goods/getAllGoods',
    method: 'post',
    params: query
  })
}

export function getDetailById(query) {
  return request({
    url: '/goods/getDetailById',
    method: 'post',
    params: query
  })
}


export function getAllProducts() {
  return request({
    url: '/goods/getAllProducts',
    method: 'post'
  })
}

export function getById(data) {
  return request({
    url: '/goods/getById',
    method: 'post',
    data
  })
}
export function unfreeze(query) {
  return request({
    url: 'goods/unfreeze',
    method: 'post',
    data: query
  })
}
