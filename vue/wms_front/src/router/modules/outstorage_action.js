import layout from '@/components/common/layout'

const outstorageActionRouter = {
  path: '/outstorage_action',
  name: '出库作业',
  meta: {
    roles: ['admin'],
    permission: '7BA525D1784B7F364F09535016166A72'
  },
  component: layout,
  redirect: '/outstorage_action/sell_out',
  children: [{
    path: "/outstorage_action/sell_out",
    name: '受注出库',
    meta: {
      roles: ['admin'],
      permission: '1F1E6E615587530B99C8F0B4C89AA0C9'
    },
    component: () => import('@/components/outstorageaction/sellout/sellOut')
  }, {
    path: "/outstorage_action/sell_out/detail/:id",
    name: '受注出库详情',
    meta: {
      roles: ['admin'],
      permission: '5C4671EE84BFF3D484AE58CA795213AD'
    },
    component: () => import('@/components/outstorageaction/sellout/detail')
  }, {
    path: "/outstorage_action/sell_out/out/:id",
    name: '受注出库处理',
    meta: {
      roles: ['admin'],
      permission: 'F395B47FD3199EF043BD0C03B88D19F9'
    },
    component: () => import('@/components/outstorageaction/sellout/out')
  }, {
    path: "/outstorage_action/move_out",
    name: '移动出库',
    meta: {
      roles: ['admin'],
      permission: '24F0BF1CCF72AC7C55737AF28913186A'
    },
    component: () => import('@/components/outstorageaction/moveout/moveout')
  }, {
    path: "/outstorage_action/move_out/detail/:id",
    name: '移动出库详情',
    meta: {
      roles: ['admin'],
      permission: '260D74E7B4493DF57734C162D2EB9D1F'
    },
    component: () => import('@/components/outstorageaction/moveout/detail')
  }, {
    path: "/outstorage_action/move_out/out/:id",
    name: '移动出库处理',
    meta: {
      roles: ['admin'],
      permission: '58C53175ED705719A4AE1F5BA1D75DB6'
    },
    component: () => import('@/components/outstorageaction/moveout/out')
  },
    {
      path: "/outstorage_action/consolidation",
      name: '集货',
      meta: {
        roles: ['admin'],
        permission: '5CE587C99A9C2B63AAB24271060768AA'
      },
      component: () => import('@/components/outstorageaction/consolidation/consolidation')
    },
    {
      path: "/outstorage_action/detail",
      name: '集货列表',
      meta: {
        roles: ['admin'],
        permission: '0771E48AD9B0B817E058B2254DB46E4F'
      },
      component: () => import('@/components/outstorageaction/consolidation/detail')
    },
    {
      path: "/outstorage_action/adjust",
      name: '出库在库调整',
      meta: {
        roles: ['admin'],
        permission: 'D25679399C1721F803A50443CB9647E6'
      },
      component: () => import('@/components/outstorageaction/adjust/adjust')
    }, {
      path: "/outstorage_action/adjust/detail/:id",
      name: '出库在库调整详情',
      meta: {
        roles: ['admin'],
        permission: '09FA2CEF0D3353474B441EA0BB74F78C'
      },
      component: () => import('@/components/outstorageaction/adjust/detail')
    }, {
      path: "/outstorage_action/adjust/out/:id",
      name: '出库在库调整处理',
      meta: {
        roles: ['admin'],
        permission: 'A4F91B75BF19259FB738D8BE79B27286'
      },
      component: () => import('@/components/outstorageaction/adjust/out')
    }, {
      path: "/outstorage_action/anticode",
      name: '防串货',
      meta: {
        roles: ['admin'],
        permission: '135ABD17AC2A072DAA3EE9AB0BE1AC87'
      },
      component: () => import('@/components/outstorageaction/anticode/anticode')
    }
  ]
}

export default outstorageActionRouter
