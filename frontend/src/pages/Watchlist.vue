<template>
  <div class="w-full max-w-7xl mx-auto px-6 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-slate-800">
      <div>
        <div class="text-[#ef6a26] text-xs font-bold uppercase tracking-widest mb-1 font-mono">YOUR SAVED MOVIES</div>
        <h1 class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight font-orbitron flex items-center gap-3">
          <span>My Watchlist</span>
          <span v-if="movies.length" class="text-sm px-3 py-1 bg-[#ef6a26]/20 border border-[#ef6a26]/40 text-[#ef6a26] rounded-full font-mono font-bold">
            {{ movies.length }} Movie{{ movies.length !== 1 ? 's' : '' }}
          </span>
        </h1>
      </div>

      <router-link 
        to="/movies" 
        class="inline-flex items-center gap-2 text-slate-300 hover:text-white transition-colors text-sm font-semibold"
      >
        <span>+ Explore More Movies</span>
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-24 space-y-4">
      <div class="w-12 h-12 border-4 border-gray-200 border-t-[#ef6a26] rounded-full animate-spin"></div>
      <p class="text-sm font-medium text-gray-400 uppercase tracking-wider font-mono">Loading Watchlist...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="movies.length === 0" class="text-center py-24 bg-slate-900/50 rounded-3xl border border-slate-800/80 shadow-2xl space-y-4">
      <div class="w-20 h-20 mx-auto rounded-full bg-slate-800/80 flex items-center justify-center text-4xl shadow-inner">
        ❤️
      </div>
      <div class="space-y-1">
        <h3 class="text-xl font-bold text-white font-orbitron">Your Watchlist is Empty</h3>
        <p class="text-slate-400 text-sm max-w-md mx-auto">
          Save movies you're interested in by clicking the heart icon on any movie card to quickly find and book showtimes later.
        </p>
      </div>
      <div class="pt-2">
        <router-link 
          to="/movies" 
          class="btn-primary inline-block px-8 py-3 text-xs font-bold uppercase tracking-wider"
        >
          Explore Movies Now
        </router-link>
      </div>
    </div>

    <!-- Watchlisted Movies Grid -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5 sm:gap-6">
      <MovieCard 
        v-for="movie in movies" 
        :key="movie.id"
        :movie="movie" 
        :isWatchlisted="true"
        @watchlistToggled="handleWatchlistToggled"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import MovieCard from '../components/MovieCard.vue'

const movies = ref([])
const loading = ref(true)

const fetchWatchlist = async () => {
  loading.value = true
  try {
    const res = await api.get('/watchlist')
    movies.value = res.data
  } catch (err) {
    console.error('Failed to load watchlist:', err)
  } finally {
    loading.value = false
  }
}

const handleWatchlistToggled = (movieId) => {
  movies.value = movies.value.filter(m => m.id !== movieId)
}

onMounted(fetchWatchlist)
</script>
