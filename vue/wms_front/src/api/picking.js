import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/picking/index',
    method: 'post',
    params: query
  })
}

export function getByNos(data) {
    return request({
      url: '/picking/getByNos',
      method: 'post',
      data
    })
  }

export function getStockById(data) {
    return request({
      url: '/picking/getStockById',
      method: 'post',
      data
    })
  }

  export function stockOut(data) {
    return request({
      url: '/picking/stockOut',
      method: 'post',
      data
    })
  }