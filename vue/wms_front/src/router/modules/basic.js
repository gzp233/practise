import layout from '@/components/common/layout'

const basicRouter = {
  path: '/basic',
  name: '基础配置',
  meta: {
    roles: ['admin'],
    permission: 'BC06A30DF9C33F50740D3DC8837FAA64'
  },
  component: layout,
  redirect: '/basic/warehourse',
  children: [{
    path: '/basic/warehourse',
    name: '仓库管理',
    meta: {
      roles: ['admin'],
      permission: '2B2D844CFFF28452BB4ED74506923C0F'
    },
    component: () => import('@/components/basic/warehourse/warehourse')
  }, {
    path: '/basic/warehourse/add',
    name: '新增仓库',
    meta: {
      roles: ['admin'],
      permission: '8A552C220CB76749F5D70585D49D709C'
    },
    component: () => import('@/components/basic/warehourse/edite')
  }, {
    path: '/basic/warehourse/edite/:id',
    name: '仓库编辑',
    meta: {
      roles: ['admin'],
      permission: '2E09DA89866686743696A9C96913FD76'
    },
    component: () => import('@/components/basic/warehourse/edite')
  }, {
    path: '/basic/location',
    name: '库位管理',
    meta: {
      roles: ['admin'],
      permission: '5A88294519AA8B7E5EFB4E483FBB6E47'
    },
    component: () => import('@/components/basic/location/location')
  }, {
    path: '/basic/location/add',
    name: '添加库位',
    meta: {
      roles: ['admin'],
      permission: '6E4F61B6A3CC160DE87DAB12FA7E2D17'
    },
    component: () => import('@/components/basic/location/edit')
  }, {
    path: '/basic/location/edit/:id',
    name: '库位编辑',
    meta: {
      roles: ['admin'],
      permission: 'A398C3D9E3FE049C5443FFAA88086324'
    },
    component: () => import('@/components/basic/location/edit')
  }, {
    path: '/basic/area',
    name: '库区管理',
    meta: {
      roles: ['admin'],
      permission: 'C425620AB1CCC1B8AE57E4B066FFC6CB'
    },
    component: () => import('@/components/basic/area/area')
  }, {
    path: '/basic/area/add',
    name: '添加库区',
    meta: {
      roles: ['admin'],
      permission: 'AD6B6677629041B20F9C5632AE0276F3'
    },
    component: () => import('@/components/basic/area/edit')
  }, {
    path: '/basic/area/edit/:id',
    name: '库区编辑',
    meta: {
      roles: ['admin'],
      permission: 'F50AC0792B5E27C9C438FCE12289E08D'
    },
    component: () => import('@/components/basic/area/edit')
  },{
    path: '/basic/attributes',
    name: '商品属性管理',
    meta: {
      roles: ['admin'],
      permission: 'D3F678220D3D7F79285F0714EEB434A9'
    },
    component: () => import('@/components/basic/attributes/attributes')
  },{
    path: '/basic/attributes/add',
    name: '添加商品属性',
    meta: {
      roles: ['admin'],
      permission: 'F830EB973B3583D3DC9A893B1D60D645'
    },
    component: () => import('@/components/basic/attributes/edit')
  },{
    path: '/basic/attributes/edit/:id',
    name: '商品属性编辑',
    meta: {
      roles: ['admin'],
      permission: '11D3990ACEE27F66FF9DA656122E97F6'
    },
    component: () => import('@/components/basic/attributes/edit')
  }]
}

export default basicRouter
