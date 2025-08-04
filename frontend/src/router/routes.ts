import type { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/AuthenticatedLayout.vue'), // <-- Layout Utama Aplikasi
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
      {
        path: '/admin/dashboard',
        component: () => import('pages/admin/AdminDashboardPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: '/admin/product-categories',
        component: () => import('pages/admin/product-category/IndexPage.vue'),
        meta: { requiresAuth: true },
      },
      //     {
      //       path: '/admin/customers',
      //       component: () => import('pages/admin/customer/Index.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/customers/add',
      //       component: () => import('pages/admin/customer/Editor.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/customers/edit/:id', // Rute edit ditambahkan
      //       component: () => import('pages/admin/customer/Editor.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/customers/detail/:id',
      //       component: () => import('pages/admin/customer/Detail.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/wallet-transactions',
      //       component: () => import('pages/admin/customer-wallet-transaction/Index.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/wallet-transactions/adjustment',
      //       component: () => import('pages/admin/customer-wallet-transaction/Detail.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/finance-accounts',
      //       component: () => import('pages/admin/finance-account/Index.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/finance-accounts/add',
      //       component: () => import('pages/admin/finance-account/Editor.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/finance-accounts/edit/:id', // Rute edit ditambahkan
      //       component: () => import('pages/admin/finance-account/Editor.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/finance-accounts/detail/:id',
      //       component: () => import('pages/admin/finance-account/Detail.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/products',
      //       component: () => import('pages/admin/product/Index.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/products/add',
      //       component: () => import('pages/admin/product/Editor.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/products/edit/:id', // Rute edit ditambahkan
      //       component: () => import('pages/admin/product/Editor.vue'),
      //       meta: { requiresAuth: true },
      //     },
      //     {
      //       path: '/admin/products/detail/:id',
      //       component: () => import('pages/admin/product/Detail.vue'),
      //       meta: { requiresAuth: true },
      //     },
    ],
  },
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'), // <-- Layout Khusus Login
    children: [
      { path: '', redirect: '/auth/login' },
      { path: 'login', component: () => import('pages/auth/LoginPage.vue') },
      { path: 'register', component: () => import('pages/auth/RegisterPage.vue') },
      { path: 'forgot-password', component: () => import('pages/auth/ForgotPasswordPage.vue') },
      {
        path: 'reset-password/:token',
        component: () => import('pages/auth/ResetPasswordPage.vue'),
        props: true,
      },
    ],
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
