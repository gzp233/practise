
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'series/index',
        method: 'post',
        data: query
    })
}

