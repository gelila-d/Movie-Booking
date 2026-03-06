<template>
  <div class="container">
    <div class="mb-10">
      <h1 class="text-3xl font-bold text-gray-900 mb-1">Available Movies</h1>
      <p class="text-gray-600">Choose a movie to see details and book tickets</p>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-yellow-500 border-t-transparent rounded-full"></div>
    </div>

    <div v-else-if="movies.length === 0" class="card text-center py-20">
      <p class="text-gray-600">No movies available at the moment.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <MovieCard 
        v-for="movie in movies" 
        :key="movie.id" 
        :movie="movie" 
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"
import MovieCard from "../components/MovieCard.vue"

const movies = ref([])
const loading = ref(true)

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