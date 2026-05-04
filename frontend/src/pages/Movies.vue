<template>
  <div class="container">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-1">Available Movies</h1>
        <p class="text-gray-600">Choose a movie to see details and book tickets</p>
      </div>
      <div class="relative w-full md:w-80">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
          🔍
        </span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search movies..." 
          class="pl-10 pr-4 py-2 w-full border border-yellow-200 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent outline-none transition-all shadow-sm"
        />
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-yellow-500 border-t-transparent rounded-full"></div>
    </div>

    <div v-else-if="filteredMovies.length === 0" class="card text-center py-20 bg-gray-50 border-dashed border-gray-200">
      <p class="text-gray-600">No movies found matching "{{ searchQuery }}"</p>
      <button v-if="searchQuery" @click="searchQuery = ''" class="mt-4 text-yellow-600 font-bold hover:underline">Clear Search</button>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <MovieCard 
        v-for="movie in filteredMovies" 
        :key="movie.id" 
        :movie="movie" 
      />
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