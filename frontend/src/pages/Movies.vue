<template>
  <div class="container relative">
    <!-- Hero section with futuristic styling -->
    <div class="mb-12 relative">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 via-purple-500/10 to-cyan-500/10 rounded-3xl blur-xl"></div>
      <div class="relative backdrop-blur-xl bg-slate-900/30 border border-blue-500/20 rounded-3xl p-8 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="space-y-4">
            <h1 class="neon-text text-4xl lg:text-5xl font-bold font-orbitron tracking-wider">
              AVAILABLE MOVIES
            </h1>
            <p class="text-slate-300 text-lg font-light">
              Experience cinema in a whole new dimension
            </p>
            <div class="flex items-center gap-4 text-sm text-slate-400 font-mono">
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span>{{ movies.length }} MOVIES ONLINE</span>
              </div>
              <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                <span>REAL-TIME BOOKING</span>
              </div>
            </div>
          </div>

          <!-- Futuristic search bar -->
          <div class="w-full lg:w-96">
            <div class="relative group">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-500/30 to-purple-500/30 rounded-xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              <div class="relative flex items-center">
                <div class="absolute left-4 text-blue-400 z-10">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="SEARCH MOVIES..." 
                  class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-600/50 rounded-xl text-slate-100 placeholder-slate-400 focus:border-blue-500/50 focus:bg-slate-800/70 transition-all duration-300 backdrop-blur-xl font-mono text-sm tracking-wide"
                />
                <div class="absolute right-3 opacity-50">
                  <div class="w-1 h-4 bg-blue-400 animate-pulse"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="relative">
        <div class="w-16 h-16 border-4 border-slate-700 border-t-blue-500 rounded-full animate-spin"></div>
        <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-purple-500 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
      </div>
    </div>

    <!-- No movies found -->
    <div v-else-if="filteredMovies.length === 0" class="relative">
      <div class="absolute inset-0 bg-gradient-to-r from-red-500/10 to-orange-500/10 rounded-3xl blur-xl"></div>
      <div class="relative card text-center py-20 bg-slate-800/30 border-dashed border-slate-600/50">
        <div class="mb-6">
          <div class="w-20 h-20 mx-auto bg-slate-700/50 rounded-full flex items-center justify-center mb-4">
            <span class="text-3xl">🔍</span>
          </div>
          <p class="text-slate-300 text-lg font-mono">NO MOVIES FOUND</p>
          <p class="text-slate-400 text-sm mt-2" v-if="searchQuery">
            No results for "<span class="text-blue-400 font-mono">{{ searchQuery }}</span>"
          </p>
        </div>
        <button 
          v-if="searchQuery" 
          @click="searchQuery = ''" 
          class="btn-secondary px-6 py-2 mx-auto"
        >
          CLEAR SEARCH
        </button>
      </div>
    </div>

    <!-- Movies grid -->
    <div v-else class="relative">
      <!-- Grid background effect -->
      <div class="absolute inset-0 opacity-20 pointer-events-none">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 h-full">
          <div v-for="i in 6" :key="i" class="border border-blue-500/10 rounded-2xl"></div>
        </div>
      </div>
      
      <div class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="(movie, index) in filteredMovies" 
          :key="movie.id"
          class="animate-float"
          :style="{ animationDelay: `${index * 0.1}s` }"
        >
          <MovieCard :movie="movie" />
        </div>
      </div>
    </div>

    <!-- Floating particles effect -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
      <div class="particle absolute w-1 h-1 bg-blue-400/30 rounded-full" style="left: 10%; top: 20%; animation: float 4s ease-in-out infinite;"></div>
      <div class="particle absolute w-1 h-1 bg-purple-400/30 rounded-full" style="left: 80%; top: 60%; animation: float 5s ease-in-out infinite reverse;"></div>
      <div class="particle absolute w-1 h-1 bg-cyan-400/30 rounded-full" style="left: 30%; top: 80%; animation: float 6s ease-in-out infinite;"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue"
import api from "../services/api"
import MovieCard from "../components/MovieCard.vue"

const movies = ref([])
const loading = ref(true)
const searchQuery = ref("")

const filteredMovies = computed(() => {
    if (!searchQuery.value) return movies.value
    const query = searchQuery.value.toLowerCase()
    return movies.value.filter(movie => 
        movie.title.toLowerCase().includes(query) || 
        (movie.description && movie.description.toLowerCase().includes(query))
    )
})

const loadMovies = async () => {
    loading.value = true
    try {
        const res = await api.get("/movies")
        movies.value = res.data
    } catch (err) {
        console.error(err)
    } finally {
        loading.value = false
    }
}

onMounted(loadMovies)
</script>