<template>
  <div class="min-h-screen font-sans flex flex-col bg-black">
    <!-- Public Navbar — hidden for auth pages and admin section -->
    <Navbar v-if="showPublicNav" />

    <main :class="['relative z-10 flex-1', { 'pt-16': showPublicNav && route.path !== '/' }]">
      <router-view />
    </main>

    <!-- Public Footer — hidden for auth pages, admin section, and pages that opt out -->
    <Footer v-if="showPublicFooter" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from "./components/Navbar.vue";
import Footer from "./components/Footer.vue";

const route = useRoute();

const isAuthPage   = computed(() => ['/login', '/register'].includes(route.path));
const isAdminPage  = computed(() => route.path.startsWith('/admin'));

const showPublicNav    = computed(() => !isAuthPage.value && !isAdminPage.value && !route.meta.hideNavbar);
const showPublicFooter = computed(() => !isAuthPage.value && !isAdminPage.value && !route.meta.hideFooter);
</script>

<style>
/* Global styles are in style.css */
</style>