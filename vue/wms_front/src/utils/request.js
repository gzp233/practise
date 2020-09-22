import axios from 'axios'
// import { Message } from 'element-ui'
import {
  Message,
  MessageBox
} from 'element-ui'
import store from '@/store'
import {
  getToken,
} from '@/utils/auth'

// create an axios instance
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url
  timeout: 30000 // request timeout
})

// request interceptor
service.interceptors.request.use(config => {
  // Do something before request is sent
  if (store.getters.token) {
    // 让每个请求携带token
    config.headers['Authorization'] = getToken()
  }
  return config
}, error => {
  // Do something with request error
  return Promise.reject(error)
})

// respone interceptor
service.interceptors.response.use(
  response => {
    // 如果 header 中存在 token，那么触发 refreshToken 方法，替换本地的 token
    var token = response.headers.authorization
    if (token) {
      store.dispatch('RefreshToken', token)
    }
    const res = response.data
    if (res.code !== 200) {
      if (res.code !== 401) {
        Message({
          message: res.message,
          type: 'error',
          duration: 5 * 1000
        })
      }

      if (res.code === 401) {
        MessageBox.confirm('你已被登出，请重新登录', '确定登出', {
          confirmButtonText: '重新登录',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          store.dispatch('FedLogOut').then(() => {
            location.reload()
          })
        })
      }
      return Promise.reject('error')
    } else {
      // 每次请求都更新操作时间
      return res
    }
  },
  error => {
    Message({
      message: error.message+"sdasdas",
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  })

export default service
