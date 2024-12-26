import { useAuthStore } from '@/stores/Auth'
import { createRouter, createWebHistory } from 'vue-router'

import Layout from '@/views/Layout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Dashboard Owner/Admin Routes
    {
      path: '/',
      component: Layout,
      children: [
        {
          path: 'dashboard',
          name: 'DashboardAdminOwner',
          meta: { isOwner: true },
          component: () => import('../views/Admin-Owner/Dashboard.vue'),
        },
        {
          path: 'product',
          name: 'ProductAdminOwner',
          // meta: { isOwner: true },
          component: () => import('../views/Admin-Owner/Product.vue'),
        },
        {
          path: 'transactions',
          name: 'TransactionsAdminOwner',
          meta: { isOwner: true },
          component: () => import('../views/Admin-Owner/Transactions.vue'),
        },
        {
          path: 'cashier',
          name: 'Cashier',
          meta: { isCashier: true },
          component: () => import('../views/Cashier/Cashier.vue'),
        }
      ],
    },
    // Authentication Routes
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/views/RegisterLogin/Login.vue'),
    },
    {
      path: '/chooseRole',
      name: 'ChooseRole',
      component: () => import('@/views/RegisterLogin/ChooseRole.vue'),
      meta: { isAuth: true, isLogin: true },
    },
    {
      path: '/register',
      name: 'Register',
      component: () => import('@/views/RegisterLogin/Register.vue'),
    },

    // Email Verification
    {
      path: '/verifikasiOTPEmail',
      name: 'verifikasiOTPEmail',
      component: () => import('@/views/RegisterLogin/VerifOTPAuth.vue'),
      meta: { isAuth: true, isRegister: true },
    },

    // Business Information
    {
      path: '/businessDataForm',
      name: 'businessDataForm',
      component: () => import('@/views/RegisterLogin/BusinessInfo.vue'),
      meta: { isAuth: true, isRegister: true },
    },

    // Forgot Password Routes
    {
      path: '/forgot-password',
      name: 'forgotPassword',
      component: () => import('@/views/RegisterLogin/ForgotPassword/SubmitEmail.vue'),
    },
    {
      path: '/verifikasiOTP-forgotPassword',
      name: 'OTPForgotPassword',
      component: () => import('@/views/RegisterLogin/ForgotPassword/VerifyOTPForgotPass.vue'),
      meta: { forgotPass: true },
    },
    {
      path: '/resetPassword',
      name: 'ResetPassword',
      component: () => import('@/views/RegisterLogin/ForgotPassword/ForgotPass.vue'),
      meta: { forgotPass: true },
    },
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  // Cek halaman yang membutuhkan autentikasi
  if (to.meta.isAuth && !authStore.tokenUser) {
    alert('Kamu tidak punya akses ke halaman ini!');
    next('/login'); // Gunakan next() dengan path tujuan
    return; // Pastikan keluar dari fungsi
  }

  // Cek halaman dashboard OWNER/ADMIN
  if (to.meta.isOwner && (!authStore.tokenUser || (authStore?.currentUser?.role !== 'Admin' && authStore?.currentUser?.role !== 'Owner'))) {
    alert('Kamu tidak punya akses ke halaman dashboard Admin/Owner ini!');
    next('/login');
    return;
  }

  // Cek halaman CASHIER
  if (to.meta.isCashier && (!authStore.tokenUser || authStore?.currentUser?.role !== 'Cashier')) {
    alert('Kamu tidak punya akses ke halaman dashboard Cashier ini!');
    next('/login');
    return;
  }

  // Cek Halaman Verifikasi OTP Email dan Business Information
  if (to.meta.isRegister) {
    const acessedRegister = localStorage.getItem('accessRegister');

    if (!acessedRegister) {
      next({ name: 'Register' });
      return;
    }
  }

  // Cek Halaman Login - Choose Role
  if (to.meta.isLogin) {
    const accessedLogin = localStorage.getItem('accessLogin');

    if (!accessedLogin) {
      next({ name: 'Login' });
      return;
    }
  }

  // Cek halaman forgot-password dengan flag di localStorage
  if (to.meta.forgotPass) {
    const accessedForgotPassword = localStorage.getItem('accessForgotPassword');

    if (!accessedForgotPassword) {
      // Redirect ke '/forgot-password' jika flag tidak ditemukan
      next({ name: 'forgotPassword' });
      return;
    }
  }

  next(); // Lanjutkan ke halaman tujuan jika semua kondisi lolos
});

export default router
