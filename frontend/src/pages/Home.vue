<template>
  <div class="relative w-full min-h-screen bg-black text-white font-sans overflow-x-hidden flex flex-col justify-between pt-20">
    <!-- Background Image -->
    <div 
      class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-85 pointer-events-none z-0"
      style="background-image: url('/bg-skull.png');"
    ></div>
    
    <!-- Gradient Overlays for Cinematic Look & Readability -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/50 to-transparent pointer-events-none z-0"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/90 pointer-events-none z-0"></div>

    <!-- Main Hero Body -->
    <div class="relative z-10 flex-1 flex flex-col justify-between px-8 md:px-16 lg:px-24 py-12">
      
      <!-- Top/Middle Left Text Content -->
      <div class="max-w-3xl my-auto">
        <!-- Subtitle -->
        <div class="mb-3 transform -rotate-2 origin-left">
          <span class="font-caveat text-5xl md:text-6xl text-[#ef6a26]">Action Movie</span>
        </div>

        <!-- Main Title -->
        <h1 class="text-6xl md:text-8xl lg:text-[105px] font-extrabold leading-[0.95] tracking-tight mb-4 text-white">
          The Witcher<br/>Season 2
        </h1>

        <!-- Director Info -->
        <p class="mt-6 text-gray-300 text-base md:text-lg font-light tracking-wide">
          Writen and Directed by Aleesha Rose / Ireland 2023
        </p>

        <!-- CTA Buttons -->
        <div class="mt-8 flex flex-wrap items-center gap-4">
          <router-link 
            to="/movies" 
            class="bg-white hover:bg-gray-100 text-black font-bold text-sm px-9 py-4 uppercase tracking-wider transition-colors shadow-xl"
          >
            More Info
          </router-link>
          <router-link 
            to="/movies" 
            class="bg-[#ef6a26] hover:bg-orange-600 text-white font-bold text-sm px-9 py-4 uppercase tracking-wider transition-colors shadow-xl"
          >
            Get Ticket
          </router-link>
        </div>
      </div>

      <!-- Right Release Date Box (In theater March 2023) -->
      <div class="absolute right-8 md:right-16 lg:right-24 top-28 flex flex-col items-end hidden sm:flex">
        <span class="text-gray-300 text-xs md:text-sm tracking-widest uppercase mb-1">In theater</span>
        <div class="relative">
          <span class="text-3xl md:text-5xl font-extrabold text-white">March 2023</span>
          <!-- Hand-drawn Orange Underline -->
          <svg class="absolute -bottom-3 left-0 w-full h-3 text-[#ef6a26]" viewBox="0 0 100 10" preserveAspectRatio="none">
            <path d="M0 5 Q 50 10 100 5 Q 50 0 0 5 Z" fill="currentColor"/>
          </svg>
        </div>
      </div>

      <!-- Bottom Right Trailers Section -->
      <div class="relative mt-12 self-end w-full lg:w-auto">
        <div class="bg-[#0b0c10]/85 backdrop-blur-md border-t border-l border-gray-800/80 p-6 md:p-8 flex flex-col sm:flex-row items-start sm:items-center gap-6">
          
          <!-- Hand-drawn Arrow & Label -->
          <div class="flex items-center space-x-3">
            <!-- Curly Hand-drawn Arrow -->
            <svg class="w-10 h-10 text-white stroke-current fill-none transform -rotate-12" viewBox="0 0 50 50">
              <path d="M 10 10 Q 30 5 35 25 T 20 40 Q 35 38 40 32" stroke-width="2.5" stroke-linecap="round"/>
              <path d="M 32 30 L 40 32 L 38 40" stroke-width="2.5" stroke-linecap="round"/>
            </svg>

            <div class="flex flex-col">
              <div class="flex items-center justify-between w-full space-x-6">
                <span class="text-lg font-semibold text-white tracking-wide">Trailers</span>
                <!-- Pagination Dots -->
                <div class="flex items-center space-x-1.5 text-xs text-gray-500">
                  <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                  <span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
                  <span class="w-1.5 h-1.5 rounded-full bg-gray-600"></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Video Thumbnails List -->
          <div class="flex items-center space-x-4">
            <!-- Thumbnail 1 (Active) -->
            <div class="relative w-36 md:w-44 h-24 border-2 border-[#ef6a26] overflow-hidden group cursor-pointer shadow-lg">
              <img src="/bg-skull.png" alt="Trailer 1" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-white text-black flex items-center justify-center shadow-md transform group-hover:scale-110 transition-transform">
                  <svg class="w-4 h-4 fill-current translate-x-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Thumbnail 2 -->
            <div class="relative w-36 md:w-44 h-24 border border-white/80 overflow-hidden group cursor-pointer shadow-lg">
              <img src="/bg-dandelion.png" alt="Trailer 2" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="absolute inset-0 bg-black/40 group-hover:bg-black/10 transition-colors"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-8 h-8 rounded-full bg-white text-black flex items-center justify-center shadow-md transform group-hover:scale-110 transition-transform">
                  <svg class="w-4 h-4 fill-current translate-x-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Floating Right Sidebar Widget (Hexagon Tab) -->
    <div class="fixed right-0 top-1/2 transform -translate-y-1/2 bg-white rounded-l-full p-3 cursor-pointer hover:bg-gray-100 transition shadow-2xl z-30 hidden lg:block">
      <svg class="w-5 h-5 text-[#ef6a26]" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 2L22 7.77V16.23L12 22L2 16.23V7.77L12 2Z"/>
      </svg>
    </div>

    <!-- Floating Refresh Icon Widget (Bottom Right) -->
    <div class="fixed bottom-12 right-6 bg-white p-3 rounded-md cursor-pointer hover:bg-gray-100 transition shadow-2xl z-30 hidden md:block">
      <svg class="w-6 h-6 text-[#ef6a26]" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
      </svg>
    </div>

    <!-- Bottom Film Perforation Strip -->
    <div class="relative z-20 w-full bg-white py-2 flex items-center overflow-hidden border-t-2 border-black">
      <div class="flex items-center space-x-3 w-full animate-marquee whitespace-nowrap">
        <div v-for="i in 60" :key="i" class="w-3.5 h-3.5 bg-black shrink-0"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Full Homepage Hero Component matching exact reference photos
</script>

<style scoped>
.font-caveat {
  font-family: 'Caveat', cursive;
}
</style>
