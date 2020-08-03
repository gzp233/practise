import layout from '@/components/common/layout'

const baseinfoRouter = {
  path: '/baseinfo',
  name: '基本信息',
  meta: {
    roles: ['admin'],
    permission: 'A1D900F5C06D6C0ECABA678BF182D0ED'
  },
  component: layout,
  redirect: '/baseinfo/customer_manage',
  children: [{
    path: "/baseinfo/customer_manage",
    name: "客户列表",
    meta: {
      roles: ['admin'],
      permission: '090841164BE1919CFBEB53524E27F0B4'
    },
    component: () => import('@/components/baseinfo/customer/customer')
  }, {
    path: '/baseinfo/company',
    name: '公司列表',
    meta: {
      roles: ['admin'],
      permission: 'CC14828FD389A4C8355BD86DB6F67F2E'
    },
    component: () => import('@/components/baseinfo/company/company')
  }, {
    path: '/baseinfo/brand',
    name: '品牌列表',
    meta: {
      roles: ['admin'],
      permission: 'B0D021DC14D003F27BD9412128E03F82'
    },
    component: () => import('@/components/baseinfo/brand/brand')
  }, {
    path: '/baseinfo/deliver',
    name: '送货地列表',
    meta: {
      roles: ['admin'],
      permission: '90273E65E45DE3407AC52264B8DDE58F'
    },
    component: () => import('@/components/baseinfo/deliver/deliver')
  }, {
    path: '/baseinfo/flg',
    name: '产品区分列表',
    meta: {
      roles: ['admin'],
      permission: 'BA3FD0700049E5F12DA8DF42F9147136'
    },
    component: () => import('@/components/baseinfo/flg/flg')
  }, {
    path: '/baseinfo/product',
    name: '产品列表',
    meta: {
      roles: ['admin'],
      permission: '9E85D5486B0AA6E441B660129FB025F5'
    },
    component: () => import('@/components/baseinfo/product/product')
  }, {
    path: '/baseinfo/reason',
    name: '订单原因列表',
    meta: {
      roles: ['admin'],
      permission: 'B84A0DA2DA7E32AFDFEF890064782A5C'
    },
    component: () => import('@/components/baseinfo/reason/reason')
  }, {
    path: '/baseinfo/rejected',
    name: '返品原因列表',
    meta: {
      roles: ['admin'],
      permission: '4587454B3AC70F14D55171F786B5D703'
    },
    component: () => import('@/components/baseinfo/rejected/rejected')
  }, {
    path: '/baseinfo/series',
    name: '产品系列列表',
    meta: {
      roles: ['admin'],
      permission: '61E25E09832AB6F878D491415D1D637C'
    },
    component: () => import('@/components/baseinfo/series/series')
  }, {
    path: '/baseinfo/vendor',
    name: '供应商列表',
    meta: {
      roles: ['admin'],
      permission: 'F21A729B835EA1FD6BD22C748E74CB8C'
    },
    component: () => import('@/components/baseinfo/vendor/vendor')
  }
  ]
}

export default baseinfoRouter
