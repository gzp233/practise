
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'deliver/index',
        method: 'post',
        data: query
    })
}

