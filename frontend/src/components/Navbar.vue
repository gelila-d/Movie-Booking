<template>
  <nav class="relative backdrop-blur-xl bg-slate-900/70 border-b border-blue-500/20 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-cyan-500/10"></div>
    
    <div class="relative flex items-center space-x-8">
      <router-link to="/movies" class="neon-text text-2xl font-bold tracking-wider font-orbitron">
        CINEMAX
      </router-link>
      <div class="hidden md:flex space-x-6">
        <router-link 
          to="/movies" 
          class="nav-link text-slate-300 hover:text-blue-400 transition-all duration-300 text-sm font-medium relative" 
          active-class="text-blue-400 nav-link-active"
        >
          MOVIES
        </router-link>
        <router-link 
          v-if="user" 
          to="/my-bookings" 
          class="nav-link text-slate-300 hover:text-blue-400 transition-all duration-300 text-sm font-medium relative" 
          active-class="text-blue-400 nav-link-active"
        >
          MY BOOKINGS
        </router-link>
        <router-link 
          v-if="user?.is_admin" 
          to="/admin/movies" 
          class="nav-link text-slate-300 hover:text-blue-400 transition-all duration-300 text-sm font-medium relative" 
          active-class="text-blue-400 nav-link-active"
        >
          ADMIN
        </router-link>
      </div>
    </div>

    <div class="relative flex items-center space-x-4">
      <template v-if="user">
        <div class="hidden sm:flex items-center space-x-2 bg-slate-800/50 px-3 py-2 rounded-full border border-slate-600/50">
          <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
          <span class="text-sm text-slate-300">{{ user.name }}</span>
        </div>
        <button 
          @click="logout" 
          class="btn-secondary text-sm px-6 py-2 border border-red-500/50 hover:border-red-400 hover:bg-red-500/10 text-red-400 hover:text-red-300 transition-all duration-300"
        >
          LOGOUT
        </button>
      </template>
      <template v-else>
        <router-link 
          to="/login" 
          class="text-sm text-slate-300 hover:text-blue-400 transition-colors font-medium px-4 py-2 rounded-lg hover:bg-blue-500/10"
        >
          LOGIN
        </router-link>
        <router-link 
          to="/register" 
          class="btn-primary text-sm px-6 py-2 relative overflow-hidden"
        >
          REGISTER
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
.nav-link::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #3b82f6, #06b6d4);
  transition: width 0.3s ease;
}

.nav-link:hover::after,
.nav-link-active::after {
  width: 100%;
}

.font-orbitron {
  font-family: 'Orbitron', monospace;
}
</style>