import request from '@/utils/request';

export function getDirectoryList(data) {
  return request({
    url: '/image/directoryList',
    method: 'get',
    data
  });
}

export function getDirectoryById(id) {
  return request({
    url: '/image/getDirectoryById',
    method: 'get',
    params: { id: id }
  });
}

export function getImagesByDirectoryId(data) {
  return request({
    url: '/image/getImagesByDirectoryId',
    method: 'get',
    params: data
  });
}
