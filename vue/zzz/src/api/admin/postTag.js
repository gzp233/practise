import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/admin/postTag',
    method: 'get',
    data
  });
}

export function getAll() {
  return request({
    url: '/admin/postTag/getAll',
    method: 'get'
  });
}

export function create(data) {
  return request({
    url: '/admin/postTag',
    method: 'post',
    data
  });
}

export function destroy(id) {
  return request({
    url: '/admin/postTag/' + id,
    method: 'delete',
    params: { id: id }
  });
}

export function update(data) {
  return request({
    url: '/admin/postTag/' + data.id,
    method: 'patch',
    data
  });
}
