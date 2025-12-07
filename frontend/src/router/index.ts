import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/',
      name: 'shelf',
      component: () => import('../views/ShelfView.vue'),
      meta: { requiresAuth: true }
    },
    // Catch all
    {
      path: '/:pathMatch(.*)*',
      redirect: 'shelf'
    }
  ],
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();
  const publicPages = ['/login'];
  const authRequired = !publicPages.includes(to.path);

  if (authRequired) {
    // Try to fetch user if not present (simple check)
    if (!auth.isAuthenticated && localStorage.getItem('token')) {
      try {
        await auth.checkAuth();
      } catch (e) { }
    }

    if (!auth.isAuthenticated) {
      return next('/login');
    }
  }

  if (to.path === '/login' && auth.isAuthenticated) {
    return next('/');
  }

  next();
});

export default router
