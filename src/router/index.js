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
      beforeEnter: (to, from, next) => {
        if (localStorage.getItem("token")) {
          next({ name: "tasks" });
        } else {
          next();
        }
      },
    },
    {
      path: "/tasks",
      name: "tasks",
      component: () => import("../views/Alltasks.vue"),
      beforeEnter: (to, from, next) => {
        if (localStorage.getItem("token")) {
          next();
        } else {
          next({ name: "login" });
        }
      },
    },
    {
      path: "/user-taks",
      name: "user-tasks",
      component: () => import("../views/UserTasks.vue"),
      beforeEnter: (to, from, next) => {
        if (localStorage.getItem("token")) {
          next();
        } else {
          next({ name: "login" });
        }
      },
    },
    {
      path: "/",
      name: "login",
      component: () => import("../views/Login.vue"),
      beforeEnter: (to, from, next) => {
        if (localStorage.getItem("token")) {
          next({ name: "tasks" });
        } else {
          next();
        }
      },
    },

    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: () => import("../views/NotFound.vue"),
    },
  ],
});

export default router;
