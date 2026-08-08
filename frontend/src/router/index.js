import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: {
        guest: true,
      },
    },

    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: {
        guest: true,
      },
    },

    {
      path: "/",
      name: 'home',
      component: HomeView,
      meta: {
        requiresAuth: true,
      },
    },

    {
      path: '/search',
      name: 'search',
      component: () =>
        import('@/views/SearchView.vue'),
      meta: {
        requiresAuth: true,
      },
    },

    {
      path: '/create-post',
      name: 'create-post',
      component: () =>
        import('@/views/CreatePostView.vue'),
      meta: {
        requiresAuth: true,
      },
    },

    {
      path: '/profile',
      name: 'my-profile',
      component: () =>
        import('@/views/ProfileView.vue'),
      meta: {
        requiresAuth: true,
      },
    },

    {
      path: '/users/:username',
      name: 'profile',
      component: () =>
        import('@/views/ProfileView.vue'),
      meta: {
        requiresAuth: true,
      },
    },

    {
      path: '/posts/:id',
      name: 'post',
      component: () =>
        import('@/views/PostView.vue'),
      meta: {
        requiresAuth: true
      },
    },

    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => 
        import('@/views/NotFoundView.vue')
    },
  ],
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return { name: 'login' }
  }

  if (to.meta.guest && token) {
    return { name: 'home' }
  }
})

export default router
