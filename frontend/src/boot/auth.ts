// src/boot/auth.ts
import { boot } from 'quasar/wrappers';

const GUEST_LANDING_ROUTE = '/auth/login';

export default boot(({ router }) => {
  router.beforeEach((to, from, next) => {
    // Cek apakah ada token di localStorage
    const isAuthenticated = !!localStorage.getItem('authToken');

    // Cek apakah rute tujuan membutuhkan autentikasi
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    if (requiresAuth && !isAuthenticated) {
      // Jika butuh login tapi tidak ada token, lempar ke halaman login
      next(GUEST_LANDING_ROUTE);
    } else {
      // Jika tidak butuh login, atau sudah punya token, lanjutkan
      next();
    }
  });
});
