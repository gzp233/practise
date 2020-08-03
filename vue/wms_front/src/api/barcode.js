import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/adjust/index',
    method: 'post',
    params: query
  })
}

export function saveCode(data) {
    return request({
      url: '/barcode/saveCode',
      method: 'post',
      data
    })
  }

export function delCode(data) {
    return request({
      url: '/barcode/delCode',
      method: 'post',
      data
    })
  }

export function getErrors(data) {
    return request({
      url: '/barcode/getErrors',
      method: 'post',
      data
    })
  }

export function getFuheOrderByNo(data) {
    return request({
      url: '/barcode/getFuheOrderByNo',
      method: 'post',
      data
    })
  }

export function getBarCode(data) {
  return request({
    url: '/barcode/getBarCode',
    method: 'post',
    data
  })
}

  export function stockOut(data) {
    return request({
      url: '/barcode/stockOut',
      method: 'post',
      data
    })
  }

export function rollback(data) {
  return request({
    url: '/adjust/rollback',
    method: 'post',
    data
  })
}


export function getGoods(data) {
  return request({
    url: '/barcode/getGoods',
    method: 'post',
    data
  })
}

export function checkJanhuoOrder(data) {
  return request({
    url: '/barcode/checkJanhuoOrder',
    method: 'post',
    data
  })
}

export function getJianhuoStockList(data) {
  return request({
    url: '/barcode/getJianhuoStockList',
    method: 'post',
    data
  })
}

export function getJianhuoStock(data) {
  return request({
    url: '/barcode/getJianhuoStock',
    method: 'post',
    data
  })
}

export function doJianhuoStock(data) {
  return request({
    url: '/barcode/doJianhuoStock',
    method: 'post',
    data
  })
}

export function doJianhuo(data) {
  return request({
    url: '/barcode/doJianhuo',
    method: 'post',
    data
  })
}