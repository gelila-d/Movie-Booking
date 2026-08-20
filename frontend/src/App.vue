<template>
  <div class="min-h-screen font-sans flex flex-col justify-between bg-[#050505]">
    <Navbar v-if="!isAuthPage" />
    <main :class="['relative z-10 flex-1', { 'pt-24': !isAuthPage && route.path !== '/' }]">
      <router-view />
    </main>
    <Footer v-if="!isAuthPage" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from "./components/Navbar.vue";
import Footer from "./components/Footer.vue";

const route = useRoute();
const isAuthPage = computed(() => ['/login', '/register'].includes(route.path));
</script>

<style>
/* Global styles are in style.css */
</style>