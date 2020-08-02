import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/admin/imageDirectory',
    method: 'get',
    data
  });
}

export function create(data) {
  return request({
    url: '/admin/imageDirectory',
    method: 'post',
    data
  });
}

export function update(data) {
  return request({
    url: '/admin/imageDirectory/' + data.id,
    method: 'patch',
    data
  });
}

export function destroy(id) {
  return request({
    url: '/admin/imageDirectory/' + id,
    method: 'delete',
    params: { id: id }
  });
}

export function getDirectoryById(id) {
  return request({
    url: '/admin/imageDirectory/' + id,
    method: 'get',
    params: { id: id }
  });
}
