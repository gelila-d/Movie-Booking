<template>
  <div class="min-h-screen font-sans">
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