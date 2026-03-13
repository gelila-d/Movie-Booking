<template>
  <div class="card flex flex-col h-full !p-0 overflow-hidden">
    <div v-if="movie.image" class="w-full h-48 bg-gray-100 flex-shrink-0">
      <img :src="getImageUrl(movie.image)" alt="Movie Poster" class="w-full h-full object-cover" />
    </div>
    <div class="flex-grow p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-2">{{ movie.title }}</h3>
      <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ movie.description }}</p>
      <div class="space-y-1 text-sm text-gray-600 mb-4">
        <p>🕒 {{ movie.show_time ? new Date(movie.show_time).toLocaleString() : 'TBD' }}</p>
        <p>🪑 {{ movie.available_seats }} seats available</p>
      </div>
    </div>
    <div class="px-6 pb-6">
      <router-link 
        :to="'/movies/' + movie.id" 
        class="btn-primary text-center block w-full"
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
