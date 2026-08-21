<template>
  <div class="container">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-1">My Bookings</h1>
        <p class="text-gray-600">Your reservations and ticket history</p>
      </div>
      <div class="relative w-full md:w-80" v-if="bookings.length > 0 || searchQuery">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
          🔍
        </span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search by movie title..." 
          class="pl-10 pr-4 py-2 w-full border border-[#ef6a26]/30 rounded-xl focus:ring-2 focus:ring-[#ef6a26] outline-none shadow-sm transition-all bg-slate-900/40 text-slate-200"
        />
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-[#ef6a26] border-t-transparent rounded-full"></div>
    </div>

    <div v-else-if="bookings.length === 0" class="card text-center py-12 border-[#ef6a26]/20">
      <p class="text-gray-300 mb-6 font-medium">No bookings found yet.</p>
      <router-link to="/movies" class="btn-primary">Explore Movies</router-link>
    </div>

    <div v-else-if="filteredBookings.length === 0" class="card text-center py-12 border-[#ef6a26]/20 bg-slate-900/30 border-dashed">
      <p class="text-gray-300">No bookings found for "{{ searchQuery }}"</p>
      <button @click="searchQuery = ''" class="mt-4 text-[#ef6a26] font-bold hover:underline">Clear Search</button>
    </div>

    <div v-else class="grid gap-3">
      <div v-for="booking in filteredBookings" :key="booking.id" class="card flex flex-col sm:flex-row shadow-sm border-[#ef6a26]/20 overflow-hidden !p-0 transition-all hover:shadow-md hover:border-[#ef6a26]/50">
        <!-- Thumbnail Image -->
        <div v-if="booking.movie.image" class="w-full sm:w-32 h-32 sm:h-auto bg-gray-900 flex-shrink-0">
          <img :src="getImageUrl(booking.movie.image)" alt="Movie Poster" class="w-full h-full object-cover" />
        </div>
        <div v-else class="w-full sm:w-32 h-32 sm:h-auto bg-slate-800 flex items-center justify-center flex-shrink-0">
          <span class="text-gray-400 text-xs font-medium">No Image</span>
        </div>
        
        <!-- Content -->
        <div class="flex-grow p-4 sm:p-5 flex flex-col justify-center">
          <div class="flex justify-between items-start mb-1">
            <h2 class="text-lg font-bold text-slate-100">{{ booking.movie.title }}</h2>
            <div class="flex items-center gap-2">
              <span class="text-green-400 bg-green-500/10 border border-green-500/20 px-2 py-0.5 rounded font-bold uppercase text-[10px] tracking-widest hidden sm:inline-block">Confirmed</span>
              <button 
                @click="cancelBooking(booking.id)" 
                class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-2 py-1 rounded transition-colors"
              >
                Cancel
              </button>
            </div>
          </div>
          <div class="flex flex-wrap items-center text-xs sm:text-sm text-gray-300 gap-y-1.5 gap-x-4 mt-0.5">
            <span class="flex items-center whitespace-nowrap"><span class="mr-1.5 opacity-70">🕒</span> {{ booking.movie.show_time ? new Date(booking.movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD' }}</span>
            <span class="flex items-center whitespace-nowrap"><span class="mr-1.5 opacity-70">🪑</span> {{ booking.seats_booked }} Seats</span>
            <span class="flex items-center whitespace-nowrap font-medium text-orange-400 bg-orange-500/10 border border-orange-500/20 px-2 py-0.5 rounded-md" v-if="booking.seat_numbers && booking.seat_numbers.length">
              Seats: {{ booking.seat_numbers.join(', ') }}
            </span>
            <!-- Mobile Badge -->
            <span class="text-green-400 font-bold uppercase text-[10px] tracking-widest sm:hidden w-full mt-1">Confirmed</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue"
import api from "../services/api"

const bookings = ref([])
const loading = ref(true)
const searchQuery = ref("")

const filteredBookings = computed(() => {
    if (!searchQuery.value) return bookings.value
    const query = searchQuery.value.toLowerCase()
    return bookings.value.filter(booking => 
        booking.movie.title.toLowerCase().includes(query)
    )
})

const loadBookings = async () => {
    loading.value = true
    try {
        const res = await api.get("/my-bookings")
        bookings.value = res.data
    } catch (err) {
        console.error(err)
    } finally {
        loading.value = false
    }
}

const cancelBooking = async (id) => {
    if (!confirm("Are you sure you want to cancel this booking?")) return
    try {
        await api.delete(`/bookings/${id}`)
        loadBookings()
    } catch (err) {
        alert("Cancellation failed")
    }
}

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

onMounted(loadBookings)
</script>