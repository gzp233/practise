import request from '@/utils/request';

export function getImagesByDirectoryId(data) {
  return request({
    url: '/admin/image/getImagesByDirectoryId',
    method: 'get',
    params: data
  });
}

export function upload(data) {
  return request({
    url: '/admin/image/upload',
    method: 'post',
    data
  });
}

export function deleteByIds(data) {
  return request({
    url: '/admin/image/deleteByIds',
    method: 'post',
    data
  });
}
