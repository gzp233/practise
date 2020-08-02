import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/admin/postComment',
    method: 'post',
    data
  });
}

export function destroy(id) {
  return request({
    url: '/admin/postComment/' + id,
    method: 'delete',
    params: { id: id }
  });
}
