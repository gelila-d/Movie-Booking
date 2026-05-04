<template>
  <nav class="glass sticky top-0 z-50 px-6 py-4 flex justify-between items-center shadow-lg border-b border-white/5">
    <div class="flex items-center space-x-10">
      <router-link to="/movies" class="flex items-center space-x-2 group">
        <div class="w-8 h-8 bg-gradient-to-tr from-violet-600 to-fuchsia-600 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
          <span class="text-white font-black text-lg">M</span>
        </div>
        <span class="text-xl font-extrabold text-white tracking-tight group-hover:text-violet-400 transition-colors">
          Movie<span class="accent-gradient-text">Vault</span>
        </span>
      </router-link>
      
      <div class="hidden md:flex items-center space-x-8">
        <router-link to="/movies" class="nav-link" active-class="nav-link-active">
          Movies
        </router-link>
        <router-link v-if="user" to="/my-bookings" class="nav-link" active-class="nav-link-active">
          Reservations
        </router-link>
        <router-link v-if="user?.is_admin" to="/admin/movies" class="nav-link flex items-center space-x-1.5" active-class="nav-link-active">
          <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
          <span>Admin</span>
        </router-link>
      </div>
    </div>

    <div class="flex items-center space-x-6">
      <template v-if="user">
        <div class="hidden sm:flex flex-col items-end">
          <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Logged in as</span>
          <span class="text-sm text-slate-200 font-semibold">{{ user.name }}</span>
        </div>
        <button @click="logout" class="px-4 py-2 text-sm font-bold text-slate-300 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-all">
          Sign Out
        </button>
      </template>
      <template v-else>
        <router-link to="/login" class="text-sm font-bold text-slate-400 hover:text-white transition-colors">
          Sign In
        </router-link>
        <router-link to="/register" class="btn-primary !py-2 !px-5 !text-sm">
          Get Started
        </router-link>
      </template>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";

const router = useRouter();

const getUserFromStorage = () => {
    try {
        const stored = localStorage.getItem("user");
        return (stored && stored !== 'undefined') ? JSON.parse(stored) : null;
    } catch (e) {
        return null;
    }
};

const user = ref(getUserFromStorage());

const logout = async () => {
  try {
    if (localStorage.getItem("token")) {
       await api.post("/logout");
    }
  } catch (error) {
    console.error("Logout failed", error);
  } finally {
    localStorage.removeItem("user");
    localStorage.removeItem("token");
    user.value = null;
    router.push("/login");
  }
};

onMounted(() => {
    window.addEventListener('storage', () => {
        user.value = getUserFromStorage();
    });
    
    setInterval(() => {
        const storedUser = getUserFromStorage();
        if (JSON.stringify(storedUser) !== JSON.stringify(user.value)) {
            user.value = storedUser;
        }
    }, 1000);
});
</script>

<style scoped>
.nav-link {
  @apply text-sm font-semibold text-slate-400 hover:text-white transition-all relative py-1;
}

.nav-link::after {
  content: '';
  @apply absolute bottom-0 left-0 w-0 h-0.5 bg-violet-500 transition-all duration-300;
}

.nav-link:hover::after {
  @apply w-full;
}

.nav-link-active {
  @apply text-white;
}

.nav-link-active::after {
  @apply w-full bg-gradient-to-r from-violet-500 to-fuchsia-500;
}
</style>