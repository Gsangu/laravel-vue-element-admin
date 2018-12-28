import Vue from 'vue'
import Router from 'vue-router'

// in development-env not use lazy-loading, because lazy-loading too many pages will cause webpack hot update too slow. so only in production use lazy-loading;
// detail: https://panjiachen.github.io/vue-element-admin-site/#/lazy-loading

Vue.use(Router)

/* Layout */
import Layout from '@/views/layout/Layout'
/* Router Modules */

// import componentsRouter from './modules/components'
/**
 * hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
 *                                if not set alwaysShow, only more than one route under the children
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
  }
 **/
export const constantRouterMap = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: resolve => void (require(['@/views/redirect/index'], resolve))
      }
    ]
  },
  {
    path: '/login',
    component: resolve => void (require(['@/views/login/index'], resolve)),
    hidden: true
  },
  {
    path: '/auth-redirect',
    component: resolve => void (require(['@/views/login/authredirect'], resolve)),
    hidden: true
  },
  {
    path: '/404',
    component: resolve => void (require(['@/views/errorPage/404'], resolve)),
    hidden: true
  },
  {
    path: '/401',
    component: resolve => void (require(['@/views/errorPage/401'], resolve)),
    hidden: true
  },
  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: resolve => void (require(['@/views/dashboard/index'], resolve)),
        name: 'Dashboard',
        meta: { title: 'dashboard', icon: 'dashboard', noCache: true }
      }
    ]
  }
]

export default new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

export const asyncRouterMap = [
  {
    path: '/article',
    component: Layout,
    redirect: '/article/list',
    name: 'article',
    meta: { roles: ['admin', 'editor'], title: 'article', icon: 'documentation' },
    children: [
      {
        path: 'list',
        component: resolve => void (require(['@/views/article/list'], resolve)),
        name: 'Article',
        meta: { title: 'articleList', icon: 'documentation', noCache: true }
      },
      {
        path: 'create',
        component: resolve => void (require(['@/views/article/create'], resolve)),
        name: 'CreateArticle',
        meta: { title: 'createArticle', icon: 'edit', noCache: true }
      },
      {
        path: 'edit/:id(\\d+)',
        component: resolve => void (require(['@/views/article/edit'], resolve)),
        name: 'EditArticle',
        meta: { title: 'editArticle', noCache: true },
        hidden: true
      },
      {
        path: 'category/list',
        component: resolve => void (require(['@/views/category/list'], resolve)),
        name: 'Category',
        meta: { title: 'category', icon: 'category', noCache: true }
      }
    ]
  },
  {
    path: '/user',
    component: Layout,
    redirect: '/user/list',
    name: 'Users',
    meta: {
      title: 'Users',
      icon: 'user'
    },
    children: [
      {
        path: 'info',
        component: resolve => void (require(['@/views/user/info'], resolve)),
        name: 'User',
        meta: { title: 'User', icon: 'user', noCache: true, roles: ['admin', 'editor'] }
      },
      {
        path: 'list',
        component: resolve => void (require(['@/views/user/list'], resolve)),
        name: 'UserList',
        meta: { title: 'UserList', icon: 'peoples', noCache: true, roles: ['admin'] }
      }
    ]
  },
  {
    path: '/message',
    component: Layout,
    redirect: '/message/list',
    name: 'Messages',
    meta: {
      title: 'Messages'
    },
    children: [
      {
        path: 'list',
        component: resolve => void (require(['@/views/message/list'], resolve)),
        name: 'MessageList',
        meta: { title: 'MessageList', icon: 'message', noCache: true, roles: ['admin', 'editor'] }
      },
      {
        path: 'field-list',
        component: resolve => void (require(['@/views/field/list'], resolve)),
        name: 'FieldList',
        meta: { title: 'FieldList', icon: 'table', noCache: true, roles: ['admin'] }
      }
    ]
  },

  { path: '*', redirect: '/404', hidden: true }

]
