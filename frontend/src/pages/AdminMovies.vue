<template>
  <div class="container space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1 font-orbitron">ADMIN DASHBOARD</h1>
        <p class="text-slate-300">Manage movies, showtimes, auditoriums, and ticket reservations</p>
      </div>
      <div class="flex gap-3">
        <button 
          v-if="!showForm && activeTab === 'catalog'" 
          @click="openCreate" 
          class="btn-primary"
        >
          + Add New Movie
        </button>
        <button 
          v-if="!showShowtimeForm && activeTab === 'showtimes'" 
          @click="openCreateShowtime" 
          class="btn-primary"
        >
          + Add New Showtime
        </button>
      </div>
    </div>

    <!-- Statistics Dashboard -->
    <div v-if="stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Movies</span>
        <span class="text-3xl font-bold text-white font-orbitron">{{ stats.summary.total_movies }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Showtimes</span>
        <span class="text-3xl font-bold text-purple-400 font-orbitron">{{ stats.summary.total_showtimes ?? 0 }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Tickets Sold</span>
        <span class="text-3xl font-bold text-[#ef6a26] font-orbitron">{{ stats.summary.booked_seats }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Overall Fill Rate</span>
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
      <div class="flex space-x-2 overflow-x-auto">
        <button 
          @click="activeTab = 'catalog'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
          :class="activeTab === 'catalog' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Movie Catalog
        </button>
        <button 
          @click="activeTab = 'showtimes'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
          :class="activeTab === 'showtimes' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Showtimes Management
        </button>
        <button 
          @click="activeTab = 'bookings'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
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
          :placeholder="activeTab === 'catalog' ? 'Search movies...' : activeTab === 'showtimes' ? 'Search showtimes...' : 'Search users or movies...'" 
          class="pl-10 pr-4 py-2 w-full border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-[#ef6a26] outline-none text-sm bg-slate-900/90 text-white placeholder-slate-400"
        />
      </div>
    </div>

    <!-- Movie Form (Create/Edit) -->
    <div v-if="showForm && activeTab === 'catalog'" class="card space-y-6 border-[#ef6a26]/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
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
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Default Release / Show Date</label>
          <input v-model="form.show_time" type="datetime-local" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30" />
        </div>
        <div class="md:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Description</label>
          <input v-model="form.description" placeholder="Movie description..." class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="md:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Movie Poster Image</label>
          <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-slate-200 px-4 py-2.5 border border-white/10 rounded-xl bg-black/40 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#ef6a26] file:text-white hover:file:bg-orange-600" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Default Capacity (Seats)</label>
          <input v-model="form.total_seats" @input="form.total_seats = Math.abs($event.target.value) || ''" type="number" min="1" placeholder="50" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="flex items-end">
          <button @click="saveMovie" :disabled="saving" class="btn-primary w-full py-3">
            {{ saving ? 'SAVING...' : 'SAVE MOVIE' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Showtime Form (Create/Edit) -->
    <div v-if="showShowtimeForm && activeTab === 'showtimes'" class="card space-y-6 border-purple-500/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
      <div class="flex justify-between items-center border-b border-white/10 pb-4">
        <h2 class="text-xl font-bold text-white font-orbitron">{{ editingShowtimeId ? 'Edit Showtime' : 'Schedule New Showtime' }}</h2>
        <button @click="closeShowtimeForm" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">Cancel</button>
      </div>

      <!-- Error Message Box -->
      <div v-if="showtimeError" class="p-4 bg-red-950/80 border border-red-500/60 text-red-200 rounded-xl text-xs text-center font-bold tracking-wide">
        ⚠️ {{ showtimeError }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Select Movie</label>
          <select v-model="showtimeForm.movie_id" class="w-full text-white bg-slate-900 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30">
            <option value="" disabled>Choose a movie...</option>
            <option v-for="m in movies" :key="m.id" :value="m.id">{{ m.title }}</option>
          </select>
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Cinema / Auditorium</label>
          <div class="flex gap-2">
            <select v-model="showtimeForm.auditorium" class="w-full text-white bg-slate-900 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30">
              <option value="Auditorium 1">Auditorium 1</option>
              <option value="Auditorium 2">Auditorium 2</option>
              <option value="Auditorium 3 (IMAX)">Auditorium 3 (IMAX)</option>
              <option value="VIP Screen 1">VIP Screen 1</option>
              <option value="Screen 4">Screen 4</option>
            </select>
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Start Time</label>
          <input v-model="showtimeForm.start_time" type="datetime-local" @change="autoSetEndTime" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">End Time</label>
          <input v-model="showtimeForm.end_time" type="datetime-local" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Ticket Price ($)</label>
          <input v-model="showtimeForm.price" type="number" step="0.50" min="0" placeholder="12.50" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30 placeholder-slate-400" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Auditorium Seat Capacity</label>
          <input v-model="showtimeForm.total_seats" type="number" min="1" placeholder="50" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30 placeholder-slate-400" />
        </div>

        <div class="md:col-span-2 flex justify-end gap-3 pt-2">
          <button @click="closeShowtimeForm" class="px-6 py-2.5 rounded-xl border border-slate-700 text-slate-300 text-sm font-bold hover:bg-slate-800 transition-colors">
            Cancel
          </button>
          <button @click="saveShowtime" :disabled="savingShowtime" class="btn-primary py-2.5 px-8">
            {{ savingShowtime ? 'SAVING...' : (editingShowtimeId ? 'UPDATE SHOWTIME' : 'CREATE SHOWTIME') }}
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
            <span class="font-medium">🕒 Release/Default: {{ movie.show_time ? new Date(movie.show_time).toLocaleString() : 'N/A' }}</span>
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

    <!-- Showtimes Management Tab Content -->
    <div v-if="activeTab === 'showtimes'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">Scheduled Showtimes ({{ filteredShowtimes.length }})</h2>
        <button @click="loadShowtimes" class="text-xs font-bold text-purple-400 hover:underline">Refresh</button>
      </div>

      <div v-if="loadingShowtimes" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-purple-500 border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredShowtimes.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No scheduled showtimes found.</p>
        <button @click="openCreateShowtime" class="mt-3 text-purple-400 text-sm font-bold hover:underline">+ Schedule a Showtime</button>
      </div>

      <div v-else class="grid grid-cols-1 gap-4">
        <div 
          v-for="st in filteredShowtimes" 
          :key="st.id" 
          class="card flex flex-col md:flex-row md:items-center justify-between border-purple-500/30 bg-black/50 backdrop-blur-xl hover:border-purple-500/70 transition-all gap-4"
        >
          <div class="flex items-start md:items-center gap-4">
            <!-- Movie Poster Thumbnail -->
            <div v-if="st.movie?.image" class="w-16 h-20 bg-slate-800 rounded-lg overflow-hidden flex-shrink-0 border border-white/10">
              <img :src="getImageUrl(st.movie.image)" alt="Poster" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-16 h-20 bg-purple-950/40 rounded-lg flex items-center justify-center text-xl flex-shrink-0 border border-purple-500/20">
              🎬
            </div>

            <div class="space-y-1">
              <div class="flex items-center gap-3 flex-wrap">
                <h3 class="text-lg font-bold text-white">{{ st.movie?.title || 'Unknown Movie' }}</h3>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold font-mono bg-purple-500/20 border border-purple-500/40 text-purple-300">
                  🏛️ {{ st.auditorium }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-emerald-500/20 border border-emerald-500/40 text-emerald-300">
                  ${{ Number(st.price).toFixed(2) }} / ticket
                </span>
              </div>

              <div class="flex flex-wrap items-center text-xs text-slate-300 gap-y-1 gap-x-4 pt-1 font-mono">
                <span>🕒 {{ new Date(st.start_time).toLocaleString(undefined, {dateStyle: 'short', timeStyle: 'short'}) }} → {{ new Date(st.end_time).toLocaleTimeString(undefined, {timeStyle: 'short'}) }}</span>
                <span>🪑 {{ st.available_seats }} / {{ st.total_seats }} Available Seats</span>
                <span 
                  class="px-2 py-0.5 rounded text-[10px] font-bold"
                  :class="getFillRateColor(getShowtimeFillRate(st))"
                >
                  {{ getShowtimeFillRate(st) }}% Booked
                </span>
              </div>
            </div>
          </div>

          <div class="flex space-x-2 self-end md:self-center">
            <button @click="openEditShowtime(st)" class="text-purple-300 hover:text-purple-100 text-xs font-bold bg-purple-500/20 border border-purple-500/40 px-3.5 py-2 rounded-lg transition-colors">
              Edit
            </button>
            <button @click="deleteShowtime(st.id)" class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-3.5 py-2 rounded-lg transition-colors">
              Cancel Showtime
            </button>
          </div>
        </div>
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
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Movie / Showtime</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Auditorium</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Seats</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Total Price</th>
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
              <td class="px-6 py-4 text-sm text-slate-200 font-medium">
                <div>{{ booking.movie?.title || 'Deleted Movie' }}</div>
                <div v-if="booking.showtime" class="text-xs text-purple-300 font-mono">
                  {{ new Date(booking.showtime.start_time).toLocaleString(undefined, {dateStyle: 'short', timeStyle: 'short'}) }}
                </div>
              </td>
              <td class="px-6 py-4 text-xs text-slate-300 font-mono">
                {{ booking.showtime?.auditorium || 'Main Screen' }}
              </td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold rounded-md block w-max">
                  {{ booking.seats_booked }} Seats ({{ booking.seat_numbers?.join(', ') || 'N/A' }})
                </span>
              </td>
              <td class="px-6 py-4 text-xs font-bold text-emerald-400 font-mono">
                ${{ booking.total_price ? Number(booking.total_price).toFixed(2) : 'N/A' }}
              </td>
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
const showtimes = ref([])
const stats = ref(null)
const allBookings = ref([])
const loading = ref(true)
const loadingShowtimes = ref(false)
const loadingBookings = ref(false)
const saving = ref(false)
const savingShowtime = ref(false)
const showForm = ref(false)
const showShowtimeForm = ref(false)
const editingId = ref(null)
const editingShowtimeId = ref(null)
const errorMessage = ref("")
const showtimeError = ref("")
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

const filteredShowtimes = computed(() => {
    if (!searchQuery.value) return showtimes.value
    const query = searchQuery.value.toLowerCase()
    return showtimes.value.filter(st => 
        (st.movie?.title && st.movie.title.toLowerCase().includes(query)) ||
        (st.auditorium && st.auditorium.toLowerCase().includes(query))
    )
})

const filteredBookings = computed(() => {
    if (!searchQuery.value) return allBookings.value
    const query = searchQuery.value.toLowerCase()
    return allBookings.value.filter(booking => 
        (booking.user?.name && booking.user.name.toLowerCase().includes(query)) ||
        (booking.user?.email && booking.user.email.toLowerCase().includes(query)) ||
        (booking.movie?.title && booking.movie.title.toLowerCase().includes(query)) ||
        (booking.showtime?.auditorium && booking.showtime.auditorium.toLowerCase().includes(query))
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
    total_seats: "50"
})

const showtimeForm = ref({
    movie_id: "",
    auditorium: "Auditorium 1",
    start_time: "",
    end_time: "",
    price: "12.50",
    total_seats: "50"
})

const autoSetEndTime = () => {
    if (showtimeForm.value.start_time) {
        const start = new Date(showtimeForm.value.start_time)
        const end = new Date(start.getTime() + 2 * 60 * 60 * 1000) // Default +2 hours
        showtimeForm.value.end_time = end.toISOString().slice(0, 16)
    }
}

const getFillRate = (movieId) => {
    if (!stats.value || !stats.value.movie_stats) return null;
    const movieStat = stats.value.movie_stats.find(s => s.id === movieId);
    return movieStat ? movieStat.fill_rate : null;
}

const getShowtimeFillRate = (st) => {
    if (!st.total_seats) return 0;
    const booked = st.total_seats - st.available_seats;
    return Math.round((booked / st.total_seats) * 100);
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

const loadShowtimes = async () => {
    loadingShowtimes.value = true
    try {
        const res = await api.get("/showtimes")
        showtimes.value = res.data
    } catch (err) {
        console.error("Failed to load showtimes:", err)
    } finally {
        loadingShowtimes.value = false
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
    form.value = { title: "", description: "", show_time: "", total_seats: "50" }
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

const openCreateShowtime = () => {
    editingShowtimeId.value = null
    showtimeForm.value = {
        movie_id: movies.value.length ? movies.value[0].id : "",
        auditorium: "Auditorium 1",
        start_time: "",
        end_time: "",
        price: "12.50",
        total_seats: "50"
    }
    showtimeError.value = ""
    showShowtimeForm.value = true
}

const openEditShowtime = (st) => {
    editingShowtimeId.value = st.id
    showtimeForm.value = {
        movie_id: st.movie_id,
        auditorium: st.auditorium,
        start_time: st.start_time ? new Date(st.start_time).toISOString().slice(0, 16) : "",
        end_time: st.end_time ? new Date(st.end_time).toISOString().slice(0, 16) : "",
        price: st.price,
        total_seats: st.total_seats
    }
    showtimeError.value = ""
    showShowtimeForm.value = true
}

const closeShowtimeForm = () => {
    showShowtimeForm.value = false
    editingShowtimeId.value = null
    showtimeError.value = ""
}

const saveMovie = async () => {
    if (!form.value.title || !form.value.description) {
        errorMessage.value = "Title and description are required"
        return
    }

    saving.value = true
    errorMessage.value = ""
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('description', form.value.description);
        if (form.value.show_time) formData.append('show_time', form.value.show_time);
        if (form.value.total_seats) formData.append('total_seats', form.value.total_seats);
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
            errorMessage.value = err.response?.data?.message || "Failed to save movie."
        }
        console.error(err)
    } finally {
        saving.value = false
    }
}

const saveShowtime = async () => {
    if (!showtimeForm.value.movie_id || !showtimeForm.value.auditorium || !showtimeForm.value.start_time || !showtimeForm.value.end_time || !showtimeForm.value.price || !showtimeForm.value.total_seats) {
        showtimeError.value = "All showtime fields are required."
        return
    }

    savingShowtime.value = true
    showtimeError.value = ""
    try {
        if (editingShowtimeId.value) {
            await api.put(`/showtimes/${editingShowtimeId.value}`, showtimeForm.value)
        } else {
            await api.post("/showtimes", showtimeForm.value)
        }
        closeShowtimeForm()
        loadShowtimes()
        loadStats()
    } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
            showtimeError.value = err.response.data.message
        } else if (err.response && err.response.data && err.response.data.errors) {
            const errors = err.response.data.errors;
            const firstKey = Object.keys(errors)[0];
            showtimeError.value = errors[firstKey][0];
        } else {
            showtimeError.value = "Failed to save showtime. Please check inputs."
        }
    } finally {
        savingShowtime.value = false
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

const deleteShowtime = async (id) => {
    if(!confirm("Cancel and delete this showtime schedule?")) return
    try {
        await api.delete(`/showtimes/${id}`)
        loadShowtimes()
        loadStats()
    } catch (err) {
        alert("Deletion failed")
    }
}

watch(activeTab, (newTab) => {
    if (newTab === 'showtimes') {
        loadShowtimes()
    } else if (newTab === 'bookings') {
        loadAllBookings()
    }
})

onMounted(() => {
    loadMovies()
    loadShowtimes()
    loadStats()
})
</script>