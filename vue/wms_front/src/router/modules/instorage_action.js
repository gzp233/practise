import layout from '@/components/common/layout'

const instorageActionRouter = {
  path: '/instorage_action',
  name: '入库作业',
  meta: {
    roles: ['admin'],
    permission: '753174F8638EBF52E7F4DD0C4EC8BCA7'
  },
  component: layout,
  redirect: '/instorage_action/procurement_storage',
  children: [{
      path: "/instorage_action/procurement_storage",
      name: "采购入库",
      meta: {
        roles: ['admin'],
        permission: '074BDEC0E56F394D9D56F2142CA3D409'
      },
      component: () => import('@/components/instorageaction/procurementstorage/procurementStorage')
    }, {
      path: "/instorage_action/procurement_storage/detail/:id",
      name: "采购入库详情",
      meta: {
        roles: ['admin'],
        permission: '4BBA43A4756F98A389E0EF3BA92A913C'
      },
      component: () => import('@/components/instorageaction/procurementstorage/detail')
    }, {
      path: "/instorage_action/procurement_storage/stockIn/:id",
      name: "采购入库交接",
      meta: {
        roles: ['admin'],
        permission: 'D9579D865AFCC1292A9D6062949B3060'
      },
      component: () => import('@/components/instorageaction/procurementstorage/detail')
    }, {
      path: "/instorage_action/procurement_storage/confirm/:id",
      name: "采购入库确认",
      meta: {
        roles: ['admin'],
        permission: '938EC0D7A1077D93258EBC2D3083126D'
      },
      component: () => import('@/components/instorageaction/procurementstorage/confirm')
    }, {
      path: "/instorage_action/sales_returns_warehousing",
      name: "退货入库",
      meta: {
        roles: ['admin'],
        permission: '3A12A39B51126775C94353B45AAE3580'
      },
      component: () => import('@/components/instorageaction/salesreturnswarehousing/salesReturnsWarehousing')
    }, {
      path: "/instorage_action/sales_returns_warehousing/detail/:id",
      name: "退货入库详情",
      meta: {
        roles: ['admin'],
        permission: 'DA738463FD80E47683774F6CA2565CA4'
      },
      component: () => import('@/components/instorageaction/salesreturnswarehousing/detail')
    }, {
      path: "/instorage_action/sales_returns_warehousing/stockIn/:id",
      name: "退货入库交接",
      meta: {
        roles: ['admin'],
        permission: 'F67E16742FC2174B745A16B8AAB15E81'
      },
      component: () => import('@/components/instorageaction/salesreturnswarehousing/detail')
    }, {
      path: "/instorage_action/sales_returns_warehousing/confirm/:id",
      name: "退货入库确认",
      meta: {
        roles: ['admin'],
        permission: '2E1E0F116B285B6528CE8BA4BE8C6408'
      },
      component: () => import('@/components/instorageaction/salesreturnswarehousing/confirm')
    }, {
      path: "/instorage_action/inner_storage",
      name: "内向交货入库",
      meta: {
        roles: ['admin'],
        permission: 'BF9866C3F8AE6F47A2E31364CAB79FB4'
      },
      component: () => import('@/components/instorageaction/innerstorage/innerstorage')
    },
    {
      path: "/instorage_action/inner_storage/detail/:id",
      name: "内向交货入库详情",
      meta: {
        roles: ['admin'],
        permission: '49F02518AA3FFB29818F032E990B911C'
      },
      component: () => import('@/components/instorageaction/innerstorage/detail')
    },
    {
      path: "/instorage_action/inner_storage/stockIn/:id",
      name: "内向交货入库交接",
      meta: {
        roles: ['admin'],
        permission: '1AED460CAA8649AEF9C9592D74A3DD8E'
      },
      component: () => import('@/components/instorageaction/innerstorage/detail')
    }, {
      path: "/instorage_action/inner_storage/confirm/:id",
      name: "内向交货入库确认",
      meta: {
        roles: ['admin'],
        permission: '808A2B940FD5D05B606CEEA2AA6F6B85'
      },
      component: () => import('@/components/instorageaction/innerstorage/confirm')
    },
    {
      path: "/instorage_action/movement_storage",
      name: "移动入库",
      meta: {
        roles: ['admin'],
        permission: '31CB76D14D63AF5E45AD39740BA237AE'
      },
      component: () => import('@/components/instorageaction/movementstorage/movementstorage')
    }, {
      path: "/instorage_action/movement_storage/detail/:id",
      name: "移动入库详情",
      meta: {
        roles: ['admin'],
        permission: 'BD7EB281DFB4C4E8FBA6B484288543A1'
      },
      component: () => import('@/components/instorageaction/movementstorage/detail')
    }, {
      path: "/instorage_action/movement_storage/stockIn/:id",
      name: "移动入库交接",
      meta: {
        roles: ['admin'],
        permission: 'F5DF7DE07814849D0F471EB0793216DF'
      },
      component: () => import('@/components/instorageaction/movementstorage/detail')
    }, {
      path: "/instorage_action/movement_storage/confirm/:id",
      name: "移动入库确认",
      meta: {
        roles: ['admin'],
        permission: '7C72AD05A5B158DA19D31F075DF6819F'
      },
      component: () => import('@/components/instorageaction/movementstorage/confirm')
    },{
      path: "/instorage_action/adjust",
      name: "入库在库调整",
      meta: {
        roles: ['admin'],
        permission: 'E250AFBA1B86BD993248F7090281469F'
      },
      component: () => import('@/components/instorageaction/adjust/adjust')
    }, {
      path: "/instorage_action/adjust/detail/:id",
      name: "入库在库调整详情",
      meta: {
        roles: ['admin'],
        permission: '7E9717FAE62F897B02069AE2B6FE0E12'
      },
      component: () => import('@/components/instorageaction/adjust/detail')
    }, {
      path: "/instorage_action/adjust/stockIn/:id",
      name: "入库在库调整交接",
      meta: {
        roles: ['admin'],
        permission: 'AF9553AA443263C7F47273C88B4BE527'
      },
      component: () => import('@/components/instorageaction/adjust/detail')
    }, {
      path: "/instorage_action/adjust/confirm/:id",
      name: "入库在库调整确认",
      meta: {
        roles: ['admin'],
        permission: 'BA02BFECAC8B0EBA1BEB16E57A0A7F2B'
      },
      component: () => import('@/components/instorageaction/adjust/confirm')
    }, {
      path: "/instorage_action/instorage_process",
      name: "入库加工",
      meta: {
        roles: ['admin'],
        permission: '1DBAAA6F8720DDD59C1F8C9B286D0865'
      },
      component: () => import('@/components/instorageaction/instorageprocess/instorageprocess')
    }, {
      path: "/instorage_action/instorage_process/list",
      name: "加工区列表",
      meta: {
        roles: ['admin'],
        permission: 'F5361C437301ACDF4A475EB490558D48'
      },
      component: () => import('@/components/instorageaction/instorageprocess/list')
    }, {
      path: "/instorage_action/instorage_process/detail",
      name: "加工上架",
      meta: {
        roles: ['admin'],
        permission: '8CBD4E34D7F2A246D81944FF309A7A83'
      },
      component: () => import('@/components/instorageaction/instorageprocess/detail')
    }
  ]
}

export default instorageActionRouter
