<template>
  <div class="card flex flex-col h-full">
    <div class="flex-grow">
      <h3 class="text-xl font-bold text-white mb-2">{{ movie.title }}</h3>
      <p class="text-gray-400 text-sm mb-4 line-clamp-2">{{ movie.description }}</p>
      <div class="space-y-1 text-sm text-gray-500 mb-4">
        <p>🕒 {{ movie.show_time }}</p>
        <p>🪑 {{ movie.available_seats }} seats available</p>
      </div>
    </div>
    <router-link 
      :to="'/movies/' + movie.id" 
      class="btn-primary text-center block"
    >
      View Details
    </router-link>
  </div>
</template>

<script setup>
defineProps({
  movie: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
