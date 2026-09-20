import { createRouter, createWebHistory } from "vue-router";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import Movies from "../pages/Movies.vue";
import MovieDetails from "../pages/MovieDetails.vue";
import MyBookings from "../pages/MyBookings.vue";
import Watchlist from "../pages/Watchlist.vue";
import AdminMovies from "../pages/AdminMovies.vue";
import Home from "../pages/Home.vue";
import AdminLayout from "../layouts/AdminLayout.vue";

// ── helper ──────────────────────────────────────────────────────────
function getUser() {
  try {
    const u = localStorage.getItem("user");
    return u && u !== "undefined" ? JSON.parse(u) : null;
  } catch {
    return null;
  }
}

// ── routes ──────────────────────────────────────────────────────────
const routes = [
  // Public auth pages
  { path: "/login",    component: Login,    meta: { guest: true } },
  { path: "/register", component: Register, meta: { guest: true } },

  // ── Admin section (own layout, no public Navbar/Footer) ──────────
  {
    path: "/admin",
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: "",           // /admin → Dashboard (reuses AdminMovies for now)
        component: AdminMovies,
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: "movies",     // /admin/movies
        component: AdminMovies,
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: "showtimes",  // /admin/showtimes
        component: AdminMovies,
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: "cinemas",    // /admin/cinemas
        component: AdminMovies,
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: "bookings",   // /admin/bookings
        component: AdminMovies,
        meta: { requiresAuth: true, requiresAdmin: true },
      },
      {
        path: "users",      // /admin/users
        component: AdminMovies,
        meta: { requiresAuth: true, requiresAdmin: true },
      },
    ],
  },

  // ── Public / User pages ──────────────────────────────────────────
  { path: "/", component: Home },
  { path: "/movies", component: Movies },
  {
    path: "/movies/:id",
    component: MovieDetails,
    meta: { requiresAuth: true, hideFooter: true, hideNavbar: true },
  },
  {
    path: "/my-bookings",
    component: MyBookings,
    meta: { requiresAuth: true, hideFooter: true, hideNavbar: true },
  },
  { path: "/watchlist", component: Watchlist, meta: { requiresAuth: true } },

  // Catch-all
  { path: "/:pathMatch(.*)*", redirect: "/" },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// ── Navigation Guard ─────────────────────────────────────────────────
router.beforeEach((to) => {
  let token = localStorage.getItem("token");
  let user = getUser();

  // Clear broken state
  if (token && !user) {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    token = null;
    if (to.meta.requiresAuth || to.meta.requiresAdmin) return "/login";
  }

  const isAdmin = user?.is_admin === true;

  // Not logged in → login
  if (to.meta.requiresAuth && !token) return "/login";

  // Guest-only pages (login/register) while logged in
  if (to.meta.guest && token) {
    return isAdmin ? "/admin" : "/";
  }

  // Admin-only pages for non-admins
  if (to.meta.requiresAdmin && !isAdmin) return "/";

  // Block admins from public pages (home, movies, etc.) — redirect to admin
  const publicOnlyPaths = ["/", "/movies", "/watchlist"];
  if (isAdmin && token && publicOnlyPaths.some((p) => to.path === p || to.path.startsWith("/movies/"))) {
    return "/admin";
  }
});

export default router;