
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'company/index',
        method: 'post',
        data: query
    })
}

