import Vue from 'vue'
import Router from 'vue-router'
import store from '../store'

Vue.use(Router)

export const constantRouterMap = [{
  path: '/login',
  name: '登录',
  component: () => import('@/components/login'),
  hidden: true
}]

export default new Router({
  // base:'/web/dist/',
  mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: constantRouterMap
})

import userRouter from './modules/user'
import basicRouter from './modules/basic'
import management from './modules/management'
import baseinfoRouter from './modules/baseinfo'
import instorageActionRouter from './modules/instorage_action'
import outstorageActionRouter from './modules/outstorage_action'
import storageActionRouter from './modules/storage_action'
import barcodeRouter from './modules/barcode'

export const asyncRouterMap = [{
    path: '/',
    name: "主页",
    meta: {
      roles: ['admin'],
      permission: ''
    },
    component: () => import('@/components/main'),
    redirect: '/index',
    children: [{
        path: '/index',
        name: '首页',
        meta: {
          roles: ['admin'],
          permission: ''
        },
        component: () => import('@/components/index')
      },
      userRouter,
      basicRouter,
      management,
      baseinfoRouter,
      instorageActionRouter,
      outstorageActionRouter,
      storageActionRouter
    ]
  },
  barcodeRouter,
  {
    path: '*',
    name: '404',
    meta: {
      roles: ['admin'],
      permission: ''
    },
    redirect: to => {
      // 对于模块自动跳转一下
      const routers = store.getters.permission_routers
      for (let i = 0;i<routers.length; i++) {
        if (routers[i].path === "/") {
          const modules = routers[i].children
          for (let j = 0;j<modules.length;j++) {
            if (modules[j].path === to.path && modules[j].children && modules[j].children.length > 0) {
              return modules[j].children[0].path
            }
          }
        }
      }

      return '/'
    }
  }
]
