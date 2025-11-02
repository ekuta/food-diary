import { createRouter, createWebHistory } from "vue-router";

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
      path: "/diary",
      name: "diary",
      component: () => import("../views/DiaryView.vue"),
    },
  ],
});

export default router;
