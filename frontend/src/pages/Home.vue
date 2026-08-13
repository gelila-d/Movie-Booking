<template>
  <div class="relative w-full h-screen overflow-hidden bg-black text-white font-sans">
    <!-- Background Image -->
    <div 
      class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-80"
      style="background-image: url('/bg-skull.png');"
    ></div>
    
    <!-- Gradient Overlay for better text readability -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/80"></div>

    <!-- Main Content -->
    <main class="relative z-10 flex flex-col h-full justify-center px-10 md:px-20 lg:px-32">
      
      <!-- Top Subtitle -->
      <div class="mb-4 transform -rotate-2 origin-left">
        <span class="font-caveat text-5xl text-orange-500">Action Movie</span>
      </div>

      <!-- Main Title -->
      <h1 class="text-7xl md:text-8xl lg:text-9xl font-bold leading-none tracking-tight mb-2">
        The Witcher<br/>Season 2
      </h1>

      <!-- Bottom Info -->
      <p class="mt-8 text-gray-300 text-lg md:text-xl font-light">
        Writen and Directed by Aleesha Rose / Ireland 2023
      </p>

      <!-- Right Side Element -->
      <div class="absolute right-10 md:right-20 top-1/2 transform -translate-y-1/2 flex flex-col items-end hidden md:flex">
        <span class="text-gray-300 text-sm tracking-widest uppercase mb-1">In theater</span>
        <div class="relative">
          <span class="text-4xl md:text-5xl font-bold">March 2023</span>
          <!-- Orange underline graphic -->
          <svg class="absolute -bottom-4 left-0 w-full h-4 text-orange-500" viewBox="0 0 100 10" preserveAspectRatio="none">
            <path d="M0 5 Q 50 10 100 5 Q 50 0 0 5 Z" fill="currentColor"/>
          </svg>
        </div>
      </div>
    </main>

    <!-- Sidebar controls (placeholder for the right edge UI) -->
    <div class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white rounded-l-full p-4 cursor-pointer hover:bg-gray-200 transition shadow-lg hidden lg:block">
      <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
        <!-- Simple hexagon icon -->
        <path d="M12 2L22 7.77V16.23L12 22L2 16.23V7.77L12 2Z"/>
      </svg>
    </div>

    <!-- Bottom Right Icon -->
    <div class="absolute bottom-8 right-8 bg-white p-3 rounded-lg cursor-pointer hover:bg-gray-200 transition shadow-lg hidden md:block">
      <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2V6C15.31 6 18 8.69 18 12C18 15.31 15.31 18 12 18C8.69 18 6 15.31 6 12H2C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z"/>
      </svg>
    </div>
  </div>
</template>

<script setup>
// Home page component
</script>

<style scoped>
.font-caveat {
  font-family: 'Caveat', cursive;
}
</style>
