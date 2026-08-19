<template>
  <div 
    class="relative w-full min-h-[100vh] bg-black text-white font-sans overflow-hidden flex flex-col justify-between pt-20 select-none"
    @mouseenter="pauseAutoPlay"
    @mouseleave="startAutoPlay"
  >
    <!-- Background Image Slides with Smooth Crossfade (Full Page Cover) -->
    <div 
      v-for="(trailer, index) in trailers" 
      :key="trailer.id || index"
      class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-all duration-1000 ease-in-out pointer-events-none z-0 transform"
      :class="index === currentIndex ? 'opacity-100 scale-100' : 'opacity-0 scale-105'"
      :style="{ backgroundImage: `url('${trailer.bgImage}')` }"
    ></div>
    
    <!-- Subtle Gradient Overlay for Text Readability Only -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/50 via-transparent to-transparent pointer-events-none z-0"></div>

    <!-- Main Hero Body -->
    <div class="relative z-10 flex-1 flex flex-col justify-between px-8 md:px-16 lg:px-24 py-12 md:py-20 lg:py-24">
      
      <!-- Top/Middle Left Text Content (Fixed min-height prevents layout shifting) -->
      <div class="max-w-3xl my-auto min-h-[320px] sm:min-h-[360px] flex items-center">
        <transition name="fade" mode="out-in">
          <div :key="currentTrailer.id || currentIndex" class="w-full">
            <!-- Subtitle -->
            <div class="mb-2 transform -rotate-2 origin-left">
              <span class="font-caveat text-3xl md:text-4xl text-[#ef6a26] drop-shadow-md">{{ currentTrailer.subtitle }}</span>
            </div>

            <!-- Main Title -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight tracking-tight mb-4 text-white drop-shadow-xl whitespace-pre-line">
              {{ currentTrailer.title }}
            </h1>

            <!-- Director Info -->
            <p class="mt-6 text-gray-300 text-base md:text-lg font-light tracking-wide max-w-2xl line-clamp-2">
              {{ currentTrailer.director }}
            </p>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center gap-4">
              <router-link 
                :to="currentTrailer.link || '/movies'" 
                class="bg-white hover:bg-gray-100 text-black font-bold text-sm px-9 py-4 uppercase tracking-wider transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-0.5"
              >
                More Info
              </router-link>
              <router-link 
                :to="currentTrailer.ticketLink || '/movies'" 
                class="bg-[#ef6a26] hover:bg-orange-600 text-white font-bold text-sm px-9 py-4 uppercase tracking-wider transition-all duration-300 shadow-xl hover:shadow-2xl hover:-translate-y-0.5"
              >
                Get Ticket
              </router-link>
              <button
                @click="openTrailerModal(currentTrailer)"
                class="border border-white/40 hover:border-white bg-black/40 hover:bg-black/70 text-white font-bold text-sm px-7 py-4 uppercase tracking-wider transition-all duration-300 backdrop-blur-sm flex items-center gap-2"
              >
                <svg class="w-4 h-4 fill-current text-[#ef6a26]" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Watch Trailer
              </button>
            </div>
          </div>
        </transition>
      </div>

      <!-- Right Release Date Box -->
      <transition name="fade" mode="out-in">
        <div :key="currentTrailer.id || currentIndex" class="absolute right-8 md:right-16 lg:right-24 top-28 flex flex-col items-end hidden sm:flex">
          <span class="text-gray-300 text-xs md:text-sm tracking-widest uppercase mb-1">In theater</span>
          <div class="relative min-h-[50px]">
            <span class="text-3xl md:text-5xl font-extrabold text-white">{{ currentTrailer.releaseDate }}</span>
            <!-- Hand-drawn Orange Underline -->
            <svg class="absolute -bottom-3 left-0 w-full h-3 text-[#ef6a26]" viewBox="0 0 100 10" preserveAspectRatio="none">
              <path d="M0 5 Q 50 10 100 5 Q 50 0 0 5 Z" fill="currentColor"/>
            </svg>
          </div>
        </div>
      </transition>

      <!-- Bottom Right Trailers Section -->
      <div class="relative mt-8 self-end w-full lg:w-auto">
        
        <!-- Hand-drawn Arrow + Corrected "Trailers" Label Pointer -->
        <div class="absolute -top-2 sm:top-0 md:top-1 -left-14 sm:-left-16 md:-left-20 flex flex-col items-center z-20 pointer-events-none hidden sm:flex">
          <span class="font-caveat text-2xl md:text-3xl text-white drop-shadow-md transform -rotate-6 tracking-wider">
            Trailers
          </span>
          <svg 
            class="w-12 h-16 text-white stroke-current fill-none transform -rotate-12 mt-0.5" 
            viewBox="0 0 50 60"
          >
            <path d="M 25 5 C 10 15, 10 32, 28 30 C 40 28, 42 12, 24 10 C 10 8, 12 40, 28 52" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M 20 44 L 28 52 L 32 42" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>

        <!-- Main Trailers Container -->
        <div class="flex flex-col space-y-4">
          
          <!-- Top Header Row: Pagination Dots -->
          <div class="flex items-center justify-between w-full min-h-[28px]">
            <span class="text-xl font-normal text-white tracking-wide font-sans sm:hidden">Trailers</span>
            
            <!-- 3 Pagination Dots (Exactly 3 dots) -->
            <div class="flex items-center space-x-2 ml-auto">
              <span 
                v-for="(t, idx) in trailers.slice(0, 3)" 
                :key="t.id || idx"
                @click="selectSlide(idx)"
                :class="[
                  'w-2.5 h-2.5 rounded-full cursor-pointer transition-all duration-300', 
                  idx === currentIndex ? 'bg-white' : 'bg-gray-500/70 hover:bg-gray-400'
                ]"
              ></span>
            </div>
          </div>

          <!-- Video Thumbnails Grid (First 3 only) -->
          <div class="flex items-center space-x-4 sm:space-x-5">
            <div 
              v-for="(trailer, idx) in trailers.slice(0, 3)" 
              :key="trailer.id || idx"
              @click="selectSlide(idx)"
              :class="[
                'relative w-44 sm:w-52 md:w-60 h-24 sm:h-28 md:h-32 overflow-hidden group cursor-pointer shrink-0 transition-all duration-300',
                idx === currentIndex 
                  ? 'border-2 border-[#ef6a26]' 
                  : 'border-2 border-white'
              ]"
            >
              <!-- Thumbnail Image -->
              <img :src="trailer.bgImage" :alt="trailer.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
              
              <!-- Center Circular Play Button -->
              <div class="absolute inset-0 flex items-center justify-center">
                <div 
                  @click.stop="openTrailerModal(trailer)"
                  class="w-10 h-10 rounded-full bg-white text-black flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform"
                  title="Play Trailer"
                >
                  <svg class="w-5 h-5 fill-current text-black translate-x-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- Floating Right Sidebar Widget (Next Slide Arrow) -->
    <div 
      @click="nextSlide"
      class="fixed right-0 top-1/2 transform -translate-y-1/2 bg-white rounded-l-full px-3 py-4 cursor-pointer hover:bg-gray-100 transition shadow-2xl z-30 hidden lg:flex items-center justify-center"
      title="Next Slide"
    >
      <svg class="w-6 h-6 text-[#106eba]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </div>

    <!-- Floating Refresh Icon Widget (Bottom Right - Cycle Slide) -->
    <div 
      @click="nextSlide"
      class="fixed bottom-12 right-6 bg-white/90 hover:bg-white p-3.5 rounded-full cursor-pointer hover:scale-110 transition-all duration-300 shadow-2xl z-30 hidden md:block group"
      title="Next Trailer Slide"
    >
      <svg class="w-6 h-6 text-[#ef6a26] group-hover:rotate-180 transition-transform duration-700" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
      </svg>
    </div>

    <!-- Bottom Film Perforation Strip -->
    <div class="relative z-20 w-full bg-white py-2 flex items-center overflow-hidden border-t-2 border-black">
      <div class="flex items-center space-x-3 w-full animate-marquee whitespace-nowrap">
        <div v-for="i in 60" :key="i" class="w-3.5 h-3.5 bg-black shrink-0"></div>
      </div>
    </div>

    <!-- Trailer Video Modal -->
    <Teleport to="body">
      <div 
        v-if="activeModalTrailer" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md animate-fade-in"
        @click.self="closeTrailerModal"
      >
        <div class="relative w-full max-w-4xl bg-slate-900 rounded-2xl border border-gray-800 overflow-hidden shadow-2xl">
          <!-- Modal Header -->
          <div class="flex items-center justify-between p-4 border-b border-gray-800 bg-black/40">
            <div>
              <span class="text-[#ef6a26] text-xs font-bold uppercase tracking-widest">{{ activeModalTrailer.subtitle }}</span>
              <h3 class="text-xl font-bold text-white">{{ activeModalTrailer.title }}</h3>
            </div>
            <button @click="closeTrailerModal" class="text-gray-400 hover:text-white p-2 rounded-full hover:bg-gray-800 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <!-- Video Frame or Featured Backdrop Image -->
          <div class="relative aspect-video w-full bg-black flex items-center justify-center">
            <iframe 
              v-if="activeModalTrailer.videoEmbed"
              :src="activeModalTrailer.videoEmbed"
              class="w-full h-full border-0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            ></iframe>
            <div v-else class="relative w-full h-full flex items-center justify-center">
              <img :src="activeModalTrailer.bgImage" class="w-full h-full object-cover opacity-60" />
              <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent flex flex-col items-center justify-center p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-[#ef6a26] text-white flex items-center justify-center mb-4 shadow-lg animate-bounce">
                  <svg class="w-8 h-8 fill-current translate-x-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <h4 class="text-2xl font-bold text-white mb-2">{{ activeModalTrailer.title }}</h4>
                <p class="text-sm text-gray-300 max-w-lg mb-6">{{ activeModalTrailer.director }}</p>
                <router-link 
                  :to="activeModalTrailer.ticketLink || '/movies'"
                  @click="closeTrailerModal"
                  class="bg-[#ef6a26] hover:bg-orange-600 text-white font-bold text-sm px-8 py-3 uppercase tracking-wider transition-colors rounded-full shadow-lg"
                >
                  Book Tickets Now
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '../services/api'

// Featured Default Slides (3 distinct items)
const defaultTrailers = [
  {
    id: 'dandelion',
    title: 'Dandelion &\nThe Wild',
    subtitle: 'Fantasy Adventure',
    director: 'Written and Directed by Marcus Vance / UK 2024',
    releaseDate: 'April 2024',
    bgImage: '/bg-dandelion.png',
    link: '/movies',
    ticketLink: '/movies',
    videoEmbed: 'https://www.youtube.com/embed/d9MyW72ELq0?autoplay=1'
  },
  {
    id: 'witcher',
    title: 'The Witcher\nSeason 2',
    subtitle: 'Action Movie',
    director: 'Written and Directed by Aleesha Rose / Ireland 2023',
    releaseDate: 'March 2023',
    bgImage: '/bg-skull.png',
    link: '/movies',
    ticketLink: '/movies',
    videoEmbed: 'https://www.youtube.com/embed/TJFYIom53hU?autoplay=1'
  },
  {
    id: 'avatar',
    title: 'Avatar: The Way\nof Water',
    subtitle: 'Sci-Fi Epic',
    director: 'Written and Directed by James Cameron / USA 2023',
    releaseDate: 'Dec 2022',
    bgImage: 'http://localhost:8000/storage/movies/XOP0koOlpALsrf2OJFhfSu79DMYpLEwHLBLMiciy.jpg',
    link: '/movies/5',
    ticketLink: '/movies/5',
    videoEmbed: null
  }
]

const trailers = ref(defaultTrailers.slice(0, 3))
const currentIndex = ref(0)
const activeModalTrailer = ref(null)
let autoPlayTimer = null

const currentTrailer = computed(() => {
  return trailers.value[currentIndex.value] || defaultTrailers[0]
})

const getImageUrl = (path) => {
  if (!path) return '/bg-skull.png'
  if (path.startsWith('http') || path.startsWith('/')) return path
  return `http://localhost:8000/storage/${path}`
}

const fetchMoviesForTrailers = async () => {
  try {
    const res = await api.get('/movies')
    if (res.data && res.data.length > 0) {
      const apiMovieTrailers = res.data.map(movie => ({
        id: movie.id,
        title: movie.title,
        subtitle: 'Now Showing',
        director: movie.description ? movie.description : `Showtime: ${new Date(movie.show_time).toLocaleString()}`,
        releaseDate: movie.show_time ? new Date(movie.show_time).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) : 'March 2026',
        bgImage: getImageUrl(movie.image),
        link: `/movies/${movie.id}`,
        ticketLink: `/movies/${movie.id}`,
        videoEmbed: null
      }))
      
      // Ensure strictly unique background images across all 3 trailer items
      const uniqueList = []
      const usedImages = new Set()
      
      const candidates = [...defaultTrailers, ...apiMovieTrailers]
      for (const item of candidates) {
        if (!usedImages.has(item.bgImage)) {
          usedImages.add(item.bgImage)
          uniqueList.push(item)
        }
        if (uniqueList.length >= 3) break
      }
      
      trailers.value = uniqueList.slice(0, 3)
    } else {
      trailers.value = defaultTrailers.slice(0, 3)
    }
  } catch (err) {
    console.error('Failed to load movies for homepage trailers:', err)
    trailers.value = defaultTrailers.slice(0, 3)
  }
}

const selectSlide = (index) => {
  currentIndex.value = index
}

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % trailers.value.length
}

const prevSlide = () => {
  currentIndex.value = (currentIndex.value - 1 + trailers.value.length) % trailers.value.length
}

const startAutoPlay = () => {
  stopAutoPlay()
  autoPlayTimer = setInterval(() => {
    nextSlide()
  }, 6000)
}

const pauseAutoPlay = () => {
  stopAutoPlay()
}

const stopAutoPlay = () => {
  if (autoPlayTimer) {
    clearInterval(autoPlayTimer)
    autoPlayTimer = null
  }
}

const openTrailerModal = (trailer) => {
  activeModalTrailer.value = trailer
}

const closeTrailerModal = () => {
  activeModalTrailer.value = null
}

onMounted(() => {
  fetchMoviesForTrailers()
  startAutoPlay()
})

onUnmounted(() => {
  stopAutoPlay()
})
</script>

<style scoped>
.font-caveat {
  font-family: 'Caveat', cursive;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s ease-out;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
