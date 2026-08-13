<template>
  <div class="w-full max-w-7xl mx-auto px-6 py-10">
    <!-- Header & Filter Bar -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 pb-6 border-b border-gray-200">
      <div>
        <div class="text-[#ef6a26] text-xs font-bold uppercase tracking-widest mb-1">Explore Cinema</div>
        <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
          Now Showing & Featured Movies
        </h1>
      </div>

      <!-- Search Bar -->
      <div class="relative w-full md:w-72 lg:w-80">
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search movies by title or genre..." 
          class="w-full bg-white border border-gray-300 rounded-full py-2.5 pl-4 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/20 transition-all shadow-sm"
        />
        <svg class="absolute right-3.5 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>

    <!-- Category Pills -->
    <div class="flex items-center space-x-3 overflow-x-auto pb-4 mb-8 scrollbar-none">
      <button 
        v-for="category in categories" 
        :key="category"
        @click="selectedCategory = category"
        :class="[
          'px-5 py-2 rounded-full text-xs font-semibold uppercase tracking-wider transition-all whitespace-nowrap',
          selectedCategory === category 
            ? 'bg-[#ef6a26] text-white shadow-md shadow-[#ef6a26]/30' 
            : 'bg-gray-200/70 text-gray-700 hover:bg-gray-300'
        ]"
      >
        {{ category }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-24 space-y-4">
      <div class="w-12 h-12 border-4 border-gray-200 border-t-[#ef6a26] rounded-full animate-spin"></div>
      <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Loading movies...</p>
    </div>

    <!-- No Movies Found -->
    <div v-else-if="filteredMovies.length === 0" class="text-center py-24 bg-white rounded-2xl border border-gray-200/80 shadow-sm">
      <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-1">No movies found</h3>
      <p class="text-gray-500 text-sm mb-4">We couldn't find any movies matching your search criteria.</p>
      <button 
        @click="resetFilters" 
        class="bg-[#ef6a26] text-white text-xs font-bold uppercase tracking-wider px-6 py-2.5 rounded-full hover:bg-orange-600 transition-colors shadow-md"
      >
        Reset Filters
      </button>
    </div>

    <!-- Movies Grid -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
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
const selectedCategory = ref("All")

const categories = ["All", "Action", "Thriller", "Adventure", "Comedy", "Animation", "Sci-Fi"]

const filteredMovies = computed(() => {
    return movies.value.filter(movie => {
        const matchesSearch = !searchQuery.value || 
            movie.title.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
            (movie.description && movie.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesCategory = selectedCategory.value === "All" || 
            (movie.genre && movie.genre.toLowerCase().includes(selectedCategory.value.toLowerCase()));

        return matchesSearch && matchesCategory;
    });
})

const resetFilters = () => {
    searchQuery.value = "";
    selectedCategory.value = "All";
}

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