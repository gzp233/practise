import request from '@/utils/request'

export function upload(data) {
  return request({
    url: '/ocr/upload',
    method: 'post',
    data
  })
}



