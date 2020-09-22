
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'reason/index',
        method: 'post',
        data: query
    })
}

