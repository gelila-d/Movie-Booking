<template>
  <div class="container space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-1">Admin Dashboard</h1>
        <p class="text-gray-600">Manage the movie catalog and ticketing</p>
      </div>
      <button 
        v-if="!showForm && activeTab === 'catalog'" 
        @click="openCreate" 
        class="btn-primary"
      >
        + Add New Movie
      </button>
    </div>

    <!-- Statistics Dashboard -->
    <div v-if="stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-100 flex flex-col">
        <span class="text-sm font-medium text-gray-500 mb-1">Total Movies</span>
        <span class="text-3xl font-bold text-gray-900">{{ stats.summary.total_movies }}</span>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-100 flex flex-col">
        <span class="text-sm font-medium text-gray-500 mb-1">Total Tickets Sold</span>
        <span class="text-3xl font-bold text-yellow-600">{{ stats.summary.booked_seats }}</span>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-100 flex flex-col">
        <span class="text-sm font-medium text-gray-500 mb-1">Remaining Seats</span>
        <span class="text-3xl font-bold text-green-600">{{ stats.summary.available_seats }}</span>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-100 flex flex-col">
        <span class="text-sm font-medium text-gray-500 mb-1">Fill Rate</span>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-blue-600">{{ stats.summary.overall_fill_rate }}%</span>
          <div class="flex-grow h-2 bg-gray-100 rounded-full mb-2 overflow-hidden">
            <div class="h-full bg-blue-500" :style="{ width: stats.summary.overall_fill_rate + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-gray-200 gap-4">
      <div class="flex">
        <button 
          @click="activeTab = 'catalog'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2"
          :class="activeTab === 'catalog' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
        >
          Movie Catalog
        </button>
        <button 
          @click="activeTab = 'bookings'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2"
          :class="activeTab === 'bookings' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
        >
          Recent Bookings
        </button>
      </div>
      <div class="relative w-full md:w-64 mb-2 md:mb-0">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">🔍</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          :placeholder="activeTab === 'catalog' ? 'Search movies...' : 'Search users or movies...'" 
          class="pl-10 pr-4 py-1.5 w-full border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-500 outline-none text-sm"
        />
      </div>
    </div>

    <!-- Movie Form (Create/Edit) -->
    <div v-if="showForm" class="card space-y-6 border-yellow-200 shadow-sm">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900">{{ editingId ? 'Edit Movie' : 'Add New Movie' }}</h2>
        <button @click="closeForm" class="text-gray-500 hover:text-gray-900 transition-colors">Cancel</button>
      </div>

      <!-- Error Message Box -->
      <div v-if="errorMessage" class="p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
        {{ errorMessage }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-700">Movie Title</label>
          <input v-model="form.title" placeholder="Title" />
        </div>
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-700">Show Time</label>
          <input v-model="form.show_time" type="datetime-local" class="w-full text-black px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" />
        </div>
        <div class="md:col-span-2 space-y-1">
          <label class="text-xs font-medium text-gray-700">Description</label>
          <input v-model="form.description" placeholder="Movie description..." />
        </div>
        <div class="md:col-span-2 space-y-1">
          <label class="text-xs font-medium text-gray-700">Movie Image</label>
          <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-black px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white" />
        </div>
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-700">Total Capacity (Seats)</label>
          <input v-model="form.total_seats" @input="form.total_seats = Math.abs($event.target.value) || ''" type="number" min="1" placeholder="100" />
        </div>
        <div class="flex items-end">
          <button @click="saveMovie" :disabled="saving" class="btn-primary w-full">
            {{ saving ? 'Saving...' : 'Save Movie' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Catalog Tab Content -->
    <div v-if="activeTab === 'catalog'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">Catalog ({{ filteredMovies.length }})</h2>
        <button @click="loadMovies" class="text-xs text-yellow-600 hover:underline">Refresh</button>
      </div>
      
      <div v-if="loading" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-yellow-500 border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredMovies.length === 0" class="text-center py-20 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
        <p class="text-gray-600">No movies found matching your search.</p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-2 text-yellow-600 text-sm font-bold">Clear Search</button>
      </div>

      <div v-for="movie in filteredMovies" :key="movie.id" class="card flex flex-col md:flex-row md:items-center justify-between border-yellow-200">
        <div class="flex-grow">
          <h3 class="text-lg font-bold text-gray-900 mb-1">{{ movie.title }}</h3>
          <p class="text-xs text-gray-600 mb-2 truncate max-w-md">{{ movie.description }}</p>
          <div class="flex items-center text-xs text-gray-600 space-x-4 mb-2">
            <span class="font-medium">🕒 {{ new Date(movie.show_time).toLocaleString() }}</span>
            <span class="font-medium">🪑 {{ movie.available_seats }} / {{ movie.total_seats }} Seats</span>
            <span v-if="getFillRate(movie.id) !== null" class="px-2 py-0.5 rounded-full font-bold" :class="getFillRateColor(getFillRate(movie.id))">
              {{ getFillRate(movie.id) }}% Full
            </span>
          </div>
          <div v-if="movie.image" class="mt-2">
            <img :src="getImageUrl(movie.image)" alt="Movie Image" class="h-20 w-auto rounded border" />
          </div>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
          <button @click="openEdit(movie)" class="text-yellow-700 hover:text-yellow-600 text-sm font-bold bg-yellow-500/10 px-3 py-1.5 rounded transition-colors">
            Edit
          </button>
          <button @click="deleteMovie(movie.id)" class="text-red-500 hover:text-red-400 text-sm font-bold bg-red-500/10 px-3 py-1.5 rounded transition-colors">
            Delete
          </button>
        </div>
      </div>
      
      <div v-if="!loading && movies.length === 0" class="text-center py-20 bg-yellow-50 border border-dashed border-yellow-200 rounded-xl">
        <p class="text-gray-600">No movies found in the catalog.</p>
      </div>
    </div>

    <!-- Bookings Tab Content -->
    <div v-if="activeTab === 'bookings'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">All Bookings ({{ filteredBookings.length }})</h2>
        <button @click="loadAllBookings" class="text-xs text-yellow-600 hover:underline">Refresh</button>
      </div>

      <div v-if="loadingBookings" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-yellow-500 border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredBookings.length === 0" class="text-center py-20 bg-gray-50 border border-dashed border-gray-200 rounded-xl">
        <p class="text-gray-600">No bookings found matching your search.</p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-2 text-yellow-600 text-sm font-bold">Clear Search</button>
      </div>

      <div v-else class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-left bg-white">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">User</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Movie</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Seats</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Numbers</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-gray-900">{{ booking.user?.name || 'Unknown' }}</span>
                  <span class="text-xs text-gray-500">{{ booking.user?.email || 'N/A' }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ booking.movie?.title || 'Deleted Movie' }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">
                  {{ booking.seats_booked }} Seats
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-gray-600">{{ booking.seat_numbers?.join(', ') || 'None' }}</td>
              <td class="px-6 py-4 text-xs text-gray-500">{{ new Date(booking.created_at).toLocaleDateString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue"
import api from "../services/api"

const movies = ref([])
const stats = ref(null)
const allBookings = ref([])
const loading = ref(true)
const loadingBookings = ref(false)
const saving = ref(false)
const showForm = ref(false)
const editingId = ref(null)
const errorMessage = ref("")
const imageFile = ref(null)
const activeTab = ref('catalog')
const searchQuery = ref("")

const filteredMovies = computed(() => {
    if (!searchQuery.value) return movies.value
    const query = searchQuery.value.toLowerCase()
    return movies.value.filter(movie => 
        movie.title.toLowerCase().includes(query) || 
        (movie.description && movie.description.toLowerCase().includes(query))
    )
})

const filteredBookings = computed(() => {
    if (!searchQuery.value) return allBookings.value
    const query = searchQuery.value.toLowerCase()
    return allBookings.value.filter(booking => 
        (booking.user?.name && booking.user.name.toLowerCase().includes(query)) ||
        (booking.user?.email && booking.user.email.toLowerCase().includes(query)) ||
        (booking.movie?.title && booking.movie.title.toLowerCase().includes(query))
    )
})

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

const form = ref({
    title: "",
    description: "",
    show_time: "",
    total_seats: ""
})

const getFillRate = (movieId) => {
    if (!stats.value || !stats.value.movie_stats) return null;
    const movieStat = stats.value.movie_stats.find(s => s.id === movieId);
    return movieStat ? movieStat.fill_rate : null;
}

const getFillRateColor = (rate) => {
    if (rate >= 90) return 'bg-red-100 text-red-700';
    if (rate >= 50) return 'bg-yellow-100 text-yellow-700';
    return 'bg-green-100 text-green-700';
}

const handleFileUpload = (event) => {
    imageFile.value = event.target.files[0]
}

const loadMovies = async () => {
    loading.value = true
    try {
        const res = await api.get("/movies")
        movies.value = res.data
    } catch (err) {
        console.error("Failed to load movies:", err)
    } finally {
        loading.value = false
    }
}

const loadStats = async () => {
    try {
        const res = await api.get("/admin/stats")
        stats.value = res.data
    } catch (err) {
        console.error("Failed to load stats:", err)
    }
}

const loadAllBookings = async () => {
    loadingBookings.value = true
    try {
        const res = await api.get("/admin/bookings")
        allBookings.value = res.data
    } catch (err) {
        console.error("Failed to load bookings:", err)
    } finally {
        loadingBookings.value = false
    }
}

const openCreate = () => {
    editingId.value = null
    form.value = { title: "", description: "", show_time: "", total_seats: "" }
    imageFile.value = null
    errorMessage.value = ""
    showForm.value = true
}

const openEdit = (movie) => {
    editingId.value = movie.id
    const dateStr = movie.show_time ? new Date(movie.show_time).toISOString().slice(0, 16) : ""
    form.value = { ...movie, show_time: dateStr }
    imageFile.value = null
    errorMessage.value = ""
    showForm.value = true
}

const closeForm = () => {
    showForm.value = false
    editingId.value = null
    errorMessage.value = ""
}

const saveMovie = async () => {
    if (!form.value.title || !form.value.description || !form.value.show_time || !form.value.total_seats) {
        errorMessage.value = "All fields are required"
        return
    }

    saving.value = true
    errorMessage.value = ""
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('description', form.value.description);
        formData.append('show_time', form.value.show_time);
        formData.append('total_seats', form.value.total_seats);
        if (imageFile.value) {
            formData.append('image', imageFile.value);
        }

        if (editingId.value) {
            formData.append('_method', 'PUT');
            await api.post(`/movies/${editingId.value}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await api.post("/movies", formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }
        closeForm()
        loadMovies()
        loadStats()
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            const errors = err.response.data.errors;
            const firstKey = Object.keys(errors)[0];
            errorMessage.value = errors[firstKey][0];
        } else {
            errorMessage.value = "Failed to save movie. Check your connection."
        }
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
        loadStats()
    } catch (err) {
        alert("Deletion failed")
    }
}

watch(activeTab, (newTab) => {
    if (newTab === 'bookings') {
        loadAllBookings()
    }
})

onMounted(() => {
    loadMovies()
    loadStats()
})
</script>