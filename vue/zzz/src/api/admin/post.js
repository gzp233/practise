import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/admin/post',
    method: 'get',
    data
  });
}

export function create(data) {
  return request({
    url: '/admin/post',
    method: 'post',
    data
  });
}

export function getById(id) {
  return request({
    url: '/admin/post/' + id,
    method: 'get'
  });
}

export function destroy(id) {
  return request({
    url: '/admin/post/' + id,
    method: 'delete',
    params: { id: id }
  });
}

export function update(data) {
  return request({
    url: '/admin/post/' + data.id,
    method: 'patch',
    data
  });
}

export function togglePublish(data) {
  return request({
    url: '/admin/post/togglePublish',
    method: 'post',
    data
  });
}
