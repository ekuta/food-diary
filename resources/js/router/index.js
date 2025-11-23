import { createRouter, createWebHistory } from "vue-router";
import { useUserStore } from '@/stores/user';
import { formatDate } from '@/utils';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: { name: 'diary', params: { date: formatDate() } },
    },
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
      path: "/diary/:date",
      name: "diary",
      component: () => import("../views/DiaryView.vue"),
    },
    {
      path: "/edit-diary/:date/:mealType",
      name: "edit diary",
      component: () => import("../views/EditDiaryView.vue"),
    },
    {
      path: "/select-food",
      name: "select food",
      component: () => import("../views/SelectFoodView.vue"),
    },
    {
      path: "/select-recipe/:date/:mealType",
      name: "select recipe",
      component: () => import("../views/SelectRecipeView.vue"),
    },
    {
      path: "/edit-recipe/:date/:mealType",
      name: "edit recipe",
      component: () => import("../views/EditRecipeView.vue"),
    },
    {
      path: "/register-food/:id",
      name: "register-food",
      component: () => import("../views/RegisterFoodView.vue"),
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
  if ((to.name == 'login' || to.name == 'register') && userStore.isLogin()) {
    next({ name: 'diary', params: { date: formatDate() } });
  } else {
    next();
  }
});


export default router;
