<template>
  <div class="movie-card flex flex-col h-[500px] rounded-2xl overflow-hidden relative group transition-all duration-500 hover:scale-105">
    <!-- Holographic border effect -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/30 via-purple-500/30 to-cyan-500/30 p-[2px] group-hover:from-blue-400 group-hover:via-purple-400 group-hover:to-cyan-400 transition-all duration-500">
      <div class="h-full w-full rounded-2xl bg-slate-900/90 backdrop-blur-xl border border-slate-700/50 group-hover:border-blue-500/50 transition-all duration-500"></div>
    </div>
    
    <!-- Image Background -->
    <div v-if="movie.image" class="absolute inset-[2px] w-[calc(100%-4px)] h-[calc(100%-4px)] z-0 overflow-hidden rounded-2xl">
      <img :src="getImageUrl(movie.image)" alt="Movie Poster" class="w-full h-full object-cover opacity-40 group-hover:opacity-60 group-hover:scale-110 transition-all duration-700 ease-out" />
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/70 to-transparent"></div>
      <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-cyan-500/10"></div>
    </div>
    
    <div v-else class="absolute inset-[2px] w-[calc(100%-4px)] h-[calc(100%-4px)] z-0 flex items-center justify-center bg-slate-800/90 rounded-2xl">
      <div class="text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-slate-700 rounded-full flex items-center justify-center">
          <span class="text-2xl">🎬</span>
        </div>
        <span class="text-slate-400 font-medium">No Image</span>
      </div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/70 to-transparent rounded-2xl"></div>
    </div>

    <!-- Scan line effect -->
    <div class="scan-line absolute inset-0 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

    <!-- Content -->
    <div class="relative z-30 flex flex-col h-full p-6 justify-end text-slate-100">
      <!-- Rating badge -->
      <div class="absolute top-6 right-6">
        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-3 py-1 rounded-full text-xs font-bold">
          ★ {{ (Math.random() * 2 + 7).toFixed(1) }}
        </div>
      </div>

      <div class="space-y-4">
        <h3 class="text-2xl font-bold text-white group-hover:text-blue-300 transition-colors duration-300 font-orbitron">
          {{ movie.title.toUpperCase() }}
        </h3>
        
        <p class="text-slate-300 text-sm line-clamp-3 leading-relaxed">{{ movie.description }}</p>
        
        <!-- Movie info with futuristic styling -->
        <div class="space-y-2 text-sm">
          <div class="flex items-center gap-3 text-slate-400">
            <div class="w-1 h-1 bg-blue-400 rounded-full animate-pulse"></div>
            <span class="font-mono">{{ movie.show_time ? new Date(movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD' }}</span>
          </div>
          <div class="flex items-center gap-3 text-slate-400">
            <div class="w-1 h-1 bg-green-400 rounded-full animate-pulse"></div>
            <span class="font-mono">{{ movie.available_seats }} SEATS AVAILABLE</span>
          </div>
        </div>

        <!-- Action button -->
        <router-link 
          :to="'/movies/' + movie.id" 
          class="btn-primary w-full text-center block py-3 px-4 text-sm font-bold tracking-wider relative overflow-hidden group/btn"
        >
          <span class="relative z-10">ACCESS DETAILS</span>
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700"></div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  movie: {
    type: Object,
    required: true
  }
})

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.font-orbitron {
  font-family: 'Orbitron', monospace;
}

.scan-line::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg, transparent, #00d4ff, transparent);
  animation: scan 2s linear infinite;
}

@keyframes scan {
  0% { left: -100%; }
  100% { left: 100%; }
}

.movie-card::before {
  content: '';
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  background: linear-gradient(45deg, #3b82f6, #8b5cf6, #06b6d4, #3b82f6);
  background-size: 400% 400%;
  border-radius: 1rem;
  opacity: 0;
  z-index: -1;
  transition: opacity 0.3s ease;
  animation: gradient 3s ease infinite;
}

.movie-card:hover::before {
  opacity: 0.7;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
