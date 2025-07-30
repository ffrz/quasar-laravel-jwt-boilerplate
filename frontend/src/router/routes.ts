import type { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue') },
      {
        path: '/admin/dashboard',
        component: () => import('pages/admin/AdminDashboardPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: '/admin/users',
        component: () => import('pages/admin/UserManagementPage.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        redirect: '/auth/login',
      },
      {
        // TODO: redirect ke login untuk path root dari auth
        path: 'login',
        component: () => import('pages/auth/LoginPage.vue'),
      },
      {
        path: 'register',
        component: () => import('pages/auth/RegisterPage.vue'),
      },
      {
        path: 'forgot-password',
        component: () => import('pages/auth/ForgotPasswordPage.vue'),
      },
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
