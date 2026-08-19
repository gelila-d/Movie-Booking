<template>
  <section class="relative w-full bg-white pt-16 sm:pt-20 md:pt-24 pb-[5px] select-none">
    
    <!-- 3-Column Promo Grid with Thin White Separator Lines -->
    <div class="grid grid-cols-1 md:grid-cols-3 w-full gap-1 sm:gap-1.5 md:gap-2 bg-white relative z-10">
      
      <!-- Card 1: Top 6 Movies in Theaters -->
      <router-link 
        to="/movies" 
        class="group relative h-[250px] sm:h-[290px] md:h-[320px] lg:h-[350px] w-full flex flex-col items-center justify-between p-5 text-center cursor-pointer transition-all duration-500"
      >
        <!-- Inner Image & Overlay Container -->
        <div class="absolute inset-0 overflow-hidden">
          <div 
            class="absolute inset-0 bg-cover bg-center grayscale contrast-125 brightness-90 group-hover:scale-110 transition-transform duration-700 ease-out"
            style="background-image: url('/promo-street.png');"
          ></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-black/30 group-hover:opacity-80 transition-opacity duration-300"></div>
        </div>

        <!-- Top Black Distressed Brush Edge Overlay (Juts UPWARDS into white margin) -->
        <div class="absolute -top-7 sm:-top-9 md:-top-10 left-0 w-full h-8 sm:h-10 md:h-12 z-20 pointer-events-none">
          <svg class="w-full h-full text-black fill-current" preserveAspectRatio="none" viewBox="0 0 1200 100">
            <path d="M 0 100 L 0 40 Q 15 15 30 35 T 60 10 T 90 45 T 120 20 T 150 48 T 180 15 T 210 40 T 240 20 T 270 50 T 300 12 T 330 42 T 360 22 T 390 48 T 420 15 T 450 42 T 480 20 T 510 50 T 540 12 T 570 40 T 600 25 T 630 45 T 660 15 T 690 42 T 720 20 T 750 50 T 780 12 T 810 40 T 840 22 T 870 48 T 900 15 T 930 42 T 960 20 T 990 50 T 1020 12 T 1050 40 T 1080 22 T 1110 48 T 1140 15 T 1170 42 T 1200 25 L 1200 100 Z" />
            <circle cx="42" cy="18" r="2.5" />
            <circle cx="165" cy="12" r="3" />
            <circle cx="285" cy="8" r="2" />
            <circle cx="405" cy="14" r="3.5" />
            <circle cx="525" cy="10" r="2.5" />
            <circle cx="645" cy="12" r="3" />
            <circle cx="765" cy="8" r="2" />
            <circle cx="885" cy="14" r="3.5" />
            <circle cx="1005" cy="10" r="2.5" />
            <circle cx="1125" cy="12" r="3" />
          </svg>
        </div>

        <!-- Badge Container: White Circle Badge with Orange Icon -->
        <div class="relative z-10 pt-4 sm:pt-6">
          <div class="relative p-2 rounded-full border border-white/40 group-hover:border-white/70 transition-colors duration-300">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white flex items-center justify-center shadow-2xl group-hover:scale-105 group-hover:rotate-6 transition-all duration-300">
              <svg class="w-7 h-7 sm:w-9 sm:h-9 text-[#ef6a26] fill-current" viewBox="0 0 24 24">
                <path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4h-2l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4zM10 10.5v5l4.5-2.5L10 10.5z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Title -->
        <div class="relative z-10 pb-4 px-2">
          <h2 class="text-lg sm:text-xl lg:text-2xl font-extrabold text-white tracking-tight leading-tight drop-shadow-lg group-hover:text-[#ef6a26] transition-colors duration-300">
            Top 6 Movies in<br />Theaters
          </h2>
        </div>
      </router-link>

      <!-- Card 2: Watch Coming Soon Movies -->
      <router-link 
        to="/movies?filter=coming-soon" 
        class="group relative h-[250px] sm:h-[290px] md:h-[320px] lg:h-[350px] w-full flex flex-col items-center justify-between p-5 text-center cursor-pointer transition-all duration-500"
      >
        <!-- Inner Image & Overlay Container -->
        <div class="absolute inset-0 overflow-hidden">
          <div 
            class="absolute inset-0 bg-cover bg-center group-hover:scale-110 transition-transform duration-700 ease-out"
            style="background-image: url('/promo-cinema.png');"
          ></div>
          <div class="absolute inset-0 bg-[#ea580c]/85 mix-blend-multiply group-hover:bg-[#d97706]/80 transition-colors duration-300"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-orange-950/90 via-orange-600/60 to-orange-500/40 opacity-90"></div>
        </div>

        <!-- Top Orange Distressed Brush Edge Overlay (Juts UPWARDS into white margin) -->
        <div class="absolute -top-7 sm:-top-9 md:-top-10 left-0 w-full h-8 sm:h-10 md:h-12 z-20 pointer-events-none">
          <svg class="w-full h-full text-[#ea580c] fill-current" preserveAspectRatio="none" viewBox="0 0 1200 100">
            <path d="M 0 100 L 0 40 Q 15 15 30 35 T 60 10 T 90 45 T 120 20 T 150 48 T 180 15 T 210 40 T 240 20 T 270 50 T 300 12 T 330 42 T 360 22 T 390 48 T 420 15 T 450 42 T 480 20 T 510 50 T 540 12 T 570 40 T 600 25 T 630 45 T 660 15 T 690 42 T 720 20 T 750 50 T 780 12 T 810 40 T 840 22 T 870 48 T 900 15 T 930 42 T 960 20 T 990 50 T 1020 12 T 1050 40 T 1080 22 T 1110 48 T 1140 15 T 1170 42 T 1200 25 L 1200 100 Z" />
            <circle cx="42" cy="18" r="2.5" />
            <circle cx="165" cy="12" r="3" />
            <circle cx="285" cy="8" r="2" />
            <circle cx="405" cy="14" r="3.5" />
            <circle cx="525" cy="10" r="2.5" />
            <circle cx="645" cy="12" r="3" />
            <circle cx="765" cy="8" r="2" />
            <circle cx="885" cy="14" r="3.5" />
            <circle cx="1005" cy="10" r="2.5" />
            <circle cx="1125" cy="12" r="3" />
          </svg>
        </div>

        <!-- Badge Container: Solid Orange Circle Badge with White Icon -->
        <div class="relative z-10 pt-4 sm:pt-6">
          <div class="relative p-2 rounded-full border border-white/40 group-hover:border-white/70 transition-colors duration-300">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-[#ea580c] flex items-center justify-center shadow-2xl group-hover:scale-105 group-hover:rotate-6 transition-all duration-300">
              <svg class="w-7 h-7 sm:w-9 sm:h-9 text-white fill-current" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM12 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zM7.5 14c-.83 0-1.5-.67-1.5-1.5S6.67 11 7.5 11s1.5.67 1.5 1.5S8.33 14 7.5 14zM16.5 14c-.83 0-1.5-.67-1.5-1.5S15.67 11 16.5 11s1.5.67 1.5 1.5S17.33 14 16.5 14zM12 8c-.83 0-1.5-.67-1.5-1.5S11.17 5 12 5s1.5.67 1.5 1.5S12.83 8 12 8z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Title -->
        <div class="relative z-10 pb-4 px-2">
          <h2 class="text-lg sm:text-xl lg:text-2xl font-extrabold text-white tracking-tight leading-tight drop-shadow-lg group-hover:text-white transition-colors duration-300">
            Watch Coming<br />Soon Movies
          </h2>
        </div>
      </router-link>

      <!-- Card 3: Hot Movies Trailers Live Now -->
      <router-link 
        to="/movies" 
        class="group relative h-[250px] sm:h-[290px] md:h-[320px] lg:h-[350px] w-full flex flex-col items-center justify-between p-5 text-center cursor-pointer transition-all duration-500"
      >
        <!-- Inner Image & Overlay Container -->
        <div class="absolute inset-0 overflow-hidden">
          <div 
            class="absolute inset-0 bg-cover bg-center grayscale contrast-125 brightness-90 group-hover:scale-110 transition-transform duration-700 ease-out"
            style="background-image: url('/promo-mask.png');"
          ></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/50 to-black/30 group-hover:opacity-80 transition-opacity duration-300"></div>
        </div>

        <!-- Top Black Distressed Brush Edge Overlay (Juts UPWARDS into white margin) -->
        <div class="absolute -top-7 sm:-top-9 md:-top-10 left-0 w-full h-8 sm:h-10 md:h-12 z-20 pointer-events-none">
          <svg class="w-full h-full text-black fill-current" preserveAspectRatio="none" viewBox="0 0 1200 100">
            <path d="M 0 100 L 0 40 Q 15 15 30 35 T 60 10 T 90 45 T 120 20 T 150 48 T 180 15 T 210 40 T 240 20 T 270 50 T 300 12 T 330 42 T 360 22 T 390 48 T 420 15 T 450 42 T 480 20 T 510 50 T 540 12 T 570 40 T 600 25 T 630 45 T 660 15 T 690 42 T 720 20 T 750 50 T 780 12 T 810 40 T 840 22 T 870 48 T 900 15 T 930 42 T 960 20 T 990 50 T 1020 12 T 1050 40 T 1080 22 T 1110 48 T 1140 15 T 1170 42 T 1200 25 L 1200 100 Z" />
            <circle cx="42" cy="18" r="2.5" />
            <circle cx="165" cy="12" r="3" />
            <circle cx="285" cy="8" r="2" />
            <circle cx="405" cy="14" r="3.5" />
            <circle cx="525" cy="10" r="2.5" />
            <circle cx="645" cy="12" r="3" />
            <circle cx="765" cy="8" r="2" />
            <circle cx="885" cy="14" r="3.5" />
            <circle cx="1005" cy="10" r="2.5" />
            <circle cx="1125" cy="12" r="3" />
          </svg>
        </div>

        <!-- Badge Container: White Circle Badge with Orange Icon -->
        <div class="relative z-10 pt-4 sm:pt-6">
          <div class="relative p-2 rounded-full border border-white/40 group-hover:border-white/70 transition-colors duration-300">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white flex items-center justify-center shadow-2xl group-hover:scale-105 group-hover:rotate-6 transition-all duration-300">
              <svg class="w-7 h-7 sm:w-9 sm:h-9 text-[#ef6a26] fill-current" viewBox="0 0 24 24">
                <path d="M16 11.5l5-3v11l-5-3v1.5c0 .55-.45 1-1 1H3c-.55 0-1-.45-1-1v-8c0-.55.45-1 1-1h12c.55 0 1 .45 1 1v1.5z M9 5a3 3 0 1 0 -6 0a3 3 0 1 0 6 0z M16 5a3 3 0 1 0 -6 0a3 3 0 1 0 6 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Title -->
        <div class="relative z-10 pb-4 px-2">
          <h2 class="text-lg sm:text-xl lg:text-2xl font-extrabold text-white tracking-tight leading-tight drop-shadow-lg group-hover:text-[#ef6a26] transition-colors duration-300">
            Hot Movies Trailers<br />Live Now
          </h2>
        </div>
      </router-link>

    </div>
  </section>
</template>

<script setup>
// PromoCards component built to exactly match the reference design
</script>
