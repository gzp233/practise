
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'brand/index',
        method: 'post',
        data: query
    })
}

