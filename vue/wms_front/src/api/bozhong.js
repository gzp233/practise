import request from '@/utils/request'

export function getByNo(data) {
    return request({
      url: '/bozhong/getByNo',
      method: 'post',
      data
    })
  }
export function getBozhongStockList(data) {
  return request({
    url: '/bozhong/getBozhongStockList',
    method: 'post',
    data
  })
}
export function getbozhongStock(data) {
  return request({
    url: '/bozhong/getbozhongStock',
    method: 'post',
    data
  })
}
export function doBozhongStock(data) {
  return request({
    url: '/bozhong/doBozhongStock',
    method: 'post',
    data
  })
}

export function getProduct(data) {
  return request({
    url: '/bozhong/getProduct',
    method: 'post',
    data
  })
}
export function doBozhong(data) {
  return request({
    url: '/bozhong/doBozhong',
    method: 'post',
    data
  })
}


