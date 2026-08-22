<template>
  <div class="group relative w-full aspect-[2/3] overflow-hidden bg-gray-900 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-800/60 hover:border-[#ef6a26]/50 rounded-2xl">
    <!-- Image Background -->
    <img 
      v-if="movie.image" 
      :src="getImageUrl(movie.image)" 
      alt="Movie Poster" 
      class="w-full h-full object-cover transform group-hover:scale-108 transition-transform duration-500 ease-out" 
    />
    <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-900 text-gray-500">
      <svg class="w-8 h-8 mb-1 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
      </svg>
      <span class="font-medium text-[10px] tracking-wider uppercase">No Poster</span>
    </div>

    <!-- Top Badges & Heart Toggle -->
    <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between z-30">
      <span class="bg-black/60 backdrop-blur-md text-white text-[9px] font-semibold px-2 py-0.5 uppercase tracking-wider border border-white/10 rounded">
        4K HD
      </span>
      
      <div class="flex items-center gap-1.5">
        <span class="bg-[#ef6a26] text-white text-[10px] font-bold px-2 py-0.5 flex items-center gap-1 shadow-sm rounded">
          <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
          </svg>
          {{ movie.rating || '8.5' }}
        </span>

        <!-- Watchlist Heart Toggle Button -->
        <button 
          @click.stop.prevent="toggleWatchlist" 
          class="w-7 h-7 rounded-full bg-black/60 backdrop-blur-md border border-white/20 flex items-center justify-center text-xs transition-transform hover:scale-115 active:scale-95 shadow-md"
          :title="inWatchlist ? 'Remove from Watchlist' : 'Add to Watchlist'"
        >
          <span v-if="inWatchlist" class="text-red-500 scale-110">❤️</span>
          <span v-else class="text-slate-300 opacity-80 hover:opacity-100">🤍</span>
        </button>
      </div>
    </div>

    <!-- Center Play Icon Hover Overlay -->
    <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-300">
      <div class="w-10 h-10 rounded-full bg-[#ef6a26]/90 text-white flex items-center justify-center shadow-md shadow-[#ef6a26]/40 transform scale-75 group-hover:scale-100 transition-transform duration-300">
        <svg class="w-4 h-4 fill-current translate-x-0.5" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z"/>
        </svg>
      </div>
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 z-10 bg-gradient-to-t from-black via-black/40 to-transparent opacity-90 group-hover:opacity-95 transition-opacity duration-300"></div>

    <!-- Content (Bottom Anchored) -->
    <div class="absolute inset-x-0 bottom-0 z-20 p-3.5 sm:p-4 flex flex-col justify-end text-white text-left">
      <!-- Info (Genre & Duration) -->
      <div class="text-[10px] text-[#ef6a26] font-semibold uppercase tracking-wider mb-0.5 flex items-center space-x-1.5 truncate">
        <span class="truncate">{{ movie.genre || 'Action, Thriller' }}</span>
        <span class="text-gray-500">•</span>
        <span class="text-gray-300 font-normal normal-case shrink-0">{{ movie.duration || '180' }}m</span>
      </div>

      <!-- Title -->
      <h3 class="text-xs sm:text-sm font-semibold mb-2.5 leading-snug tracking-tight group-hover:text-[#ef6a26] transition-colors duration-300 line-clamp-2">
        {{ movie.title }}
      </h3>

      <!-- Action Button -->
      <router-link 
        :to="'/movies/' + movie.id" 
        class="inline-flex items-center justify-between bg-white hover:bg-[#ef6a26] text-black hover:text-white font-bold text-[10px] uppercase tracking-wider px-3.5 py-2 w-full transition-all duration-300 shadow-md group/btn rounded-lg"
      >
        <span>Get Ticket</span>
        <svg class="w-3 h-3 transform group-hover/btn:translate-x-0.5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </router-link>
    </div>

    <!-- Bottom Accent Line -->
    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ef6a26] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left z-30"></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const props = defineProps({
  movie: {
    type: Object,
    required: true
  },
  isWatchlisted: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['watchlistToggled'])

const inWatchlist = ref(props.isWatchlisted)

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

const checkWatchlistState = async () => {
    if (props.isWatchlisted) {
        inWatchlist.value = true;
        return;
    }
    if (!localStorage.getItem('token')) return;
    try {
        const res = await api.get('/watchlist/ids');
        if (Array.isArray(res.data) && res.data.includes(props.movie.id)) {
            inWatchlist.value = true;
        }
    } catch (e) {
        // Guest user or error
    }
}

const toggleWatchlist = async () => {
    if (!localStorage.getItem('token')) {
        alert("Please log in to save movies to your watchlist.");
        return;
    }

    try {
        const res = await api.post('/watchlist/toggle', { movie_id: props.movie.id });
        if (res.data.status === 'added') {
            inWatchlist.value = true;
        } else {
            inWatchlist.value = false;
            emit('watchlistToggled', props.movie.id);
        }
    } catch (err) {
        console.error("Failed to toggle watchlist:", err);
    }
}

onMounted(checkWatchlistState)
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
