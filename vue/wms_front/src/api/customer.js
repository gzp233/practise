
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'customer/index',
        method: 'post',
        data: query
    })
}

