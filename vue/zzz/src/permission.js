import router from './router'
import store from './store'
import { Message } from 'element-ui'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import { getToken } from '@/utils/auth' // get token from cookie
import getPageTitle from '@/utils/get-page-title'

NProgress.configure({ showSpinner: false }) // NProgress Configuration

router.beforeEach(async(to, from, next) => {
  // start progress bar
  NProgress.start()

  // set page title
  document.title = getPageTitle(to.meta.title)

  const NeedLoginList = ['/chatroom'];

  // determine whether the user has logged in
  const hasToken = getToken()
  const paths = to.path.split('/')
  if (hasToken) {
    if (to.path === '/login' || to.path === '/register') {
      // if is logged in, redirect to the home page
      next({ path: '/' })
      NProgress.done()
    } else {
      const hasGetUserInfo = store.getters.name
      if (hasGetUserInfo) {
        if (paths[1] === 'admin' && store.getters.is_admin !== 1) {
          next({ path: '/' })
          NProgress.done()
        } else {
          next()
        }
      } else {
        try {
          // get user info
          await store.dispatch('user/getInfo')
          if (paths[1] === 'admin' && store.getters.is_admin !== 1) {
            next({ path: '/' })
            NProgress.done()
          } else {
            next()
          }
        } catch (error) {
          // remove token and go to login page to re-login
          await store.dispatch('user/resetToken')
          Message.error(error || 'Has Error')
          next(`/login?redirect=${to.path}`)
          NProgress.done()
        }
      }
    }
  } else {
    // 后台页面需要先登录，前台页面跳转
    if (paths[1] === 'admin' || NeedLoginList.indexOf(to.path) > -1) {
      next(`/login?redirect=${to.path}`)
      NProgress.done()
    } else {
      next()
    }
  }
})

router.afterEach(() => {
  // finish progress bar
  NProgress.done()
})
