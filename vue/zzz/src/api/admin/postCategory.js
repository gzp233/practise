import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/admin/postCategory',
    method: 'get',
    data
  });
}

export function getAll() {
  return request({
    url: '/admin/postCategory/getAll',
    method: 'get'
  });
}

export function create(data) {
  return request({
    url: '/admin/postCategory',
    method: 'post',
    data
  });
}

export function destroy(id) {
  return request({
    url: '/admin/postCategory/' + id,
    method: 'delete',
    params: { id: id }
  });
}

export function update(data) {
  return request({
    url: '/admin/postCategory/' + data.id,
    method: 'patch',
    data
  });
}
