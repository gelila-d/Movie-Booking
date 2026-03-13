<template>
  <div class="flex flex-col h-[450px] rounded-2xl overflow-hidden relative group shadow-md hover:shadow-xl transition-shadow bg-gray-900 border border-gray-800">
    <!-- Image Background -->
    <div v-if="movie.image" class="absolute inset-0 w-full h-full z-0 overflow-hidden">
      <img :src="getImageUrl(movie.image)" alt="Movie Poster" class="w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-105 transition-all duration-700 ease-in-out" />
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
    </div>
    
    <div v-else class="absolute inset-0 w-full h-full z-0 flex items-center justify-center bg-gray-800">
      <span class="text-gray-500 font-bold text-xl">No Image</span>
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col h-full p-6 justify-end text-white">
      <h3 class="text-2xl font-bold mb-2 text-white">{{ movie.title }}</h3>
      <p class="text-gray-300 text-sm mb-4 line-clamp-2">{{ movie.description }}</p>
      <div class="space-y-1 text-sm text-gray-400 mb-5">
        <p class="flex items-center gap-2"><span>🕒</span> {{ movie.show_time ? new Date(movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD' }}</p>
        <p class="flex items-center gap-2"><span>🪑</span> {{ movie.available_seats }} seats available</p>
      </div>
      <router-link 
        :to="'/movies/' + movie.id" 
        class="bg-yellow-500 text-black font-bold py-3 px-4 rounded-lg text-center block w-full hover:bg-yellow-400 transition-colors"
      >
        View Details
      </router-link>
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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
