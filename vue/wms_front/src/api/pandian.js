import request from '@/utils/request'

export function checkPandianOrder(data) {
  return request({
    url: '/pandian/checkPandianOrder',
    method: 'post',
    data
  })
}
export function getPandianStockList(data) {
  return request({
    url: '/pandian/getPandianStockList',
    method: 'post',
    data
  })
}
export function getPandianStock(data) {
  return request({
    url: '/pandian/getPandianStock',
    method: 'post',
    data
  })
}
export function barCode(data) {
  return request({
    url: '/pandian/barCode',
    method: 'post',
    data
  })
}
export function doPandianStock(data) {
  return request({
    url: '/pandian/doPandianStock',
    method: 'post',
    data
  })
}
export function doPandian(data) {
  return request({
    url: '/pandian/doPandian',
    method: 'post',
    data
  })
}