<template>
  <div class="container">
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1 font-orbitron">My Movie Tickets</h1>
        <p class="text-slate-300">Digital receipts, scannable QR tickets, and transaction history</p>
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

    <div v-else class="grid gap-6">
      <div 
        v-for="booking in filteredBookings" 
        :key="booking.id" 
        class="card flex flex-col lg:flex-row shadow-2xl border-slate-700/80 bg-black/70 backdrop-blur-2xl overflow-hidden !p-0 transition-all hover:border-[#ef6a26]/60 rounded-3xl"
      >
        <!-- Left Thumbnail Poster -->
        <div v-if="booking.movie?.image" class="w-full lg:w-48 h-48 lg:h-auto bg-gray-900 flex-shrink-0 relative overflow-hidden">
          <img :src="getImageUrl(booking.movie.image)" alt="Movie Poster" class="w-full h-full object-cover" />
          <div class="absolute top-3 left-3 bg-emerald-500 text-black text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
            ✓ {{ booking.payment_status || 'PAID' }}
          </div>
        </div>
        <div v-else class="w-full lg:w-48 h-48 lg:h-auto bg-slate-800 flex items-center justify-center flex-shrink-0 relative">
          <span class="text-gray-400 text-3xl">🎬</span>
          <div class="absolute top-3 left-3 bg-emerald-500 text-black text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
            ✓ {{ booking.payment_status || 'PAID' }}
          </div>
        </div>
        
        <!-- Main Content Section -->
        <div class="flex-grow p-6 flex flex-col justify-between space-y-4">
          <div>
            <div class="flex justify-between items-start mb-2 flex-wrap gap-2">
              <div>
                <h2 class="text-2xl font-bold text-white font-orbitron">{{ booking.movie?.title || 'Movie' }}</h2>
                <div class="text-xs font-mono font-bold text-purple-300 mt-1 flex items-center gap-2">
                  <span>🏛️ {{ booking.showtime?.auditoriumDetail?.cinema?.name ? `${booking.showtime.auditoriumDetail.cinema.name} - ${booking.showtime.auditoriumDetail.name}` : (booking.showtime?.auditorium || 'Main Cinema Hall') }}</span>
                </div>
              </div>
              
              <div class="flex items-center gap-3">
                <!-- Payment Badge -->
                <span class="px-3 py-1 rounded-full font-bold uppercase text-xs font-mono flex items-center gap-1.5" :class="getPaymentBadgeStyle(booking.payment_method)">
                  <span>{{ getPaymentIcon(booking.payment_method) }}</span>
                  <span>{{ getPaymentName(booking.payment_method) }}</span>
                </span>

                <button 
                  @click="cancelBooking(booking.id)" 
                  class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-3 py-1 rounded-xl transition-colors"
                >
                  Cancel Ticket
                </button>
              </div>
            </div>

            <!-- Datetime & Seat info -->
            <div class="flex flex-wrap items-center text-xs text-gray-300 gap-y-2 gap-x-4 mt-3 font-mono">
              <span class="flex items-center bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                <span class="mr-1.5">🕒</span> 
                {{ booking.showtime?.start_time ? new Date(booking.showtime.start_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : (booking.movie?.show_time ? new Date(booking.movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD') }}
              </span>

              <span class="flex items-center bg-slate-800/80 px-3 py-1.5 rounded-xl border border-slate-700">
                <span class="mr-1.5">🪑</span> {{ booking.seats_booked }} Seats
              </span>

              <span class="flex items-center font-bold text-orange-400 bg-orange-500/10 border border-orange-500/20 px-3 py-1.5 rounded-xl" v-if="booking.seat_numbers && booking.seat_numbers.length">
                Seat IDs: {{ booking.seat_numbers.join(', ') }}
              </span>

              <span class="flex items-center font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-xl">
                Total Paid: {{ booking.total_price ? `${Number(booking.total_price).toFixed(0)} Birr` : 'Paid' }}
              </span>
            </div>

            <!-- Itemized Ticket Breakdown -->
            <div v-if="booking.ticket_details && booking.ticket_details.length" class="mt-3 flex gap-2 flex-wrap">
              <span 
                v-for="td in booking.ticket_details" 
                :key="td.seat_id"
                class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-xs font-mono text-slate-300"
              >
                Seat {{ td.seat_id }} ({{ td.type }}: {{ td.price }} Birr)
              </span>
            </div>
          </div>

          <!-- Transaction Footer & Digital E-Ticket Barcode -->
          <div class="text-xs text-slate-400 font-mono border-t border-white/10 pt-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
              <span class="block text-slate-500">Transaction Ref: <strong class="text-white">{{ booking.transaction_ref || `TB-${booking.id}98234` }}</strong></span>
              <span class="block text-[11px] text-slate-500">Issued on: {{ new Date(booking.created_at).toLocaleDateString() }}</span>
            </div>

            <!-- Simulated E-Ticket QR Code / Barcode -->
            <div class="flex items-center gap-3 bg-black/60 p-2 px-3 rounded-xl border border-white/10">
              <div class="flex flex-col items-center">
                <!-- Barcode visual simulation -->
                <div class="flex items-center gap-0.5 h-6">
                  <div class="w-1 h-full bg-white"></div>
                  <div class="w-0.5 h-full bg-white"></div>
                  <div class="w-1.5 h-full bg-white"></div>
                  <div class="w-0.5 h-full bg-white"></div>
                  <div class="w-1 h-full bg-white"></div>
                  <div class="w-2 h-full bg-white"></div>
                  <div class="w-0.5 h-full bg-white"></div>
                  <div class="w-1 h-full bg-white"></div>
                  <div class="w-1.5 h-full bg-white"></div>
                  <div class="w-0.5 h-full bg-white"></div>
                </div>
                <span class="text-[9px] text-slate-400 tracking-widest mt-0.5 font-mono">SCAN AT ENTRY</span>
              </div>
            </div>
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
        (booking.showtime?.auditorium && booking.showtime.auditorium.toLowerCase().includes(query)) ||
        (booking.transaction_ref && booking.transaction_ref.toLowerCase().includes(query))
    )
})

const getPaymentName = (method) => {
  switch(method) {
    case 'telebirr': return 'Telebirr';
    case 'cbe_birr': return 'CBE Birr';
    case 'chapa': return 'Chapa';
    case 'boa': return 'Abyssinia Pay';
    default: return 'Telebirr';
  }
}

const getPaymentIcon = (method) => {
  switch(method) {
    case 'telebirr': return '📱';
    case 'cbe_birr': return '🏦';
    case 'chapa': return '💳';
    case 'boa': return '🏛️';
    default: return '📱';
  }
}

const getPaymentBadgeStyle = (method) => {
  switch(method) {
    case 'telebirr': return 'bg-cyan-500/15 border-cyan-500/30 text-cyan-300';
    case 'cbe_birr': return 'bg-purple-500/15 border-purple-500/30 text-purple-300';
    case 'chapa': return 'bg-green-500/15 border-green-500/30 text-green-300';
    case 'boa': return 'bg-amber-500/15 border-amber-500/30 text-amber-300';
    default: return 'bg-cyan-500/15 border-cyan-500/30 text-cyan-300';
  }
}

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
    if (!confirm("Are you sure you want to cancel this ticket booking?")) return
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