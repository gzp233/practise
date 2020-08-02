import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/admin/user',
    method: 'get',
    data
  });
}

export function destroy(data) {
  return request({
    url: '/admin/user/' + data.id,
    method: 'delete',
    data
  });
}

export function modifyPassword(data) {
  return request({
    url: '/admin/user/modifyPassword',
    method: 'post',
    data
  });
}
