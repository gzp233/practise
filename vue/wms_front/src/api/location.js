import request from '@/utils/request'

export function getIndex(query) {
  return request({
    url: 'location/index',
    method: 'post',
    data: query
  })
}

export function save(query) {
  return request({
    url: 'location/save',
    method: 'post',
    data: query
  })
}

export function stockOut(data) {
  return request({
    url: '/location/stockOut',
    method: 'post',
    data
  })
}

export function del(query) {
  return request({
    url: 'location/del',
    method: 'post',
    data: query
  })
}


export function getById(query) {
  return request({
    url: 'location/getById',
    method: 'post',
    data: query
  })
}

export function getLocations(query) {
  return request({
    url: 'location/getLocations',
    method: 'post',
    data: query
  })
}
