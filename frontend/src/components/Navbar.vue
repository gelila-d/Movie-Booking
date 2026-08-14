<template>
  <header 
    :class="[
      'fixed top-0 left-0 right-0 w-full flex items-center justify-between z-50 transition-all duration-300',
      isScrolled 
        ? 'bg-[#0f1014]/95 backdrop-blur-md py-4 px-6 md:px-10 shadow-xl border-b border-gray-800/50' 
        : 'bg-gradient-to-b from-black/80 via-black/40 to-transparent py-6 px-6 md:px-10'
    ]"
  >
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <router-link to="/" class="flex items-center space-x-2 hover:opacity-80 transition">
        <!-- Film Reel Icon mimicking the image -->
        <svg class="w-8 h-8 text-[#ef6a26]" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
          <circle cx="12" cy="12" r="2.5"/>
          <circle cx="12" cy="6.5" r="1.5"/>
          <circle cx="17.23" cy="10.3" r="1.5"/>
          <circle cx="15.23" cy="16.45" r="1.5"/>
          <circle cx="8.77" cy="16.45" r="1.5"/>
          <circle cx="6.77" cy="10.3" r="1.5"/>
        </svg>
        <span class="text-3xl font-bold tracking-tight text-white">movies</span>
      </router-link>
    </div>

    <!-- Navigation Links -->
    <nav class="hidden lg:flex items-center space-x-10 text-[15px] font-medium">
      <router-link 
        to="/" 
        class="flex items-center transition" 
        :class="[route.path === '/' ? 'text-[#ef6a26]' : 'text-white hover:text-[#ef6a26]']"
      >
        Home <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link 
        to="/movies" 
        class="flex items-center transition" 
        :class="[route.path.startsWith('/movies') ? 'text-[#ef6a26]' : 'text-white hover:text-[#ef6a26]']"
      >
        Movies <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link to="/movies" class="text-white hover:text-[#ef6a26] flex items-center transition">
        Ticket <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link to="/movies" class="text-white hover:text-[#ef6a26] flex items-center transition">
        Pages <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link to="/movies" class="text-white hover:text-[#ef6a26] flex items-center transition">
        News <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link to="/movies" class="text-white hover:text-[#ef6a26] transition">
        Contact
      </router-link>
    </nav>

    <!-- Right Icons -->
    <div class="flex items-center space-x-6 text-white">
      <router-link to="/movies" class="hover:text-[#ef6a26] transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
        </svg>
      </router-link>

      <button @click="user ? logout() : router.push('/login')" class="hover:text-[#ef6a26] transition relative group" title="Account">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
        </svg>
        <span v-if="user" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-green-500 border-2 border-[#0f1014] rounded-full"></span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "../services/api";

const router = useRouter();
const route = useRoute();
const isScrolled = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

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
    window.addEventListener('scroll', handleScroll);
    handleScroll();

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

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>