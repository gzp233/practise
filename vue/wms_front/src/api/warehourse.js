import request from '@/utils/request' 

export function getIndex(query) {
    return request({
        url: 'warehourse/index',
        method: 'post',
        data: query
    })
}

export function save(query) {
    return request({
        url: 'warehourse/save',
        method: 'post',
        data: query
    })
}

export function del(query) {
    return request({
        url: 'warehourse/del',
        method: 'post',
        data: query
    })
}

export function getAll() {
    return request({
      url: '/warehourse/getList',
      method: 'post'
    })
  }

  export function getAll2() {
    return request({
      url: '/warehourse/getAll',
      method: 'post'
    })
  }
