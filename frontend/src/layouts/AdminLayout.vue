<template>
  <div class="min-h-screen bg-[#09090b] flex font-sans">

    <!-- ───────────── Sidebar ───────────── -->
    <aside
      :class="[
        'fixed top-0 left-0 h-full z-40 flex flex-col bg-[#0d0d0f] border-r border-white/[0.06] transition-all duration-300 ease-in-out',
        sidebarOpen ? 'w-60' : 'w-16'
      ]"
    >
      <!-- Logo / Toggle -->
      <div class="flex items-center gap-3 px-3 py-4 border-b border-white/[0.06] min-h-[64px]">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#ef6a26]/10 hover:bg-[#ef6a26]/20 text-[#ef6a26] transition-colors flex-shrink-0"
          :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
        >
          <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-8c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm6 0c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-3-5c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm0 10c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z"/>
          </svg>
        </button>
        <span
          v-if="sidebarOpen"
          class="text-lg font-extrabold text-white tracking-tight whitespace-nowrap overflow-hidden"
        >
          movies <span class="text-[#ef6a26]">admin</span>
        </span>
      </div>

      <!-- Nav Items -->
      <nav class="flex-1 py-4 space-y-1 px-2 overflow-y-auto">
        <router-link
          v-for="item in navItems"
          :key="item.path"
          :to="item.path"
          :title="!sidebarOpen ? item.label : ''"
          :class="[
            'flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200',
            isActive(item.path)
              ? 'bg-[#ef6a26]/15 text-[#ef6a26] border border-[#ef6a26]/20'
              : 'text-gray-400 hover:text-white hover:bg-white/[0.05]'
          ]"
        >
          <span class="w-5 h-5 flex-shrink-0 flex items-center justify-center" v-html="item.icon"></span>
          <span v-if="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- Bottom actions -->
      <div class="border-t border-white/[0.06] px-2 py-3 space-y-1">
        <a
          href="/"
          target="_blank"
          :title="!sidebarOpen ? 'View site' : ''"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/[0.05] transition-all duration-200"
        >
          <svg class="w-5 h-5 flex-shrink-0 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
          <span v-if="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">View Site</span>
        </a>
        <button
          @click="logout"
          :title="!sidebarOpen ? 'Logout' : ''"
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-400 hover:text-red-300 hover:bg-red-950/30 transition-all duration-200"
        >
          <svg class="w-5 h-5 flex-shrink-0 fill-current" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
          <span v-if="sidebarOpen" class="text-sm font-semibold whitespace-nowrap">Logout</span>
        </button>
      </div>
    </aside>

    <!-- ───────────── Main Content ───────────── -->
    <div :class="['flex-1 flex flex-col min-h-screen transition-all duration-300', sidebarOpen ? 'ml-60' : 'ml-16']">
      <!-- Top Bar -->
      <header class="sticky top-0 z-30 h-16 bg-[#0d0d0f]/95 backdrop-blur border-b border-white/[0.06] flex items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-3">
          <span class="text-white font-bold text-base">{{ currentPageTitle }}</span>
          <span class="hidden sm:inline text-xs text-slate-500 font-mono uppercase tracking-widest">/ Admin Panel</span>
        </div>
        <div class="flex items-center gap-2 bg-white/[0.05] border border-white/[0.08] rounded-full px-3 py-1.5">
          <div class="w-6 h-6 rounded-full bg-[#ef6a26] flex items-center justify-center text-white text-xs font-extrabold flex-shrink-0">
            {{ userInitial }}
          </div>
          <span class="text-xs text-gray-300 font-medium hidden sm:block">{{ userName }}</span>
          <span class="text-[10px] text-[#ef6a26] font-bold uppercase tracking-wide">ADMIN</span>
        </div>
      </header>

      <!-- Page -->
      <main class="flex-1 p-4 sm:p-6 overflow-y-auto">
        <router-view />
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const sidebarOpen = ref(true)

const user = computed(() => {
  try { return JSON.parse(localStorage.getItem('user') || 'null') } catch { return null }
})
const userName = computed(() => user.value?.name || 'Admin')
const userInitial = computed(() => (userName.value || 'A')[0].toUpperCase())

const navItems = [
  { path: '/admin', label: 'Dashboard', icon: `<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>` },
  { path: '/admin/movies', label: 'Movies', icon: `<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>` },
  { path: '/admin/showtimes', label: 'Showtimes', icon: `<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>` },
  { path: '/admin/cinemas', label: 'Cinemas', icon: `<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 3L2 12h3v9h6v-6h2v6h6v-9h3L12 3z"/></svg>` },
  { path: '/admin/bookings', label: 'Bookings', icon: `<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M20 12c0-1.1-.9-2-2-2V7c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3c1.1 0 2-.9 2-2zm-4 5H4V7h12v10zm2-5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>` },
  { path: '/admin/users', label: 'Users', icon: `<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>` },
]

const currentPageTitle = computed(() => {
  const match = navItems.find(n => n.path === route.path)
  return match ? match.label : 'Admin'
})

const isActive = (path) => path === '/admin' ? route.path === '/admin' : route.path.startsWith(path)

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>
