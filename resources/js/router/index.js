import { createRouter, createWebHistory } from "vue-router";
import { useUserStore } from '@/stores/user';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/diary' },
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
    },
    {
      path: "/register",
      name: "register",
      component: () => import("../views/RegisterView.vue"),
    },
    {
      path: "/verify-email/:id/:hash",
      name: "verify-email",
      component: () => import("../views/LoginView.vue"),
    },
    {
      path: "/diary",
      name: "diary",
      component: () => import("../views/DiaryView.vue"),
    },
    { path: '/food', redirect: '/food/0' },
    {
      path: "/food/:id",
      name: "food",
      component: () => import("../views/FoodView.vue"),
    },
    {
      path: "/mext-food",
      name: "mext-food",
      component: () => import("../views/MextFoodView.vue"),
    },
  ],
});

router.beforeEach((to, from, next) => {
  const userStore = useUserStore();
  console.log("router to: ", to);
  if ((to.name == 'login' || to.name == 'register') && userStore.isLogin()) {
    next({ name: 'diary' });
  } else {
    next();
  }
});


export default router;
