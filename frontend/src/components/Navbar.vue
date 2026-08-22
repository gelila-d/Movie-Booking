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
        <!-- Film Reel Icon -->
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
      <router-link 
        to="/my-bookings" 
        class="flex items-center transition" 
        :class="[route.path === '/my-bookings' ? 'text-[#ef6a26]' : 'text-white hover:text-[#ef6a26]']"
      >
        My Bookings <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link 
        to="/watchlist" 
        class="flex items-center transition" 
        :class="[route.path === '/watchlist' ? 'text-[#ef6a26]' : 'text-white hover:text-[#ef6a26]']"
      >
        Watchlist <span class="ml-1 text-xs">❤️</span>
      </router-link>
      <router-link to="/movies" class="text-white hover:text-[#ef6a26] flex items-center transition">
        News <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </router-link>
      <router-link to="/movies" class="text-white hover:text-[#ef6a26] transition">
        Contact
      </router-link>
    </nav>

    <!-- Right Icons & Profile Menu -->
    <div class="flex items-center space-x-6 text-white relative">
      <router-link to="/movies" class="hover:text-[#ef6a26] transition" title="Search Movies">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
        </svg>
      </router-link>

      <!-- Account Profile Dropdown Trigger -->
      <div class="relative" ref="dropdownRef">
        <button 
          @click="toggleDropdown" 
          class="hover:text-[#ef6a26] transition relative group" 
          title="Account"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
          </svg>
          <span v-if="user" class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-green-500 border-2 border-[#0f1014] rounded-full"></span>
        </button>

        <!-- Dropdown Menu Box -->
        <transition 
          enter-active-class="transition ease-out duration-200" 
          enter-from-class="transform opacity-0 scale-95 -translate-y-2" 
          enter-to-class="transform opacity-100 scale-100 translate-y-0" 
          leave-active-class="transition ease-in duration-150" 
          leave-from-class="transform opacity-100 scale-100 translate-y-0" 
          leave-to-class="transform opacity-0 scale-95 -translate-y-2"
        >
          <div 
            v-if="dropdownOpen" 
            class="absolute right-0 mt-3 w-64 rounded-2xl bg-black/60 border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.8),inset_0_1px_0_rgba(255,255,255,0.1)] backdrop-blur-2xl py-3 px-2 z-50 text-slate-100"
          >
            <!-- User Info Header (Logged In) -->
            <div v-if="user" class="px-3 py-2 border-b border-white/10 mb-1">
              <div class="flex items-center justify-between">
                <p class="text-sm font-bold text-white truncate">{{ user.name }}</p>
                <span v-if="user.is_admin" class="text-[10px] font-bold bg-[#ef6a26]/20 border border-[#ef6a26]/50 text-[#ef6a26] px-2 py-0.5 rounded-full uppercase tracking-wider">
                  Admin
                </span>
              </div>
              <p class="text-xs text-slate-400 truncate mt-0.5">{{ user.email }}</p>
            </div>

            <!-- Menu Links -->
            <div class="space-y-0.5">
              <router-link 
                v-if="user"
                to="/my-bookings" 
                @click="dropdownOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-200 hover:bg-[#ef6a26]/15 hover:text-[#ef6a26] transition-colors group"
              >
                <svg class="w-4 h-4 text-[#ef6a26] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <span>My Bookings</span>
              </router-link>

              <router-link 
                v-if="user"
                to="/watchlist" 
                @click="dropdownOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-200 hover:bg-[#ef6a26]/15 hover:text-[#ef6a26] transition-colors group"
              >
                <span class="text-xs">❤️</span>
                <span>My Watchlist</span>
              </router-link>

              <router-link 
                v-if="user && user.is_admin"
                to="/admin/movies" 
                @click="dropdownOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-200 hover:bg-[#ef6a26]/15 hover:text-[#ef6a26] transition-colors group"
              >
                <svg class="w-4 h-4 text-[#ef6a26] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Admin Dashboard</span>
              </router-link>

              <router-link 
                v-if="!user"
                to="/login" 
                @click="dropdownOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-200 hover:bg-[#ef6a26]/15 hover:text-[#ef6a26] transition-colors group"
              >
                <svg class="w-4 h-4 text-[#ef6a26] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>Log In</span>
              </router-link>

              <router-link 
                v-if="!user"
                to="/register" 
                @click="dropdownOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-200 hover:bg-[#ef6a26]/15 hover:text-[#ef6a26] transition-colors group"
              >
                <svg class="w-4 h-4 text-[#ef6a26] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Register Account</span>
              </router-link>

              <!-- Log Out Button -->
              <button 
                v-if="user"
                @click="handleLogout" 
                class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors border-t border-white/10 mt-2 group"
              >
                <svg class="w-4 h-4 text-red-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Log Out</span>
              </button>
            </div>
          </div>
        </transition>
      </div>
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
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

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

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    dropdownOpen.value = false;
  }
};

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

const handleLogout = async () => {
  dropdownOpen.value = false;
  await logout();
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('click', handleClickOutside);
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
    window.removeEventListener('click', handleClickOutside);
});
</script>