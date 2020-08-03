import layout from '@/components/common/layout'

const storageActionRouter = {
  path: '/storage_action',
  name: '库内作业',
  meta: {
    roles: ['admin'],
    permission: 'D69E7314DEBE2FE5B8384D8C061285B6'
  },
  component: layout,
  redirect: '/storage_action/goods_stock',
  children: [{
    path: "/storage_action/goods_stock",
    name: "商品库存",
    meta: {
      roles: ['admin'],
      permission: 'F8C1C778BC0D15D5D78DC9F7821B091F'
    },
    component: () => import('@/components/storageaction/goodsstock/goodsStock')
  }, {
    path: "/storage_action/enter",
    name: "入库汇总",
    meta: {
      roles: ['admin'],
      permission: '09918D1675A600F7EAAA78ED0DFA727A'
    },
    component: () => import('@/components/storageaction/statistics/enter')
  }, {
    path: "/storage_action/appear",
    name: "出库汇总",
    meta: {
      roles: ['admin'],
      permission: 'C390E8CF7402798D824D500AE0802D3B'
    },
    component: () => import('@/components/storageaction/statistics/appear')
  },
    {
      path: "/storage_action/frost",
      name: "库存冻结",
      meta: {
        roles: ['admin'],
        permission: '7932C37DF6838A36780F4F797E62CC7D'
      },
      component: () => import('@/components/storageaction/frost/frost')
    },
    {
      path: "/storage_action/frost/detail/:id",
      name: "库存解冻详情",
      meta: {
        roles: ['admin'],
        permission: '7E0DE2B614F87641BFD60B405EF6D8A7'
      },
      component: () => import('@/components/storageaction/frost/detail')
    },
    {
      path: "/storage_action/check",
      name: "全仓/区域盘点",
      meta: {
        roles: ['admin'],
        permission: '7731FB0FE5DE9974F22BC6C3DECFBA19'
      },
      component: () => import('@/components/storageaction/check/check')
    },
    {
      path: "/storage_action/check/shopping/:id",
      name: "全仓/区域盘点购物车",
      meta: {
        roles: ['admin'],
        permission: 'E82B8DD5AD3D498EBF7217E71285296F'
      },
      component: () => import('@/components/storageaction/check/shopping')
    },
    {
      path: "/storage_action/check/detail/:id",
      name: "全仓/区域盘点详情",
      meta: {
        roles: ['admin'],
        permission: '6939E419F1C1E56AE591AC913A80AB09'
      },
      component: () => import('@/components/storageaction/check/detail')
    },
    {
      path: "/storage_action/check/list/:id",
      name: "全仓/区域盘点详情页",
      meta: {
        roles: ['admin'],
        permission: 'E92CAE701D390F0E2693DB9041858330'
      },
      component: () => import('@/components/storageaction/check/list')
    },

    {
      path: "/storage_action/check/particulars/:id",
      name: "全仓/区域盘点详情页详情",
      meta: {
        roles: ['admin'],
        permission: '1A13BB7EAC07C7918DEAA50484A828E1'
      },
      component: () => import('@/components/storageaction/check/particulars')
    },
    {
      path: "/storage_action/diff_check",
      name: "异动盘点",
      meta: {
        roles: ['admin'],
        permission: '45BF4C2EA0325A1423597554D9077EFA'
      },
      component: () => import('@/components/storageaction/diff_check/diff_check')
    },
    {
      path: "/storage_action/diff_check/detail/:id",
      name: "异动盘点详情",
      meta: {
        roles: ['admin'],
        permission: '0DF5EB007A8830EC58F8308C517F9F62'
      },
      component: () => import('@/components/storageaction/diff_check/detail')
    },
    {
      path: "/storage_action/diff_check/list/:id",
      name: "异动盘点详情页",
      meta: {
        roles: ['admin'],
        permission: '0F403770C4A7B992D0071CCD00F6526E'
      },
      component: () => import('@/components/storageaction/diff_check/list')
    },
    {
      path: "/storage_action/diff_check/particulars/:id",
      name: "异动盘点详情页详情",
      meta: {
        roles: ['admin'],
        permission: '8713C00B53535B96EEF7D40E79669670'
      },
      component: () => import('@/components/storageaction/diff_check/particulars')
    },
    {
      path: "/storage_action/diff_check/shopping/:id",
      name: "异动盘点购物车",
      meta: {
        roles: ['admin'],
        permission: '8F0EC58BE98706F2DCDF3A85527B5F6F'
      },
      component: () => import('@/components/storageaction/diff_check/shopping')
    },
    {
      path: "/storage_action/move_stock2",
      name: "库内移动",
      meta: {
        roles: ['admin'],
        permission: '0F9AC4C003655D37AA90E51859AE826D'
      },
      component: () => import('@/components/storageaction/moveshop/movestock')
    },
    {
      path: "/storage_action/moveshop/goodslist",
      name: "库内移动列表",
      meta: {
        roles: ['admin'],
        permission: 'E02DF6E98A557A96BA1BBB83406BA334'
      },
      component: () => import('@/components/storageaction/moveshop/goodslist')
    },
    {
      path: "/storage_action/moveshop/detail",
      name: "库内移动购物车",
      meta: {
        roles: ['admin'],
        permission: 'ED03DC85020D496CB2DD159869B88A9E'
      },
      component: () => import('@/components/storageaction/moveshop/detail')
    },
  ]
}

export default storageActionRouter
