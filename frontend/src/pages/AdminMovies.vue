<template>
  <div class="container space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1 font-orbitron">ADMIN DASHBOARD</h1>
        <p class="text-slate-300">Manage the movie catalog and ticketing</p>
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
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Movies</span>
        <span class="text-3xl font-bold text-white font-orbitron">{{ stats.summary.total_movies }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Tickets Sold</span>
        <span class="text-3xl font-bold text-[#ef6a26] font-orbitron">{{ stats.summary.booked_seats }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Remaining Seats</span>
        <span class="text-3xl font-bold text-green-400 font-orbitron">{{ stats.summary.available_seats }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Fill Rate</span>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-blue-400 font-orbitron">{{ stats.summary.overall_fill_rate }}%</span>
          <div class="flex-grow h-2 bg-slate-800 rounded-full mb-2 overflow-hidden">
            <div class="h-full bg-blue-500" :style="{ width: stats.summary.overall_fill_rate + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-white/10 gap-4">
      <div class="flex space-x-2">
        <button 
          @click="activeTab = 'catalog'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2"
          :class="activeTab === 'catalog' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Movie Catalog
        </button>
        <button 
          @click="activeTab = 'bookings'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2"
          :class="activeTab === 'bookings' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Recent Bookings
        </button>
      </div>
      <div class="relative w-full md:w-64 mb-2 md:mb-0">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">🔍</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          :placeholder="activeTab === 'catalog' ? 'Search movies...' : 'Search users or movies...'" 
          class="pl-10 pr-4 py-2 w-full border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-[#ef6a26] outline-none text-sm bg-slate-900/90 text-white placeholder-slate-400"
        />
      </div>
    </div>

    <!-- Movie Form (Create/Edit) -->
    <div v-if="showForm" class="card space-y-6 border-[#ef6a26]/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
      <div class="flex justify-between items-center border-b border-white/10 pb-4">
        <h2 class="text-xl font-bold text-white font-orbitron">{{ editingId ? 'Edit Movie' : 'Add New Movie' }}</h2>
        <button @click="closeForm" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">Cancel</button>
      </div>

      <!-- Error Message Box -->
      <div v-if="errorMessage" class="p-3.5 bg-red-950/60 border border-red-800/60 text-red-300 rounded-xl text-xs text-center font-medium">
        {{ errorMessage }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Movie Title</label>
          <input v-model="form.title" placeholder="Title" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Show Time</label>
          <input v-model="form.show_time" type="datetime-local" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30" />
        </div>
        <div class="md:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Description</label>
          <input v-model="form.description" placeholder="Movie description..." class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="md:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Movie Image</label>
          <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-slate-200 px-4 py-2.5 border border-white/10 rounded-xl bg-black/40 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#ef6a26] file:text-white hover:file:bg-orange-600" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Total Capacity (Seats)</label>
          <input v-model="form.total_seats" @input="form.total_seats = Math.abs($event.target.value) || ''" type="number" min="1" placeholder="100" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="flex items-end">
          <button @click="saveMovie" :disabled="saving" class="btn-primary w-full py-3">
            {{ saving ? 'SAVING...' : 'SAVE MOVIE' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Catalog Tab Content -->
    <div v-if="activeTab === 'catalog'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">Catalog ({{ filteredMovies.length }})</h2>
        <button @click="loadMovies" class="text-xs font-bold text-[#ef6a26] hover:underline">Refresh</button>
      </div>
      
      <div v-if="loading" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-[#ef6a26] border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredMovies.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No movies found matching your search.</p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-2 text-[#ef6a26] text-sm font-bold hover:underline">Clear Search</button>
      </div>

      <div v-for="movie in filteredMovies" :key="movie.id" class="card flex flex-col md:flex-row md:items-center justify-between border-[#ef6a26]/20 bg-black/50 backdrop-blur-xl hover:border-[#ef6a26]/50 transition-all">
        <div class="flex-grow">
          <h3 class="text-lg font-bold text-white mb-1">{{ movie.title }}</h3>
          <p class="text-xs text-slate-300 mb-2 truncate max-w-md">{{ movie.description }}</p>
          <div class="flex flex-wrap items-center text-xs text-slate-300 gap-4 mb-2">
            <span class="font-medium">🕒 {{ new Date(movie.show_time).toLocaleString() }}</span>
            <span class="font-medium">🪑 {{ movie.available_seats }} / {{ movie.total_seats }} Seats</span>
            <span v-if="getFillRate(movie.id) !== null" class="px-2.5 py-0.5 rounded-full font-bold text-[11px]" :class="getFillRateColor(getFillRate(movie.id))">
              {{ getFillRate(movie.id) }}% Full
            </span>
          </div>
          <div v-if="movie.image" class="mt-2">
            <img :src="getImageUrl(movie.image)" alt="Movie Image" class="h-20 w-auto rounded-lg border border-white/10 object-cover" />
          </div>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
          <button @click="openEdit(movie)" class="text-orange-400 hover:text-orange-300 text-xs font-bold bg-orange-500/10 border border-orange-500/20 px-3.5 py-2 rounded-lg transition-colors">
            Edit
          </button>
          <button @click="deleteMovie(movie.id)" class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-3.5 py-2 rounded-lg transition-colors">
            Delete
          </button>
        </div>
      </div>
      
      <div v-if="!loading && movies.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No movies found in the catalog.</p>
      </div>
    </div>

    <!-- Bookings Tab Content -->
    <div v-if="activeTab === 'bookings'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">All Bookings ({{ filteredBookings.length }})</h2>
        <button @click="loadAllBookings" class="text-xs font-bold text-[#ef6a26] hover:underline">Refresh</button>
      </div>

      <div v-if="loadingBookings" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-[#ef6a26] border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredBookings.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No bookings found matching your search.</p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-2 text-[#ef6a26] text-sm font-bold hover:underline">Clear Search</button>
      </div>

      <div v-else class="overflow-x-auto rounded-2xl border border-white/10 shadow-2xl backdrop-blur-xl">
        <table class="w-full text-left bg-black/60">
          <thead class="bg-black/80 border-b border-white/10">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Movie</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Seats</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Numbers</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-white/5 transition-colors">
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-white">{{ booking.user?.name || 'Unknown' }}</span>
                  <span class="text-xs text-slate-400">{{ booking.user?.email || 'N/A' }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-200 font-medium">{{ booking.movie?.title || 'Deleted Movie' }}</td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold rounded-md">
                  {{ booking.seats_booked }} Seats
                </span>
              </td>
              <td class="px-6 py-4 text-xs text-slate-300 font-mono">{{ booking.seat_numbers?.join(', ') || 'None' }}</td>
              <td class="px-6 py-4 text-xs text-slate-400 font-mono">{{ new Date(booking.created_at).toLocaleDateString() }}</td>
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
    if (rate >= 90) return 'bg-red-500/20 text-red-400 border border-red-500/30';
    if (rate >= 50) return 'bg-orange-500/20 text-orange-400 border border-orange-500/30';
    return 'bg-green-500/20 text-green-400 border border-green-500/30';
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