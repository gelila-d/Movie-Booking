<template>
  <div class="container space-y-8 font-sans">
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1 font-cinematic tracking-wide">MY MOVIE TICKETS</h1>
        <p class="text-slate-400 text-sm font-sans">View upcoming showtimes, past movie history, refunds, and scannable QR passes</p>
      </div>
      <div class="relative w-full md:w-80" v-if="bookings.length > 0 || searchQuery">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
          🔍
        </span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search by movie title or ref..." 
          class="pl-10 pr-4 py-2.5 w-full border border-white/15 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none shadow-sm transition-all bg-black/80 text-white placeholder-slate-400 text-sm font-sans"
        />
      </div>
    </div>

    <!-- Explicit Cancellation & Refund Policy Banner -->
    <div class="p-4 bg-black/60 backdrop-blur-2xl border border-white/15 rounded-2xl flex items-start gap-3 shadow-md">
      <span class="text-xl">ℹ️</span>
      <div class="space-y-0.5 text-xs text-slate-300 font-sans">
        <h4 class="font-bold text-orange-400 uppercase tracking-wider">Cancellation & Refund Policy</h4>
        <p>100% Mobile Money refund (Telebirr / CBE Birr) is granted for ticket cancellations requested at least <strong>2 hours prior to showtime</strong>. Seats are immediately restored to the cinema hall.</p>
      </div>
    </div>

    <!-- Upcoming vs Past vs Cancelled Tab Filters -->
    <div class="flex items-center justify-between border-b border-white/10 pb-4 gap-4 flex-wrap">
      <div class="flex space-x-3 overflow-x-auto">
        <button 
          @click="activeTab = 'upcoming'" 
          class="px-5 py-2.5 rounded-xl font-bold text-xs font-sans uppercase tracking-wider transition-all flex items-center gap-2"
          :class="activeTab === 'upcoming' ? 'bg-orange-500 text-white shadow-md shadow-orange-950/30' : 'bg-black/60 border border-white/15 text-slate-300 hover:border-orange-500/50 hover:bg-black/80'"
        >
          <span>🎟️ Upcoming Bookings</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-black/40 text-white font-bold">{{ upcomingBookings.length }}</span>
        </button>

        <button 
          @click="activeTab = 'past'" 
          class="px-5 py-2.5 rounded-xl font-bold text-xs font-sans uppercase tracking-wider transition-all flex items-center gap-2"
          :class="activeTab === 'past' ? 'bg-orange-500 text-white shadow-md shadow-orange-950/30' : 'bg-black/60 border border-white/15 text-slate-300 hover:border-orange-500/50 hover:bg-black/80'"
        >
          <span>⏳ Past Bookings</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-black/40 text-slate-300 font-bold">{{ pastBookings.length }}</span>
        </button>

        <button 
          @click="activeTab = 'cancelled'" 
          class="px-5 py-2.5 rounded-xl font-bold text-xs font-sans uppercase tracking-wider transition-all flex items-center gap-2"
          :class="activeTab === 'cancelled' ? 'bg-red-950 border border-red-500/50 text-red-100 shadow-md shadow-red-900/30' : 'bg-black/60 border border-white/15 text-slate-300 hover:border-orange-500/50 hover:bg-black/80'"
        >
          <span>🚫 Cancelled & Refunded</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-black/40 text-red-300 font-bold">{{ cancelledBookings.length }}</span>
        </button>

        <button 
          @click="activeTab = 'all'" 
          class="px-5 py-2.5 rounded-xl font-bold text-xs font-sans uppercase tracking-wider transition-all flex items-center gap-2"
          :class="activeTab === 'all' ? 'bg-orange-500 text-white shadow-md shadow-orange-950/30' : 'bg-black/60 border border-white/15 text-slate-300 hover:border-orange-500/50 hover:bg-black/80'"
        >
          <span>📋 All Reservations</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-black/40 text-white font-bold">{{ bookings.length }}</span>
        </button>
      </div>

      <router-link to="/movies" class="text-xs font-bold text-orange-400 hover:text-orange-300 transition-colors flex items-center gap-1 font-sans">
        <span>+ Book Another Movie</span>
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="animate-spin h-8 w-8 border-2 border-orange-500 border-t-transparent rounded-full"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="displayedBookings.length === 0" class="card text-center py-16 border-white/15 bg-black/60 backdrop-blur-2xl rounded-2xl">
      <div class="w-16 h-16 mx-auto mb-4 bg-black/60 border border-white/15 rounded-full flex items-center justify-center text-3xl">
        🎟️
      </div>
      <h3 class="text-lg font-bold text-white mb-1 font-sans">
        {{ activeTab === 'upcoming' ? 'No Upcoming Bookings' : activeTab === 'past' ? 'No Past Movie History' : activeTab === 'cancelled' ? 'No Cancelled Tickets' : 'No Bookings Found' }}
      </h3>
      <p class="text-slate-400 text-sm mb-6 max-w-md mx-auto font-sans">
        {{ activeTab === 'upcoming' ? 'You have no scheduled upcoming movie showtimes.' : 'No reservations found in this view.' }}
      </p>
      <router-link to="/movies" class="btn-primary px-6 py-2.5 text-xs font-bold font-sans">Browse Showing Movies</router-link>
    </div>

    <!-- Bookings Ticket Cards (Casual Dark Glassy Cards) -->
    <div v-else class="grid gap-6">
      <div 
        v-for="booking in displayedBookings" 
        :key="booking.id" 
        class="card flex flex-col lg:flex-row border border-white/15 bg-black/70 backdrop-blur-2xl overflow-hidden !p-0 transition-all hover:border-orange-500/50 shadow-xl rounded-2xl"
        :class="{ 'opacity-70 border-red-900/40': booking.status === 'cancelled' }"
      >
        <!-- Left Thumbnail Poster -->
        <div v-if="booking.movie?.image" class="w-full lg:w-44 h-44 lg:h-auto bg-black flex-shrink-0 relative overflow-hidden">
          <img :src="getImageUrl(booking.movie.image)" alt="Movie Poster" class="w-full h-full object-cover" />
          <div 
            class="absolute top-3 left-3 text-black text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider font-sans shadow-md"
            :class="booking.status === 'cancelled' ? 'bg-red-500 text-white' : (isUpcoming(booking) ? 'bg-orange-500 text-white' : 'bg-slate-400 text-black')"
          >
            {{ booking.status === 'cancelled' ? '🚫 CANCELLED & REFUNDED' : (isUpcoming(booking) ? '✓ UPCOMING' : '✓ PAST SHOW') }}
          </div>
        </div>
        <div v-else class="w-full lg:w-44 h-44 lg:h-auto bg-slate-900 flex items-center justify-center flex-shrink-0 relative border-r border-white/10">
          <span class="text-slate-400 text-3xl">🎬</span>
          <div 
            class="absolute top-3 left-3 text-black text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider font-sans shadow-md"
            :class="booking.status === 'cancelled' ? 'bg-red-500 text-white' : (isUpcoming(booking) ? 'bg-orange-500 text-white' : 'bg-slate-400 text-black')"
          >
            {{ booking.status === 'cancelled' ? '🚫 CANCELLED & REFUNDED' : (isUpcoming(booking) ? '✓ UPCOMING' : '✓ PAST SHOW') }}
          </div>
        </div>
        
        <!-- Main Content Section -->
        <div class="flex-grow p-5 flex flex-col justify-between space-y-3">
          <div>
            <div class="flex justify-between items-start mb-2 flex-wrap gap-2">
              <div>
                <h2 class="text-xl font-bold text-white font-cinematic tracking-wide">{{ booking.movie?.title || 'Movie' }}</h2>
                <div class="text-xs font-sans text-slate-300 mt-0.5 flex items-center gap-2">
                  <span>🏛️ {{ booking.showtime?.auditoriumDetail?.cinema?.name ? `${booking.showtime.auditoriumDetail.cinema.name} - ${booking.showtime.auditoriumDetail.name}` : (booking.showtime?.auditorium || 'Main Cinema Hall') }}</span>
                </div>
              </div>
              
              <div class="flex items-center gap-2 flex-wrap">
                <!-- Payment Provider Badge -->
                <span class="px-2.5 py-1 rounded-full font-bold uppercase text-[11px] font-sans flex items-center gap-1.5" :class="getPaymentBadgeStyle(booking.payment_method)">
                  <span>{{ getPaymentIcon(booking.payment_method) }}</span>
                  <span>{{ getPaymentName(booking.payment_method) }}</span>
                </span>

                <!-- View E-Ticket Pass Button (if active) -->
                <button 
                  v-if="booking.status !== 'cancelled'"
                  @click="openTicket(booking)" 
                  class="text-orange-400 hover:text-white text-xs font-bold bg-orange-500/10 border border-orange-500/30 px-3 py-1 rounded-xl transition-all hover:bg-orange-500 flex items-center gap-1 font-sans"
                >
                  <span>🎟️</span> View E-Ticket Pass
                </button>

                <!-- Download / Print Ticket Button (if active) -->
                <button 
                  v-if="booking.status !== 'cancelled'"
                  @click="openTicket(booking)" 
                  class="text-emerald-400 hover:text-white text-xs font-bold bg-emerald-500/10 border border-emerald-500/30 px-3 py-1 rounded-xl transition-all hover:bg-emerald-500 flex items-center gap-1 font-sans"
                >
                  <span>📥</span> Download
                </button>

                <!-- Cancel & Refund Request Option -->
                <button 
                  v-if="booking.status !== 'cancelled' && isUpcoming(booking)"
                  @click="openCancelModal(booking)" 
                  class="text-red-400 hover:text-white text-xs font-bold bg-red-500/10 border border-red-500/30 px-3 py-1 rounded-xl transition-all hover:bg-red-600 flex items-center gap-1 font-sans"
                >
                  <span>🚫</span> Cancel Ticket
                </button>
              </div>
            </div>

            <!-- Datetime & Seat info (Casual Dark Glassy Pills) -->
            <div class="flex flex-wrap items-center text-xs text-gray-200 gap-y-2 gap-x-3 mt-2.5 font-sans">
              <span class="flex items-center bg-black/60 backdrop-blur-2xl px-3 py-1 rounded-lg border border-white/15 font-medium text-white shadow-sm">
                <span class="mr-1.5 text-orange-400">🕒</span> 
                {{ booking.showtime?.start_time ? new Date(booking.showtime.start_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : (booking.movie?.show_time ? new Date(booking.movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD') }}
              </span>

              <span class="flex items-center bg-black/60 backdrop-blur-2xl px-3 py-1 rounded-lg border border-white/15 font-medium text-white shadow-sm">
                <span class="mr-1.5 text-orange-400">🪑</span> {{ booking.seats_booked }} Seats
              </span>

              <span class="flex items-center font-bold text-orange-300 bg-orange-950/40 backdrop-blur-2xl border border-orange-500/30 px-3 py-1 rounded-lg shadow-sm" v-if="booking.seat_numbers && booking.seat_numbers.length">
                Seats: {{ booking.seat_numbers.join(', ') }}
              </span>

              <span 
                class="flex items-center font-bold px-3 py-1 rounded-lg border backdrop-blur-2xl shadow-sm"
                :class="booking.status === 'cancelled' ? 'text-red-300 bg-red-950/40 border-red-500/30' : 'text-emerald-300 bg-emerald-950/40 border-emerald-500/30'"
              >
                {{ booking.status === 'cancelled' ? `REFUNDED: ${Number(booking.refund_amount || booking.total_price).toLocaleString()} ETB` : `Total: ${booking.total_price ? `${Number(booking.total_price).toLocaleString()} ETB` : 'Paid'}` }}
              </span>
            </div>

            <!-- Itemized Ticket Breakdown (Casual Dark Pill) -->
            <div v-if="booking.ticket_details && booking.ticket_details.length" class="mt-2.5 flex gap-2 flex-wrap">
              <span 
                v-for="td in booking.ticket_details" 
                :key="td.seat_id"
                class="px-2.5 py-0.5 rounded-lg bg-black/60 backdrop-blur-2xl border border-white/10 text-xs font-sans text-slate-300 shadow-sm"
              >
                <strong class="text-orange-400">Seat {{ td.seat_id }}</strong> ({{ td.type }}: {{ td.price }} ETB)
              </span>
            </div>
          </div>

          <!-- Transaction Footer & Digital E-Ticket Barcode -->
          <div class="text-xs text-slate-400 font-sans border-t border-white/10 pt-2.5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
              <span class="block text-slate-400 font-sans">
                Booking Ref: <strong class="text-orange-400 font-sans">{{ booking.transaction_ref || `MV-${booking.id}98` }}</strong>
                <span v-if="booking.refund_ref" class="ml-2 text-red-400 font-bold">(Refund Ref: {{ booking.refund_ref }})</span>
              </span>
              <span class="block text-[11px] text-slate-400 font-sans">Booked on: {{ new Date(booking.created_at).toLocaleDateString() }}</span>
            </div>

            <!-- Simulated E-Ticket QR Code / Barcode Button (White Pass) -->
            <button 
              v-if="booking.status !== 'cancelled'"
              @click="openTicket(booking)" 
              class="flex items-center gap-2.5 bg-black/70 hover:bg-black/90 p-1.5 px-2.5 rounded-lg border border-white/20 hover:border-white/40 transition-all shadow-sm group"
            >
              <div class="flex flex-col items-center">
                <!-- Barcode visual simulation (White) -->
                <div class="flex items-center gap-0.5 h-5">
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
                <span class="text-[9px] text-white group-hover:text-slate-200 tracking-wider mt-0.5 font-sans font-bold">SCAN / DOWNLOAD QR PASS</span>
              </div>
            </button>
            <div v-else class="text-red-400 font-bold text-xs font-sans">
              [ SEATS RESTORED TO CINEMA ]
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- DIGITAL E-TICKET PASS MODAL -->
    <TicketModal 
      :show="showTicketModal" 
      :booking="activeBooking" 
      @close="showTicketModal = false" 
    />

    <!-- CANCELLATION & REFUND CONFIRMATION MODAL -->
    <div v-if="showCancelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md">
      <div class="bg-slate-950 border border-red-500/40 w-full max-w-md rounded-3xl p-6 space-y-6 shadow-2xl relative text-white font-sans">
        <button @click="showCancelModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white font-bold">✕</button>

        <div class="border-b border-white/10 pb-4">
          <h3 class="text-xl font-bold text-red-400 font-orbitron flex items-center gap-2">
            🚫 CANCEL & REFUND TICKET
          </h3>
          <p class="text-xs text-slate-400 font-mono mt-1">Review 2-hour policy and refund details</p>
        </div>

        <div v-if="cancellingBooking" class="space-y-4 text-xs font-mono">
          <div class="p-4 bg-red-950/40 border border-red-500/30 rounded-2xl space-y-2">
            <div class="flex justify-between">
              <span class="text-slate-400">Movie:</span>
              <span class="font-bold text-white">{{ cancellingBooking.movie?.title }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Showtime:</span>
              <span class="font-bold text-orange-400">{{ new Date(cancellingBooking.showtime?.start_time || cancellingBooking.movie?.show_time).toLocaleString(undefined, {dateStyle:'short', timeStyle:'short'}) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-400">Seats to Restore:</span>
              <span class="font-bold text-emerald-400">{{ cancellingBooking.seat_numbers?.join(', ') }}</span>
            </div>
          </div>

          <!-- Refund Summary Pill -->
          <div class="p-4 bg-emerald-950/40 border border-emerald-500/30 rounded-2xl flex justify-between items-center">
            <div>
              <span class="text-[10px] text-slate-400 uppercase block">100% Refund Destination</span>
              <span class="text-xs font-bold text-white flex items-center gap-1 mt-0.5">
                {{ getPaymentIcon(cancellingBooking.payment_method) }} {{ getPaymentName(cancellingBooking.payment_method) }}
              </span>
            </div>
            <div class="text-right">
              <span class="text-xl font-bold text-emerald-400">{{ Number(cancellingBooking.total_price || 0).toLocaleString() }} ETB</span>
            </div>
          </div>

          <div class="text-[11px] text-slate-300 bg-slate-900 p-3 rounded-xl border border-white/10">
            ℹ️ <strong>Rule Check Passed:</strong> Showtime is more than 2 hours away. Clicking confirm will issue a full refund and immediately release seats back to the hall.
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button @click="showCancelModal = false" class="w-1/2 py-3 rounded-xl border border-slate-700 text-slate-300 text-xs font-bold hover:bg-slate-800">
            Keep My Ticket
          </button>
          <button @click="confirmCancellation" :disabled="processingCancel" class="w-1/2 btn-primary py-3 text-xs font-bold bg-red-600 hover:bg-red-700 flex items-center justify-center gap-2">
            <span v-if="processingCancel" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
            <span>{{ processingCancel ? 'PROCESSING...' : 'CONFIRM REFUND' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue"
import api from "../services/api"
import TicketModal from "../components/TicketModal.vue"

const bookings = ref([])
const loading = ref(true)
const searchQuery = ref("")
const activeTab = ref("upcoming")
const showTicketModal = ref(false)
const activeBooking = ref(null)

const showCancelModal = ref(false)
const cancellingBooking = ref(null)
const processingCancel = ref(false)

const openTicket = (booking) => {
  activeBooking.value = booking
  showTicketModal.value = true
}

const openCancelModal = (booking) => {
  cancellingBooking.value = booking
  showCancelModal.value = true
}

const isUpcoming = (booking) => {
  if (booking.status === 'cancelled') return false;
  const dStr = booking.showtime?.start_time || booking.movie?.show_time;
  if (!dStr) return true;
  return new Date(dStr) >= new Date();
}

const upcomingBookings = computed(() => {
  return bookings.value.filter(b => b.status !== 'cancelled' && isUpcoming(b));
})

const pastBookings = computed(() => {
  return bookings.value.filter(b => b.status !== 'cancelled' && !isUpcoming(b));
})

const cancelledBookings = computed(() => {
  return bookings.value.filter(b => b.status === 'cancelled');
})

const displayedBookings = computed(() => {
    let list = bookings.value;
    if (activeTab.value === 'upcoming') {
      list = upcomingBookings.value;
    } else if (activeTab.value === 'past') {
      list = pastBookings.value;
    } else if (activeTab.value === 'cancelled') {
      list = cancelledBookings.value;
    }

    if (!searchQuery.value) return list;
    const query = searchQuery.value.toLowerCase();
    return list.filter(booking => 
        (booking.movie?.title && booking.movie.title.toLowerCase().includes(query)) ||
        (booking.showtime?.auditorium && booking.showtime.auditorium.toLowerCase().includes(query)) ||
        (booking.transaction_ref && booking.transaction_ref.toLowerCase().includes(query)) ||
        (booking.refund_ref && booking.refund_ref.toLowerCase().includes(query))
    );
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

const confirmCancellation = async () => {
    if (!cancellingBooking.value) return
    processingCancel.value = true
    try {
        const res = await api.delete(`/bookings/${cancellingBooking.value.id}`)
        alert(res.data.message || "Ticket cancelled and refund processed!")
        showCancelModal.value = false
        cancellingBooking.value = null
        loadBookings()
    } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          alert(`Cancellation Denied: ${err.response.data.message}`)
        } else {
          alert("Cancellation failed. Please check policy conditions.")
        }
    } finally {
        processingCancel.value = false
    }
}

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

onMounted(loadBookings)
</script>