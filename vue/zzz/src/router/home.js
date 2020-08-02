import Layout from '@/layout';

const homeRouter = [
  {
    path: '/',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '',
        component: () => import('@/views/home/index')
      },
      {
        path: 'imageDirectory/:id',
        component: () => import('@/views/image/index')
      },
      {
        path: 'postCategory/:id',
        component: () => import('@/views/post/category')
      },
      {
        path: 'postTag/:id',
        component: () => import('@/views/post/tag')
      },
      {
        path: 'post/:id',
        component: () => import('@/views/post/index')
      },
      {
        path: 'chatroom',
        component: () => import('@/views/chatroom/index')
      }
    ]
  },
  {
    path: '/login',
    component: () => import('@/views/auth/login'),
    hidden: true
  },
  {
    path: '/register',
    component: () => import('@/views/auth/register'),
    hidden: true
  },
  {
    path: '*',
    redirect: '/',
    hidden: true
  }
];

export default homeRouter;
