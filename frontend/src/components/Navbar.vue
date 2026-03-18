<template>
  <nav class="bg-white border-b border-yellow-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
    <div class="flex items-center space-x-8">
      <router-link to="/movies" class="text-xl font-bold text-gray-900 tracking-tight">
        MovieBooking
      </router-link>
      <div class="hidden md:flex space-x-6">
        <router-link to="/movies" class="text-gray-600 hover:text-yellow-600 transition-colors text-sm font-medium" active-class="text-yellow-600">Movies</router-link>
        <router-link v-if="user" to="/my-bookings" class="text-gray-600 hover:text-yellow-600 transition-colors text-sm font-medium" active-class="text-yellow-600">My Bookings</router-link>
        <router-link v-if="user?.is_admin" to="/admin/movies" class="text-gray-600 hover:text-yellow-600 transition-colors text-sm font-medium" active-class="text-yellow-600">Admin</router-link>
      </div>
    </div>

    <div class="flex items-center space-x-4">
      <template v-if="user">
        <span class="text-sm text-gray-600 hidden sm:block">Welcome, {{ user.name }}</span>
        <button @click="logout" class="text-sm text-yellow-800 bg-yellow-200 hover:bg-yellow-300 px-4 py-2 rounded-lg transition-colors border border-yellow-300">
          Logout
        </button>
      </template>
      <template v-else>
        <router-link to="/login" class="text-sm text-gray-600 hover:text-yellow-600 transition-colors">Login</router-link>
        <router-link to="/register" class="text-sm text-white bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded-lg transition-colors font-medium">
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
/* No @apply here to avoid build errors and keep it simple */
</style>