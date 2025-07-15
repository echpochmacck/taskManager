import { createRouter, createWebHistory } from "vue-router";
import Register from "../views/Register.vue";
import NotFound from "../views/NotFound.vue";
import Login from "../views/Login.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
   
    {
      path: "/register",
      name: "register",
      component: () => import("../views/Register.vue"),
    },
    {
      path: "/",
      name: "login",
      component: () => import("../views/Login.vue"),
    },
    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: () => import("../views/NotFound.vue"),
    },
  ],
});

export default router;
