
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'vendor/index',
        method: 'post',
        data: query
    })
}

