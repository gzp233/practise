import adminLayout from '@/layout/admin';

const adminRouter = [
  {
    path: '/admin',
    redirect: '/admin/dashboard',
    component: adminLayout,
    children: [
      {
        path: 'dashboard',
        name: '面板',
        component: () => import('@/views/admin/dashboard/index'),
        meta: { title: '面板', icon: 'dashboard' }
      },
      {
        path: '404',
        name: '404',
        component: () => import('@/views/admin/404'),
        hidden: true
      }
    ]
  },
  {
    path: '/admin/user',
    component: adminLayout,
    redirect: 'admin/user/index',
    children: [
      {
        path: 'index',
        name: '用户列表',
        component: () => import('@/views/admin/user/index'),
        meta: { title: '用户列表', icon: 'user' }
      }
    ]
  },
  {
    path: '/admin/image',
    component: adminLayout,
    redirect: 'admin/image/index',
    children: [
      {
        path: 'index',
        name: '相册',
        component: () => import('@/views/admin/image/index'),
        meta: { title: '相册', icon: 'image' }
      },
      {
        path: 'image/:id',
        component: () => import('@/views/admin/image/image'),
        meta: { title: '相册图片', icon: 'image' },
        hidden: true
      }
    ]
  },
  {
    path: '/admin/post',
    component: adminLayout,
    redirect: 'admin/post/list',
    meta: { title: '文章', icon: 'post' },
    children: [
      {
        path: 'list',
        name: '文章列表',
        component: () => import('@/views/admin/post/list'),
        meta: { title: '文章列表', icon: 'posts' }
      },
      {
        path: 'create',
        name: '文章创建',
        hidden: true,
        component: () => import('@/views/admin/post/create'),
        meta: { title: '文章创建', icon: 'post' }
      },
      {
        path: 'edit/:id(\\d+)',
        name: '文章编辑',
        component: () => import('@/views/admin/post/edit'),
        meta: { title: '文章编辑', icon: 'post' },
        hidden: true
      },
      {
        path: 'category',
        name: '文章分类',
        component: () => import('@/views/admin/post/category'),
        meta: { title: '文章分类', icon: 'category' }
      },
      {
        path: 'tag',
        name: '文章标签',
        component: () => import('@/views/admin/post/tag'),
        meta: { title: '文章标签', icon: 'tag' }
      },
      {
        path: 'comment/:id(\\d+)',
        name: '文章评论',
        component: () => import('@/views/admin/post/comment'),
        meta: { title: '文章标签', icon: 'tag' },
        hidden: true
      }
    ]
  },
  {
    path: '/admin/*',
    redirect: '/admin/404',
    hidden: true
  }
];

export default adminRouter;
