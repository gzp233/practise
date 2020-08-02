import request from '@/utils/request';

export function getList(data) {
  return request({
    url: '/post/index',
    method: 'post',
    data
  });
}

export function getCommentList(data) {
  return request({
    url: '/post/getCommentList',
    method: 'post',
    data
  });
}

export function publishComment(data) {
  return request({
    url: '/post/publishComment',
    method: 'post',
    data
  });
}

export function show(id) {
  return request({
    url: '/post/show/' + id,
    method: 'get'
  });
}

export function getCategoryList() {
  return request({
    url: '/post/getCategoryList',
    method: 'get'
  });
}

export function getTagList() {
  return request({
    url: '/post/getTagList',
    method: 'get'
  });
}
