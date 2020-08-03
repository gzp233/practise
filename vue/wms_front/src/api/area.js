
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'area/index',
        method: 'post',
        data: query
    })
}

export function save(query) {
    return request({
        url: 'area/save',
        method: 'post',
        data: query
    })
}

export function del(query) {
    return request({
        url: 'area/del',
        method: 'post',
        data: query
    })
}

export function getById(query) {
    return request({
        url: 'area/getById',
        method: 'post',
        data: query
    })
}

export function getAll() {
    return request({
        url: 'area/getAll',
        method: 'post'
    })
}

