
import request from '@/utils/request'

export function getIndex(query) {
    return request({
        url: 'flg/index',
        method: 'post',
        data: query
    })
}

