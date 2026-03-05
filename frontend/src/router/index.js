import { createRouter, createWebHistory } from "vue-router";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import Movies from "../pages/Movies.vue";
import MovieDetails from "../pages/MovieDetails.vue";
import MyBookings from "../pages/MyBookings.vue";
import AdminMovies from "../pages/AdminMovies.vue";

const routes = [
  { path: "/", redirect: "/login" },
  { path: "/login", component: Login, meta: { guest: true } },
  { path: "/register", component: Register, meta: { guest: true } },
  { path: "/movies", component: Movies, meta: { requiresAuth: true } },
  { path: "/movies/:id", component: MovieDetails, meta: { requiresAuth: true } },
  { path: "/my-bookings", component: MyBookings, meta: { requiresAuth: true } },
  { path: "/admin/movies", component: AdminMovies, meta: { requiresAuth: true, requiresAdmin: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  const user = JSON.parse(localStorage.getItem("user")) || null;

  if (to.meta.requiresAuth && !token) {
    next("/login");
  } else if (to.meta.guest && token) {
    next("/movies");
  } else if (to.meta.requiresAdmin && (!user || !user.is_admin)) {
    next("/movies");
  } else {
    next();
  }
});

export default router;