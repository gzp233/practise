import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/out_ensure/index',
    method: 'post',
    params: query
  })
}

export function getAllProducts() {
  return request({
    url: '/out_ensure/getAllProducts',
    method: 'post'
  })
}

export function generate(data) {
  return request({
    url: '/out_ensure/generate',
    method: 'post',
    data
  })
}

export function adjustNo(data) {
  return request({
    url: '/out_ensure/adjustNo',
    method: 'post',
    data
  })
}

export function getByNo(data) {
  return request({
    url: '/out_ensure/getByNo',
    method: 'post',
    data
  })
}
export function moveByNo(data) {
  return request({
    url: '/out_ensure/moveByNo',
    method: 'post',
    data
  })
}



