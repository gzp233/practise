import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/anticode/index',
    method: 'post',
    params: query
  })
}

export function sendCode(data) {
  return request({
    url: '/anticode/sendCode',
    method: 'post',
    data
  })
}
