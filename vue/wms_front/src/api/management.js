import request from '@/utils/request'

export function modifyList(query) {
  return request({
    url: 'goods/modifyList',
    method: 'get',
    data: query
  })
}
export function modify(data) {
    return request({
      url: 'goods/modify',
      method: 'post',
      data
    })
}
export function goodsList(data) {
  return request({
    url: 'goods/goodsList',
    method: 'post',
    data
  })
}