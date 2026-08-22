<template>
  <div class="container">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1 font-orbitron">My Bookings</h1>
        <p class="text-slate-300">Your reservations, ticket receipts (Birr), and auditorium history</p>
      </div>
      <div class="relative w-full md:w-80" v-if="bookings.length > 0 || searchQuery">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
          🔍
        </span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search by movie title..." 
          class="pl-10 pr-4 py-2.5 w-full border border-[#ef6a26]/40 rounded-xl focus:ring-2 focus:ring-[#ef6a26] outline-none shadow-sm transition-all bg-slate-900/90 text-white placeholder-slate-400 text-sm"
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

    <div v-else class="grid gap-4">
      <div 
        v-for="booking in filteredBookings" 
        :key="booking.id" 
        class="card flex flex-col sm:flex-row shadow-lg border-slate-700/60 bg-black/60 backdrop-blur-xl overflow-hidden !p-0 transition-all hover:border-[#ef6a26]/50"
      >
        <!-- Thumbnail Image -->
        <div v-if="booking.movie?.image" class="w-full sm:w-36 h-36 sm:h-auto bg-gray-900 flex-shrink-0">
          <img :src="getImageUrl(booking.movie.image)" alt="Movie Poster" class="w-full h-full object-cover" />
        </div>
        <div v-else class="w-full sm:w-36 h-36 sm:h-auto bg-slate-800 flex items-center justify-center flex-shrink-0">
          <span class="text-gray-400 text-2xl">🎬</span>
        </div>
        
        <!-- Content -->
        <div class="flex-grow p-5 flex flex-col justify-between">
          <div>
            <div class="flex justify-between items-start mb-2 flex-wrap gap-2">
              <div>
                <h2 class="text-xl font-bold text-white font-orbitron">{{ booking.movie?.title || 'Movie' }}</h2>
                <div v-if="booking.showtime?.auditorium" class="text-xs font-mono font-bold text-purple-300 mt-0.5">
                  🏛️ Auditorium: {{ booking.showtime.auditorium }}
                </div>
              </div>
              
              <div class="flex items-center gap-3">
                <span class="text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded font-bold uppercase text-xs font-mono">
                  {{ booking.total_price ? `${Number(booking.total_price).toFixed(0)} Birr` : 'Confirmed' }}
                </span>
                <button 
                  @click="cancelBooking(booking.id)" 
                  class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-2.5 py-1 rounded transition-colors"
                >
                  Cancel Ticket
                </button>
              </div>
            </div>

            <div class="flex flex-wrap items-center text-xs text-gray-300 gap-y-2 gap-x-4 mt-3 font-mono">
              <span class="flex items-center bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700">
                <span class="mr-1.5">🕒</span> 
                {{ booking.showtime?.start_time ? new Date(booking.showtime.start_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : (booking.movie?.show_time ? new Date(booking.movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD') }}
              </span>

              <span class="flex items-center bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700">
                <span class="mr-1.5">🪑</span> {{ booking.seats_booked }} Seats
              </span>

              <span class="flex items-center font-bold text-orange-400 bg-orange-500/10 border border-orange-500/20 px-2.5 py-1 rounded-md" v-if="booking.seat_numbers && booking.seat_numbers.length">
                Seat IDs: {{ booking.seat_numbers.join(', ') }}
              </span>
            </div>

            <!-- Itemized Ticket Details if available -->
            <div v-if="booking.ticket_details && booking.ticket_details.length" class="mt-3 flex gap-2 flex-wrap">
              <span 
                v-for="td in booking.ticket_details" 
                :key="td.seat_id"
                class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-[11px] font-mono text-slate-300"
              >
                {{ td.seat_id }} ({{ td.type }}: {{ td.price }} Birr)
              </span>
            </div>
          </div>

          <div class="text-[11px] text-slate-500 font-mono mt-3 border-t border-white/5 pt-2 flex justify-between">
            <span>Booking Ref #: {{ booking.id }}</span>
            <span>Booked on: {{ new Date(booking.created_at).toLocaleDateString() }}</span>
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
        (booking.movie?.title && booking.movie.title.toLowerCase().includes(query)) ||
        (booking.showtime?.auditorium && booking.showtime.auditorium.toLowerCase().includes(query))
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