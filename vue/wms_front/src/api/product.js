
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'product/index',
        method: 'post',
        data: query
    })
}

export function toggleCode(query) {
    return request({
        url: 'product/toggleCode',
        method: 'post',
        data: query
    })
}

export function editProduct(data) {
    return request({
        url: 'product/edit',
        method: 'post',
        data: data
    })
}

export function upload(data) {
    return request({
        url: 'product/upload',
        method: 'post',
        data: data,
        headers:{"Content-Type": "multipart/form-data"}
    })
}

