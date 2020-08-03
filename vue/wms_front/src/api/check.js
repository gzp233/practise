import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/check/index',
    method: 'post',
    data: query
  })
}

export function unfreezeIndex(query) {
  return request({
    url: '/check/unfreezeIndex',
    method: 'post',
    data: query
  })
}


export function getGoodsByProductId(data) {
  return request({
    url: '/check/getGoodsByProductId',
    method: 'post',
    data
  })
}

export function getByNo(data) {
  return request({
    url: '/check/getByNo',
    method: 'post',
    data
  })
}

export function relieve(data) {
  return request({
    url: '/check/relieve',
    method: 'post',
    data
  })
}

export function goodsList(query) {
  return request({
    url: '/check/goodsList',
    method: 'post',
    data: query
  })
}
export function shoppingList(query) {
  return request({
    url: '/check/shoppingList',
    method: 'post',
    data: query
  })
}

export function getAllGoods(data) {
  return request({
    url: '/check/getAllGoods',
    method: 'post',
    data
  })
}

export function getDetailById(data) {
  return request({
    url: '/check/getDetailById',
    method: 'post',
    data
  })
}
export function getDetailByIds(data) {
  return request({
    url: '/check/getDetailByIds',
    method: 'post',
    data
  })
}
export function separate(data) {
  return request({
    url: '/check/separate',
    method: 'post',
    data
  })
}

export function getAllProducts(data) {
  return request({
    url: '/check/getAllProducts',
    method: 'post',
    data
  })
}

export function getById(data) {
  return request({
    url: '/check/getById',
    method: 'post',
    data
  })
}
export function unfreeze(query) {
  return request({
    url: 'check/unfreeze',
    method: 'post',
    data: query
  })
}
export function shopping(data) {
  return request({
    url: '/check/shopping',
    method: 'post',
    data
  })
}

export function shoppingIndex(data) {
  return request({
    url: '/check/shoppingIndex',
    method: 'post',
    data
  })
}
export function del(query) {
  return request({
    url: 'check/del',
    method: 'post',
    data: query
  })
}
export function verify(data) {
  return request({
    url: '/check/verify',
    method: 'get',
    params:data
  })
}

export function batches(query) {
  return request({
    url: 'check/batches',
    method: 'post',
    data: query
  })
}
