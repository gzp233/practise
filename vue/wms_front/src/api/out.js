import request from '@/utils/request'

export function out(data) {
  return request({
    url: '/yiku/index',
    method: 'post',
    data
  })
}
export function delCart(data) {
  return request({
    url: '/yiku/delCart',
    method: 'post',
    data
  })
}
export function goodsList2(data) {
  return request({
    url: '/goods/goodsList2',
    method: 'post',
    data
  })
}
export function addCart(data) {
  return request({
    url: '/yiku/addCart',
    method: 'post',
    data
  })
}
export function cartList(data) {
  return request({
    url: '/yiku/cartList',
    method: 'post',
    data
  })
}
export function submitCart(data) {
  return request({
    url: '/yiku/submitCart',
    method: 'post',
    data
  })
}

export function getList(data) {
  return request({
    url: '/user/getList',
    method: 'post',
    data
  })
}

//手机端
export function getYikuNos(data) {
  return request({
    url: 'yiku_wap/getYikuNos',
    method: 'post',
    data
  })
}
export function getStockListByNo(data) {
  return request({
    url: 'yiku_wap/getStockListByNo',
    method: 'post',
    data
  })
}
export function getStock(data) {
  return request({
    url: 'yiku_wap/getStock',
    method: 'post',
    data
  })
}
export function doStock(data) {
  return request({
    url: 'yiku_wap/doStock',
    method: 'post',
    data
  })
}
export function submit(data) {
  return request({
    url: 'yiku_wap/submit',
    method: 'post',
    data
  })
}
//手机端创建任务
export function createTask(data) {
  return request({
    url: 'yiku_wap/createTask',
    method: 'post',
    data
  })
}

export function getOriginStockList(data) {
  return request({
    url: 'yiku_wap/getOriginStockList',
    method: 'post',
    data
  })
}
export function getOriginByStockNo(data) {
  return request({
    url: 'yiku_wap/getOriginByStockNo',
    method: 'post',
    data
  })
}
export function stockOrigin(data) {
  return request({
    url: 'yiku_wap/stockOrigin',
    method: 'post',
    data
  })
}
export function submitOrigin(data) {
  return request({
    url: 'yiku_wap/submitOrigin',
    method: 'post',
    data
  })
}
export function getGoodsList(data) {
  return request({
    url: 'yiku_wap/getGoodsList',
    method: 'post',
    data
  })
}
export function getToStockList(data) {
  return request({
    url: 'yiku_wap/getToStockList',
    method: 'post',
    data
  })
}
export function getToByStockNo(data) {
  return request({
    url: 'yiku_wap/getToByStockNo',
    method: 'post',
    data
  })
}
export function stockTo(data) {
  return request({
    url: 'yiku_wap/stockTo',
    method: 'post',
    data
  })
}
export function submitTo(data) {
  return request({
    url: 'yiku_wap/submitTo',
    method: 'post',
    data
  })
}
export function getOriginGoodsList(data) {
  return request({
    url: 'yiku_wap/getOriginGoodsList',
    method: 'post',
    data
  })
}

