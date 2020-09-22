import barcodeLayout from '@/components/barcode/layout'

const barcodeRouter = {
  path: '/barcode',
  name: '扫描条码',
  meta: {
    roles: ['admin'],
    permission: 'F0104BA0DA57416163D3BEE833642E5B'
  },
  component: barcodeLayout,
  redirect: '/barcode/menu',
  children: [{
    path: "/barcode/menu",
    name: "扫码菜单",
    meta: {
      roles: ['admin'],
      permission: 'F0104BA0DA57416163D3BEE833642E5B'
    },
    component: () => import('@/components/barcode/scanMenu')
  }, {
    path: "/barcode/reivew",
    name: "出库复核",
    meta: {
      roles: ['admin'],
      permission: 'E30F4F07BA567B4251E5FCB8FC91C7CE'
    },
    component: () => import('@/components/barcode/reivew')
  }, {
    path: "/barcode/detail/:id",
    name: "出库复核详情",
    meta: {
      roles: ['admin'],
      permission: ''
    },
    component: () => import('@/components/barcode/detail')
  }, {
    path: "/barcode/fuheErrors/:id",
    name: "出库复核详情",
    meta: {
      roles: ['admin'],
      permission: ''
    },
    component: () => import('@/components/barcode/errors')
  }, {
    path: "/barcode/jianhuo",
    name: "出库拣货",
    meta: {
      roles: ['admin'],
      permission: '0224D36D7244A43220EBEEA2ABCECF47'
    },
    component: () => import('@/components/barcode/jianhuo/jianhuo')
  }, {
    path: "/barcode/jianhuo/stockList/:id",
    name: "拣货库位列表",
    meta: {
      roles: ['admin'],
      permission: ''
    },
    component: () => import('@/components/barcode/jianhuo/stockList')
  }, {
    path: "/barcode/jianhuoDetail",
    name: "拣货库位详情",
    meta: {
      roles: ['admin'],
      permission: ''
    },
    component: () => import('@/components/barcode/jianhuo/detail')
  },
    {
      path: "/barcode/jihuo",
      name: "集货扫码",
      meta: {
        roles: ['admin'],
        permission: '8DBFC6FB150BC863657F10D315F02B23'
      },
      component: () => import('@/components/barcode/jihuo/jihuo')
    },
    {
      path: "/barcode/jihuo/stockList/:id",
      name: "集货库位列表",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/jihuo/stockList')
    },
    {
      path: "/barcode/jihuoDetail",
      name: "集货库位详情",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/jihuo/detail')
    },
    {
      path: "/barcode/bozhong",
      name: "播种扫码",
      meta: {
        roles: ['admin'],
        permission: 'B8104099424DB41C0678B76C15420E47'
      },
      component: () => import('@/components/barcode/bozhong/bozhong')
    },

    {
      path: "/barcode/bozhong/stockList/:id",
      name: "播种产品列表",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/bozhong/stockList')
    },
    {
      path: "/barcode/bozhongDetail",
      name: "拣货库位详情",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/bozhong/detail')
    },
    {
      path: "/barcode/movement",
      name: "移库扫码",
      meta: {
        roles: ['admin'],
        permission: '92E8751E5401CF349D84DB2C66182BEA'
      },
      component: () => import('@/components/barcode/movement/movement')
    },
    {
      path: "/barcode/movement/taskout",
      name: "任务出",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/taskout')
    },
    {
      path: "/barcode/movement/detailout",
      name: "详细任务出",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/detailout')
    },
    {
      path: "/barcode/movement/detailin",
      name: "详细任务入",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/detailin')
    },
    {
      path: "/barcode/movement/createout",
      name: "创建任务出",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/createout')
    },
    {
      path: "/barcode/movement/createin",
      name: "创建任务入",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/createin')
    },
    {
      path: "/barcode/movement/createoutdet",
      name: "创建任务出详情",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/createoutdet')
    },
    {
      path: "/barcode/movement/createindet",
      name: "创建任务入详情",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/movement/createindet')
    },
    {
      path: "/barcode/fangchuanhuo",
      name: "防串货扫码",
      meta: {
        roles: ['admin'],
        permission: '89D8DC7E46F5F6994B7FF154AC79F4A3'
      },
      component: () => import('@/components/barcode/fangchuanhuo/panel')
    },
    {
      path: "/barcode/fangchuanhuo/detail/:id",
      name: "防串货详情",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/fangchuanhuo/detail')
    },
    {
      path: "/barcode/pandian",
      name: "盘点扫码",
      meta: {
        roles: ['admin'],
        permission: '6EAE5933379EB916FD46CCC29EEAB87A'
      },
      component: () => import('@/components/barcode/pandian/pandian')
    },
    {
      path: "/barcode/pandian/stockList/:id",
      name: "播种产品列表",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/pandian/stockList')
    },
    {
      path: "/barcode/pandianDetail",
      name: "盘点库位详情",
      meta: {
        roles: ['admin'],
        permission: ''
      },
      component: () => import('@/components/barcode/pandian/detail')
    },
  ]
}

export default barcodeRouter
