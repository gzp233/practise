import layout from '@/components/common/layout'

const management = {
  path: '/management',
  name: '后台管理',
  meta: {
    roles: ['admin'],
    permission: 'D369BAD80CC23260EE99B52963C189CA'
  },
  component: layout,
  redirect: '/management/goodslist',
  children: [
    {
      path: '/management/goodslist',
      name: '商品库存修改记录列表',
      meta: {
        roles: ['admin'],
        permission: 'CB7570DC39A0DB6B57EA81E731C48C58'
      },
      component: () => import('@/components/management/management/goodslist')
    },
    {
    path: '/management/management',
    name: '商品库存修改',
    meta: {
      roles: ['admin'],
      permission: 'CE0AF761D3CA11F0E81D579268322980'
    },
    component: () => import('@/components/management/management/management')
   }
  ]
}

export default management
