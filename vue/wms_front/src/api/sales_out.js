import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/sales_out/index',
    method: 'post',
    params: query
  })
}

export function getByNo(data) {
    return request({
      url: '/sales_out/getByNo',
      method: 'post',
      data
    })
  }

  export function stockOut(data) {
    return request({
      url: '/sales_out/stockOut',
      method: 'post',
      data
    })
  }
export function getBySee(data) {
  return request({
    url: '/sales_out/getBySee',
    method: 'post',
    data
  })
}

export function rollback(data) {
  return request({
    url: '/sales_out/rollback',
    method: 'post',
    data
  })
}

export function wave(data) {
  return request({
    url: '/sales_out/wave',
    method: 'post',
    data
  })
}

export function pxList(data) {
  return request({
    url: '/sales_out/pxList',
    method: 'post',
    data
  })
}
export function getByOut(data) {
  return request({
    url: '/sales_out/getByOut',
    method: 'post',
    data
  })
}
export function backNo(data) {
  return request({
    url: '/sales_out/backNo',
    method: 'post',
    data
  })
}