<template>
  <div class="container space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1">Admin Dashboard</h1>
        <p class="text-gray-500">Manage the movie catalog and ticketing</p>
      </div>
      <button 
        v-if="!showForm" 
        @click="openCreate" 
        class="btn-primary"
      >
        + Add New Movie
      </button>
    </div>

    <!-- Movie Form (Create/Edit) -->
    <div v-if="showForm" class="card space-y-6 border-blue-900/30">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-white">{{ editingId ? 'Edit Movie' : 'Add New Movie' }}</h2>
        <button @click="closeForm" class="text-gray-500 hover:text-white transition-colors">Cancel</button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400">Movie Title</label>
          <input v-model="form.title" placeholder="Title" />
        </div>
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400">Show Time</label>
          <input v-model="form.show_time" placeholder="e.g. 19:30" />
        </div>
        <div class="md:col-span-2 space-y-1">
          <label class="text-xs font-medium text-gray-400">Description</label>
          <input v-model="form.description" placeholder="Movie description..." />
        </div>
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400">Total Capacity (Seats)</label>
          <input v-model="form.total_seats" type="number" placeholder="100" />
        </div>
        <div class="flex items-end">
          <button @click="saveMovie" :disabled="saving" class="btn-primary w-full">
            {{ saving ? 'Saving...' : 'Save Movie' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Movie List -->
    <div class="space-y-4">
      <h2 class="text-xl font-bold text-white">Catalog ({{ movies.length }})</h2>
      
      <div v-if="loading" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
      </div>

      <div v-for="movie in movies" :key="movie.id" class="card flex flex-col md:flex-row md:items-center justify-between border-gray-800">
        <div class="flex-grow">
          <h3 class="text-lg font-bold text-white mb-1">{{ movie.title }}</h3>
          <p class="text-xs text-gray-500 mb-2 truncate max-w-md">{{ movie.description }}</p>
          <div class="flex items-center text-xs text-gray-400 space-x-4">
            <span class="font-medium">🕒 {{ movie.show_time }}</span>
            <span class="font-medium">🪑 {{ movie.available_seats }} / {{ movie.total_seats }} Seats</span>
          </div>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
          <button @click="openEdit(movie)" class="text-blue-500 hover:text-blue-400 text-sm font-bold bg-blue-500/10 px-3 py-1.5 rounded transition-colors">
            Edit
          </button>
          <button @click="deleteMovie(movie.id)" class="text-red-500 hover:text-red-400 text-sm font-bold bg-red-500/10 px-3 py-1.5 rounded transition-colors">
            Delete
          </button>
        </div>
      </div>
      
      <div v-if="!loading && movies.length === 0" class="text-center py-20 bg-gray-900 border border-dashed border-gray-800 rounded-xl">
        <p class="text-gray-600">No movies found in the catalog.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"

const movies = ref([])
const loading = ref(true)
const saving = ref(false)
const showForm = ref(false)
const editingId = ref(null)

const form = ref({
    title: "",
    description: "",
    show_time: "",
    total_seats: ""
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

const openCreate = () => {
    editingId.value = null
    form.value = { title: "", description: "", show_time: "", total_seats: "" }
    showForm.value = true
}

const openEdit = (movie) => {
    editingId.value = movie.id
    form.value = { ...movie }
    showForm.value = true
}

const closeForm = () => {
    showForm.value = false
    editingId.value = null
}

const saveMovie = async () => {
    if (!form.value.title || !form.value.description || !form.value.show_time || !form.value.total_seats) {
        alert("All fields are required")
        return
    }

    saving.value = true
    try {
        if (editingId.value) {
            await api.put(`/movies/${editingId.value}`, form.value)
        } else {
            await api.post("/movies", form.value)
        }
        closeForm()
        loadMovies()
    } catch (err) {
        alert("Failed to save movie")
        console.error(err)
    } finally {
        saving.value = false
    }
}

const deleteMovie = async (id) => {
    if(!confirm("Remove this movie from the catalog?")) return
    try {
        await api.delete(`/movies/${id}`)
        loadMovies()
    } catch (err) {
        alert("Deletion failed")
    }
}

onMounted(loadMovies)
</script>