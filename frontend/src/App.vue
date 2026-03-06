<template>
  <div class="min-h-screen bg-yellow-50 text-gray-900 font-sans">
    <Navbar v-if="!isAuthPage" />
    <main>
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