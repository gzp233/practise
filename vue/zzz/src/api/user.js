import request from '@/utils/request';

export function register(data) {
  return request({
    url: '/auth/register',
    method: 'post',
    data
  });
}

export function login(data) {
  return request({
    url: '/auth/login',
    method: 'post',
    data
  });
}

export function getInfo(token) {
  return request({
    url: '/auth/me',
    method: 'post',
    params: { token }
  });
}

export function logout() {
  return request({
    url: '/auth/logout',
    method: 'post'
  });
}
