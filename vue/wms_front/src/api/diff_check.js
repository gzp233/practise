import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/diff_check/index',
    method: 'post',
    data: query
  })
}

export function unfreezeIndex(query) {
  return request({
    url: '/diff_check/unfreezeIndex',
    method: 'post',
    data: query
  })
}


export function getGoodsByProductId(data) {
  return request({
    url: '/diff_check/getGoodsByProductId',
    method: 'post',
    data
  })
}

export function getByNo(data) {
  return request({
    url: '/diff_check/getByNo',
    method: 'post',
    data
  })
}

export function relieve(data) {
  return request({
    url: '/diff_check/relieve',
    method: 'post',
    data
  })
}

export function goodsList(query) {
  return request({
    url: '/diff_check/goodsList',
    method: 'post',
    data: query
  })
}

export function getAllGoods(data) {
  return request({
    url: '/diff_check/getAllGoods',
    method: 'post',
    data
  })
}
export function shopping(data) {
  return request({
    url: '/diff_check/shopping',
    method: 'post',
    data
  })
}

export function shoppingList(data) {
  return request({
    url: '/diff_check/shoppingList',
    method: 'post',
    data
  })
}

export function separate(data) {
  return request({
    url: '/diff_check/separate',
    method: 'post',
    data
  })
}

export function getDetailById(data) {
  return request({
    url: '/diff_check/getDetailById',
    method: 'post',
    data
  })
}


export function getAllProducts(data) {
  return request({
    url: '/diff_check/getAllProducts',
    method: 'post',
    data
  })
}

export function getById(data) {
  return request({
    url: '/diff_check/getById',
    method: 'post',
    data
  })
}
export function unfreeze(query) {
  return request({
    url: 'diff_check/unfreeze',
    method: 'post',
    data: query
  })
}
export function del(query) {
  return request({
    url: 'diff_check/del',
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
    url: 'diff_check/batches',
    method: 'post',
    data: query
  })
}
