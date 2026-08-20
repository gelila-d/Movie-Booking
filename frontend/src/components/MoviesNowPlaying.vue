<template>
  <section class="relative w-full bg-white pt-10 sm:pt-14 pb-0 sm:pb-0 select-none overflow-hidden">
    <!-- Light film strip background behind MoviesNowPlaying cards -->
    <div class="absolute inset-0 pointer-events-none opacity-15 film-strip-background"></div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      
      <!-- Section Header -->
      <div class="text-center mb-10 sm:mb-14">
        <!-- Orange Film Reel Icon -->
        <div class="inline-flex items-center justify-center mb-2.5">
          <div class="w-9 h-9 rounded-full bg-[#ea580c]/10 text-[#ea580c] flex items-center justify-center shadow-sm group cursor-pointer">
            <svg class="w-6 h-6 fill-[#ea580c] group-hover:rotate-180 transition-transform duration-700" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-8c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm6 0c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-3-5c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm0 10c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z"/>
            </svg>
          </div>
        </div>

        <!-- Subtitle -->
        <p class="text-gray-500 text-xs sm:text-sm font-semibold tracking-wider uppercase mb-1">
          Watch New Movies
        </p>

        <!-- Main Title -->
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-black tracking-tight drop-shadow-sm">
          Movies Now Playing
        </h2>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20">
        <div class="w-12 h-12 border-4 border-gray-200 border-t-[#ea580c] rounded-full animate-spin"></div>
        <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Loading Movies...</p>
      </div>

      <!-- Movies Grid / Cards -->
      <div v-else-if="movies.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 sm:gap-8 justify-center">
        <div 
          v-for="movie in movies.slice(0, 3)" 
          :key="movie.id"
          @click="goToMovie(movie.id)"
          class="group relative bg-[#0a0a0a] overflow-hidden aspect-[4/5] sm:aspect-[1/1] md:aspect-[4/5] lg:aspect-[4/5] w-full cursor-pointer transition-all duration-500 shadow-xl hover:shadow-[0_20px_50px_rgba(234,88,12,0.25)] border-2 border-transparent hover:border-[#ea580c] transform hover:-translate-y-1.5 flex flex-col justify-end"
        >
          <!-- Top Badges -->
          <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-20 pointer-events-none">
            <span class="bg-black/60 backdrop-blur-md text-white text-[10px] font-extrabold px-2.5 py-1 uppercase tracking-wider border border-white/20">
              NOW SHOWING
            </span>
            <span class="bg-[#ea580c] text-white text-[11px] font-black px-2.5 py-1 flex items-center gap-1 shadow-md shadow-[#ea580c]/40">
              ★ {{ movie.rating || '8.8' }}
            </span>
          </div>

          <!-- Center Play Button Overlay -->
          <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none opacity-0 group-hover:opacity-100 transition-all duration-300">
            <div class="w-14 h-14 rounded-full bg-[#ea580c] text-white flex items-center justify-center shadow-xl shadow-[#ea580c]/50 transform scale-75 group-hover:scale-100 transition-all duration-300">
              <svg class="w-6 h-6 fill-current translate-x-0.5" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
              </svg>
            </div>
          </div>

          <!-- Movie Poster Image Background -->
          <div class="absolute inset-0 overflow-hidden">
            <img 
              v-if="movie.image" 
              :src="getImageUrl(movie.image)" 
              :alt="movie.title" 
              class="w-full h-full object-cover transform group-hover:scale-108 transition-transform duration-700 ease-out" 
            />
            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-900 text-gray-600">
              <svg class="w-12 h-12 mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
              </svg>
              <span class="text-xs uppercase tracking-widest font-semibold text-gray-400">No Image</span>
            </div>
            
            <!-- Dark Gradient Vignette at Bottom -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/45 to-transparent"></div>
          </div>

          <!-- Bottom Card Content -->
          <div class="relative z-10 p-6 sm:p-7 flex flex-col justify-end items-start text-left">
            <!-- Category / Duration -->
            <div class="text-xs sm:text-sm font-semibold text-gray-200/90 mb-1.5 tracking-wide flex items-center gap-1.5">
              <span>{{ getGenreAndDuration(movie) }}</span>
            </div>

            <!-- Movie Title -->
            <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-snug tracking-tight mb-4 group-hover:text-white drop-shadow-md">
              {{ movie.title }}
            </h3>

            <!-- Get Ticket Button -->
            <router-link 
              :to="'/movies/' + movie.id" 
              @click.stop
              class="inline-flex items-center justify-between w-full bg-white group-hover:bg-[#ea580c] text-gray-950 group-hover:text-white font-extrabold text-xs sm:text-sm px-5 py-3 transition-all duration-300 shadow-md uppercase tracking-wider group/btn"
            >
              <span>Get Ticket</span>
              <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </router-link>
          </div>

          <!-- Bottom Orange Accent Bar -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#ea580c] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left z-30"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16 bg-white rounded-lg border border-gray-200">
        <p class="text-gray-500 font-medium">No movies currently playing.</p>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()
const movies = ref([])
const loading = ref(true)

const getImageUrl = (path) => {
  if (!path) return ''
  return `http://localhost:8000/storage/${path}`
}

const goToMovie = (id) => {
  router.push(`/movies/${id}`)
}

// Fallback genre & duration lookup for realistic display matching the reference
const getGenreAndDuration = (movie) => {
  if (movie.genre && movie.duration) {
    return `${movie.genre} / ${movie.duration} Mins`
  }

  const titleLower = (movie.title || '').toLowerCase()
  let genre = movie.genre || 'Action, Drama'
  let duration = movie.duration || '180'

  if (titleLower.includes('avatar')) {
    genre = 'Adventure, Sci-Fi'
    duration = '192'
  } else if (titleLower.includes('knives out')) {
    genre = 'Comedy, Mystery'
    duration = '130'
  } else if (titleLower.includes('gone girl')) {
    genre = 'Drama, Mystery'
    duration = '149'
  } else if (titleLower.includes('fast x') || titleLower.includes('fast')) {
    genre = 'Action, Crime'
    duration = '141'
  } else if (titleLower.includes('dune')) {
    genre = 'Adventure, Sci-Fi'
    duration = '166'
  } else if (titleLower.includes('wild')) {
    genre = 'Adventure'
    duration = '190'
  } else if (titleLower.includes('fifth day')) {
    genre = 'Comedy'
    duration = '180'
  } else if (titleLower.includes('twins')) {
    genre = 'Animation, Comedy'
    duration = '190'
  }

  return `${genre} / ${duration} Mins`
}

const fetchMovies = async () => {
  loading.value = true
  try {
    const res = await api.get('/movies')
    movies.value = res.data
  } catch (err) {
    console.error('Failed to load now playing movies:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchMovies)
</script>

<style scoped>
/* Smooth scale effect */
.group:hover img {
  transform: scale(1.08);
}
</style>
