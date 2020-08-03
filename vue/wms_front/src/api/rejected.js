
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'rejected/index',
        method: 'post',
        data: query
    })
}

