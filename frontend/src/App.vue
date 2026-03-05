<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 font-sans">
    <Navbar />
    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from "./components/Navbar.vue";
</script>

<style>
/* Global styles are in style.css */
</style>