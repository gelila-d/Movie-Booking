import { createRouter, createWebHistory } from "vue-router";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import Movies from "../pages/Movies.vue";
import MovieDetails from "../pages/MovieDetails.vue";
import MyBookings from "../pages/MyBookings.vue";
import Watchlist from "../pages/Watchlist.vue";
import AdminMovies from "../pages/AdminMovies.vue";
import Home from "../pages/Home.vue";

const routes = [
  { path: "/", component: Home },
  { path: "/login", component: Login, meta: { guest: true } },
  { path: "/register", component: Register, meta: { guest: true } },
  { path: "/movies", component: Movies },
  { path: "/movies/:id", component: MovieDetails, meta: { requiresAuth: true, hideFooter: true } },
  { path: "/my-bookings", component: MyBookings, meta: { requiresAuth: true, hideFooter: true } },
  { path: "/watchlist", component: Watchlist, meta: { requiresAuth: true } },
  { path: "/admin/movies", component: AdminMovies, meta: { requiresAuth: true, requiresAdmin: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from) => {
  let token = localStorage.getItem("token");
  let user = null;

  try {
    const userStr = localStorage.getItem("user");
    if (userStr && userStr !== "undefined") {
      user = JSON.parse(userStr);
    }
  } catch (err) {
    console.error("Failed to parse user from localStorage", err);
  }

  // If we have a token but user is corrupted or missing, clear it
  if (token && !user) {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    token = null;
    if (to.meta.requiresAuth || to.meta.requiresAdmin) {
      return "/login";
    }
  }

  if (to.meta.requiresAuth && !token) {
    return "/login";
  } else if (to.meta.guest && token) {
    return "/movies";
  } else if (to.meta.requiresAdmin && (!user || !user.is_admin)) {
    return "/movies";
  }
});

export default router;