<template>
  <section class="relative w-full bg-white select-none overflow-hidden pt-16 sm:pt-20 lg:pt-24 pb-12 sm:pb-16">
    <div class="w-full">
      <div class="grid grid-cols-1 lg:grid-cols-12 w-full">
        
        <!-- Left Side: Event Announcement & Info -->
        <div class="lg:col-span-6 xl:col-span-6 bg-white p-8 sm:p-12 lg:p-16 xl:p-20 flex flex-col justify-center relative overflow-hidden">
          <!-- Light Smoke / Popcorn Background Pattern Watermark -->
          <div 
            class="absolute inset-0 pointer-events-none opacity-[0.06] bg-repeat" 
            style="background-image: url('data:image/svg+xml;utf8,<svg width=\'120\' height=\'120\' viewBox=\'0 0 120 120\' xmlns=\'http://www.w3.org/2000/svg\'><circle cx=\'20\' cy=\'20\' r=\'12\' fill=\'%23000\'/><circle cx=\'40\' cy=\'30\' r=\'16\' fill=\'%23000\'/><circle cx=\'25\' cy=\'45\' r=\'14\' fill=\'%23000\'/><circle cx=\'80\' cy=\'80\' r=\'15\' fill=\'%23000\'/><circle cx=\'100\' cy=\'70\' r=\'18\' fill=\'%23000\'/><circle cx=\'85\' cy=\'95\' r=\'12\' fill=\'%23000\'/></svg>'); background-size: 240px 240px;"
          ></div>

          <div class="relative z-10 max-w-xl text-left">
            <!-- Top Icon & Subheading -->
            <div class="flex items-center gap-2.5 mb-3">
              <div class="w-7 h-7 rounded-full bg-[#ea580c]/10 text-[#ea580c] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 fill-[#ea580c]" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-8c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm6 0c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-3-5c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm0 10c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z"/>
                </svg>
              </div>
              <span class="text-gray-500 font-semibold text-xs sm:text-sm uppercase tracking-wider">
                Upcoming Event
              </span>
            </div>

            <!-- Main Heading -->
            <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-5xl font-black text-black leading-[1.12] tracking-tight mb-5">
              Register Yourself Now<br class="hidden sm:inline" /> for the Events
            </h2>

            <!-- Description -->
            <p class="text-gray-500 text-sm sm:text-base leading-relaxed mb-8 font-normal">
              Every man must decide whether he will walk in the light of creative altruism or in the darkness of eritdestructive selfishness. Ut porttitor et lectus ut tempus. Aliquam lacinia justo.
            </p>

            <!-- More Info Button -->
            <div>
              <router-link 
                to="/movies" 
                class="inline-block bg-[#d6682b] hover:bg-[#e67536] text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider px-8 py-3.5 shadow-md transition-colors duration-300"
              >
                More Info
              </router-link>
            </div>
          </div>
        </div>

        <!-- Right Side: Dark Container with 2 Stacked Event Cards -->
        <div class="lg:col-span-6 xl:col-span-6 bg-[#050505] p-6 sm:p-10 lg:p-12 xl:p-16 flex flex-col justify-center gap-6 sm:gap-8 relative overflow-hidden">
          <!-- Dark Film Reel Overlay Watermark -->
          <div class="absolute inset-0 pointer-events-none opacity-15 card-strip-overlay"></div>

          <div class="relative z-10 flex flex-col gap-6 sm:gap-8 max-w-2xl mx-auto w-full">
            
            <!-- Event Card 1: Best Magical Movies Ever Made -->
            <div class="group grid grid-cols-1 sm:grid-cols-12 bg-white overflow-hidden shadow-2xl cursor-pointer border border-transparent hover:border-[#d6682b]/40 transition-all duration-300">
              <!-- Left Image Container -->
              <div class="sm:col-span-5 relative h-52 sm:h-auto min-h-[190px] overflow-hidden bg-gray-900">
                <img 
                  src="/event-magic.jpg" 
                  alt="Best Magical Movies" 
                  class="w-full h-full object-cover transform group-hover:scale-108 transition-transform duration-700 ease-out" 
                />
                
                <!-- Vertical Date Badge -->
                <div class="absolute left-0 top-0 bottom-0 w-9 bg-[#d6682b] text-white font-extrabold text-[11px] tracking-widest flex items-center justify-center shadow-md">
                  <span class="rotate-180 [writing-mode:vertical-lr] uppercase whitespace-nowrap">
                    26 MAR, 2025
                  </span>
                </div>
              </div>

              <!-- Right Content Info -->
              <div class="sm:col-span-7 bg-white p-6 sm:p-7 flex flex-col justify-between text-left">
                <!-- Meta Info (Time & Location) -->
                <div class="flex items-center gap-4 text-xs font-semibold text-gray-500 mb-3">
                  <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#d6682b] fill-current" viewBox="0 0 24 24">
                      <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                    <span>14:00 - 18:00</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#d6682b] fill-current" viewBox="0 0 24 24">
                      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span>New York</span>
                  </div>
                </div>

                <!-- Event Title -->
                <h3 class="text-xl sm:text-2xl font-black text-black leading-snug tracking-tight mb-4 group-hover:text-[#d6682b] transition-colors duration-300">
                  Best Magical Movies Ever Made
                </h3>

                <!-- Read More Link -->
                <div class="mt-auto">
                  <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 group-hover:text-[#d6682b] transition-colors duration-300 uppercase tracking-wider">
                    Read More ┘
                  </span>
                </div>
              </div>
            </div>

            <!-- Event Card 2: The Strange Love Movie 2023 Festival -->
            <div class="group grid grid-cols-1 sm:grid-cols-12 bg-white overflow-hidden shadow-2xl cursor-pointer border border-transparent hover:border-[#d6682b]/40 transition-all duration-300">
              <!-- Left Image Container -->
              <div class="sm:col-span-5 relative h-52 sm:h-auto min-h-[190px] overflow-hidden bg-gray-900">
                <img 
                  src="/event-festival.jpg" 
                  alt="Strange Love Movie Festival" 
                  class="w-full h-full object-cover transform group-hover:scale-108 transition-transform duration-700 ease-out" 
                />
                
                <!-- Vertical Date Badge -->
                <div class="absolute left-0 top-0 bottom-0 w-9 bg-[#d6682b] text-white font-extrabold text-[11px] tracking-widest flex items-center justify-center shadow-md">
                  <span class="rotate-180 [writing-mode:vertical-lr] uppercase whitespace-nowrap">
                    26 APR, 2025
                  </span>
                </div>
              </div>

              <!-- Right Content Info -->
              <div class="sm:col-span-7 bg-white p-6 sm:p-7 flex flex-col justify-between text-left">
                <!-- Meta Info (Time & Location) -->
                <div class="flex items-center gap-4 text-xs font-semibold text-gray-500 mb-3">
                  <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#d6682b] fill-current" viewBox="0 0 24 24">
                      <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                    <span>16:00 - 22:00</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#d6682b] fill-current" viewBox="0 0 24 24">
                      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span>New York</span>
                  </div>
                </div>

                <!-- Event Title -->
                <h3 class="text-xl sm:text-2xl font-black text-black leading-snug tracking-tight mb-4 group-hover:text-[#d6682b] transition-colors duration-300">
                  The Strange Love Movie 2023 Festival
                </h3>

                <!-- Read More Link -->
                <div class="mt-auto">
                  <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 group-hover:text-[#d6682b] transition-colors duration-300 uppercase tracking-wider">
                    Read More ┘
                  </span>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script setup>
// UpcomingEvents component matching the reference design layout
</script>

<style scoped>
.card-strip-overlay {
  background-image: url('data:image/svg+xml;utf8,<svg width="240" height="240" viewBox="0 0 240 240" xmlns="http://www.w3.org/2000/svg"><g transform="rotate(-15 120 120)"><rect x="70" y="-100" width="100" height="440" fill="%23fff"/><rect x="80" y="-80" width="15" height="20" fill="%23000"/><rect x="80" y="-40" width="15" height="20" fill="%23000"/><rect x="80" y="0" width="15" height="20" fill="%23000"/><rect x="80" y="40" width="15" height="20" fill="%23000"/><rect x="80" y="80" width="15" height="20" fill="%23000"/><rect x="80" y="120" width="15" height="20" fill="%23000"/><rect x="80" y="160" width="15" height="20" fill="%23000"/><rect x="80" y="200" width="15" height="20" fill="%23000"/><rect x="80" y="240" width="15" height="20" fill="%23000"/><rect x="80" y="280" width="15" height="20" fill="%23000"/><rect x="80" y="320" width="15" height="20" fill="%23000"/><rect x="145" y="-80" width="15" height="20" fill="%23000"/><rect x="145" y="-40" width="15" height="20" fill="%23000"/><rect x="145" y="0" width="15" height="20" fill="%23000"/><rect x="145" y="40" width="15" height="20" fill="%23000"/><rect x="145" y="80" width="15" height="20" fill="%23000"/><rect x="145" y="120" width="15" height="20" fill="%23000"/><rect x="145" y="160" width="15" height="20" fill="%23000"/><rect x="145" y="200" width="15" height="20" fill="%23000"/><rect x="145" y="240" width="15" height="20" fill="%23000"/><rect x="145" y="280" width="15" height="20" fill="%23000"/><rect x="145" y="320" width="15" height="20" fill="%23000"/></g></svg>');
  background-size: 480px 480px;
  background-position: center;
}
</style>
