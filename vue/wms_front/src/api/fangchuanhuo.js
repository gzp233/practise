import request from '@/utils/request'

export function getList(query) {
  return request({
    url: 'fangchuanhuo/index',
    method: 'post',
    data: query
  })
}

export function checkFangchuanhuoOrder(query) {
  return request({
    url: 'fangchuanhuo/checkFangchuanhuoOrder',
    method: 'post',
    data: query
  })
}

export function getByOrderNo(query) {
  return request({
    url: 'fangchuanhuo/getByOrderNo',
    method: 'post',
    data: query
  })
}

export function submit(query) {
  return request({
    url: 'fangchuanhuo/submit',
    method: 'post',
    data: query
  })
}
export function setRedis(query) {
  return request({
    url: 'fangchuanhuo/setRedis',
    method: 'post',
    data: query
  })
}
