<template>
  <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
    <div class="flex items-center space-x-8">
      <router-link :to="user ? '/movies' : '/login'" class="text-xl font-bold text-white tracking-tight">
        MovieBooking
      </router-link>
      <div v-if="user" class="hidden md:flex space-x-6">
        <router-link to="/movies" class="text-gray-400 hover:text-white transition-colors text-sm font-medium" active-class="text-white">Movies</router-link>
        <router-link to="/my-bookings" class="text-gray-400 hover:text-white transition-colors text-sm font-medium" active-class="text-white">My Bookings</router-link>
        <router-link v-if="user?.is_admin" to="/admin/movies" class="text-gray-400 hover:text-white transition-colors text-sm font-medium" active-class="text-white">Admin</router-link>
      </div>
    </div>

    <div class="flex items-center space-x-4">
      <template v-if="user">
        <span class="text-sm text-gray-400 hidden sm:block">Welcome, {{ user.name }}</span>
        <button @click="logout" class="text-sm text-white bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg transition-colors border border-gray-700">
          Logout
        </button>
      </template>
      <template v-else>
        <router-link to="/login" class="text-sm text-gray-400 hover:text-white transition-colors">Login</router-link>
        <router-link to="/register" class="text-sm text-white bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-lg transition-colors font-medium">
          Register
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
const user = ref(JSON.parse(localStorage.getItem("user")) || null);

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
        user.value = JSON.parse(localStorage.getItem("user"));
    });
    
    setInterval(() => {
        const storedUser = JSON.parse(localStorage.getItem("user"));
        if (JSON.stringify(storedUser) !== JSON.stringify(user.value)) {
            user.value = storedUser;
        }
    }, 1000);
});
</script>

<style scoped>
/* No @apply here to avoid build errors and keep it simple */
</style>