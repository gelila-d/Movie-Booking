<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-violet-900 text-slate-100 font-sans grid-bg">
    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-violet-500/5 to-fuchsia-500/5"></div>
    <Navbar v-if="!isAuthPage" />
    <main :class="['relative z-10', { 'pt-24': route.path !== '/' }]">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from "./components/Navbar.vue";

const route = useRoute();
const isAuthPage = computed(() => ['/login', '/register'].includes(route.path));
</script>

<style>
/* Global styles are in style.css */
</style>