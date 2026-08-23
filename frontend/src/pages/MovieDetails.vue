<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <div v-if="loading" class="flex justify-center py-20">
      <div class="relative">
        <div class="w-16 h-16 border-4 border-slate-700 border-t-[#ef6a26] rounded-full animate-spin"></div>
        <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-orange-500 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
      </div>
    </div>

    <div v-else-if="movie" class="space-y-8">
      <!-- Back button -->
      <router-link 
        to="/movies" 
        class="inline-flex items-center gap-2 text-[#ef6a26] hover:text-orange-400 transition-colors text-sm font-medium group"
      >
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        BACK TO MOVIES
      </router-link>
      
      <!-- Movie details card (Black Glassy) -->
      <div class="relative">
        <!-- Holographic border effect -->
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-orange-500/30 via-orange-400/30 to-orange-500/30 p-[2px]">
          <div class="h-full w-full rounded-3xl bg-black/80 backdrop-blur-2xl"></div>
        </div>
        
        <div class="relative card border-white/15 bg-black/80 backdrop-blur-2xl rounded-3xl overflow-hidden shadow-[0_20px_60px_rgba(0,0,0,0.9),inset_0_1px_1px_rgba(255,255,255,0.1)]">
          <div class="grid lg:grid-cols-[260px_1fr] gap-6 p-6">
            <!-- Left Column: Movie poster & metadata -->
            <div class="space-y-4">
              <div class="relative flex justify-center">
                <div v-if="movie.image" class="relative w-full max-w-[260px] h-[320px] rounded-2xl overflow-hidden group border border-white/15 shadow-lg">
                  <img 
                    :src="getImageUrl(movie.image)" 
                    alt="Movie Poster" 
                    class="w-full h-full object-cover opacity-95 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700" 
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                  
                  <!-- Rating badge -->
                  <div class="absolute top-3 right-3">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                      ★ 9.4
                    </div>
                  </div>
                </div>
                
                <div v-else class="w-full max-w-[260px] h-[320px] bg-black/60 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/15">
                  <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 bg-white/5 rounded-full flex items-center justify-center border border-white/10">
                      <span class="text-3xl">🎬</span>
                    </div>
                    <span class="text-slate-400 text-sm font-medium">No Poster Available</span>
                  </div>
                </div>
              </div>

              <!-- Plot synopsis card under poster (Black Glassy) -->
              <div class="bg-black/60 backdrop-blur-2xl border border-white/15 rounded-xl p-4 space-y-2 shadow-md">
                <span class="text-[10px] uppercase font-bold tracking-wider text-orange-400 font-sans block">PLOT SYNOPSIS</span>
                <p class="text-slate-200 leading-relaxed text-xs font-light">
                  {{ movie.description }}
                </p>
              </div>

              <button 
                @click="toggleWatchlist"
                class="w-full py-2.5 rounded-xl text-xs font-bold font-sans transition-all flex items-center justify-center gap-1.5 border"
                :class="inWatchlist ? 'bg-red-950/70 border-red-500/50 text-red-300 shadow-md shadow-red-900/30' : 'bg-black/60 backdrop-blur-xl border-white/15 text-slate-300 hover:border-orange-500/50 hover:bg-black/80'"
              >
                <span>{{ inWatchlist ? '💖 Saved in Watchlist' : '❤️ Add to Watchlist' }}</span>
              </button>
            </div>

            <!-- Right Column: Title, Showtimes & Interactive Seat Map (Beside Poster Image) -->
            <div class="space-y-5">
              <div class="flex justify-between items-center gap-4 border-b border-white/10 pb-3">
                <h1 class="font-cinematic text-xl sm:text-3xl font-bold tracking-wide text-orange-500">
                  {{ movie.title }}
                </h1>
                <div v-if="selectedShowtime" class="text-xs font-sans text-slate-300 bg-black/60 backdrop-blur-xl px-3 py-1 rounded-lg border border-white/15 hidden sm:block">
                  <span class="text-orange-400 font-bold">{{ selectedShowtime.auditoriumDetail?.cinema?.name ? `${selectedShowtime.auditoriumDetail.cinema.name} (${selectedShowtime.auditoriumDetail.name})` : selectedShowtime.auditorium }}</span>
                </div>
              </div>

              <!-- Showtime Selector -->
              <div class="space-y-2">
                <h3 class="text-xs uppercase font-bold tracking-wider text-orange-400 font-sans flex items-center gap-2">
                  <span>📅 1. SELECT CINEMA & SHOWTIME</span>
                </h3>

                <div v-if="showtimes.length === 0" class="p-3 bg-black/60 backdrop-blur-xl border border-orange-500/30 rounded-xl text-slate-300 text-xs">
                  No showtimes currently scheduled for this movie. Check back soon!
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-36 overflow-y-auto pr-1">
                  <button
                    v-for="st in showtimes"
                    :key="st.id"
                    @click="selectShowtime(st)"
                    class="text-left p-2.5 rounded-xl border transition-all duration-200 flex flex-col justify-between"
                    :class="selectedShowtime?.id === st.id 
                      ? 'bg-orange-500/20 border-orange-500 shadow-md shadow-orange-950/30' 
                      : 'bg-black/60 backdrop-blur-xl border-white/15 text-slate-300 hover:border-orange-400/50 hover:bg-black/80'"
                  >
                    <div class="flex items-center justify-between mb-0.5">
                      <span class="text-[11px] font-sans font-bold text-orange-300 bg-orange-950/60 border border-orange-500/30 px-2 py-0.5 rounded truncate max-w-[130px]">
                        🏛️ {{ st.auditoriumDetail?.cinema?.name ? `${st.auditoriumDetail.cinema.name} - ${st.auditoriumDetail.name}` : st.auditorium }}
                      </span>
                      <span class="text-xs font-bold text-emerald-400 font-sans">
                        {{ Number(st.price || 100).toFixed(0) }} Birr
                      </span>
                    </div>
                    
                    <div class="text-xs font-bold text-white font-sans mt-0.5 flex justify-between items-center">
                      <span>🕒 {{ new Date(st.start_time).toLocaleString(undefined, {dateStyle: 'short', timeStyle: 'short'}) }}</span>
                      <span :class="st.available_seats > 0 ? 'text-green-400 text-[10px]' : 'text-red-400 text-[10px]'">
                        {{ st.available_seats }} left
                      </span>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Interactive Seat Mapping Section (Directly Beside Poster Image) -->
              <div v-if="selectedShowtime || showtimes.length === 0" class="border-t border-white/10 pt-4 space-y-3">
                <h2 class="text-sm font-bold text-orange-500 font-sans tracking-wider flex items-center gap-2">
                  <span>🪑 2. SELECT SEATS & CONFIRM TICKETS</span>
                </h2>

                <BookingForm 
                  :movieId="movie.id" 
                  :showtimeId="selectedShowtime?.id || null"
                  :price="selectedShowtime ? Number(selectedShowtime.price || 100) : 100"
                  :vipPrice="selectedShowtime ? Number(selectedShowtime.vip_price || 150) : 150"
                  :studentPrice="selectedShowtime ? Number(selectedShowtime.student_price || 80) : 80"
                  :childPrice="selectedShowtime ? Number(selectedShowtime.child_price || 60) : 60"
                  :vipRowsCount="selectedShowtime?.auditoriumDetail?.vip_rows_count || 2"
                  :availableSeats="selectedShowtime ? selectedShowtime.available_seats : movie.available_seats" 
                  :totalSeats="selectedShowtime ? selectedShowtime.total_seats : movie.total_seats"
                  :rowsCount="selectedShowtime?.auditoriumDetail?.rows_count || null"
                  :seatsPerRow="selectedShowtime?.auditoriumDetail?.seats_per_row || null"
                  :bookedSeats="bookedSeats"
                  :loading="booking"
                  @book="handleBooking"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- DIGITAL E-TICKET CONFIRMATION PASS MODAL -->
    <TicketModal 
      :show="showTicketModal" 
      :booking="createdBooking" 
      @close="closeTicketModal" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import BookingForm from '../components/BookingForm.vue'
import TicketModal from '../components/TicketModal.vue'

const route = useRoute()
const router = useRouter()
const movie = ref(null)
const showtimes = ref([])
const selectedShowtime = ref(null)
const bookedSeats = ref([])
const loading = ref(true)
const booking = ref(false)

const showTicketModal = ref(false)
const createdBooking = ref(null)
const inWatchlist = ref(false)

const checkWatchlistState = async () => {
  if (!localStorage.getItem('token') || !route.params.id) return;
  try {
    const res = await api.get('/watchlist/ids');
    if (Array.isArray(res.data) && res.data.includes(Number(route.params.id))) {
      inWatchlist.value = true;
    }
  } catch (e) {
    // Guest or error
  }
}

const toggleWatchlist = async () => {
  if (!localStorage.getItem('token')) {
    alert("Please log in to save movies to your watchlist.");
    return;
  }

  try {
    const res = await api.post('/watchlist/toggle', { movie_id: Number(route.params.id) });
    if (res.data.status === 'added') {
      inWatchlist.value = true;
    } else {
      inWatchlist.value = false;
    }
  } catch (err) {
    console.error("Failed to toggle watchlist:", err);
  }
}

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

const selectShowtime = async (st) => {
  selectedShowtime.value = st
  try {
    const seatsRes = await api.get(`/showtimes/${st.id}/booked-seats`)
    bookedSeats.value = seatsRes.data
  } catch (err) {
    console.error("Failed to load booked seats for showtime:", err)
  }
}

const fetchMovieAndShowtimes = async () => {
  try {
    const [movieRes, showtimesRes] = await Promise.all([
      api.get(`/movies/${route.params.id}`),
      api.get(`/showtimes?movie_id=${route.params.id}`)
    ])
    
    movie.value = movieRes.data
    showtimes.value = showtimesRes.data

    if (showtimes.value.length > 0) {
      await selectShowtime(showtimes.value[0])
    } else if (movie.value) {
      const seatsRes = await api.get(`/movies/${movie.value.id}/booked-seats`)
      bookedSeats.value = seatsRes.data
    }
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const handleBooking = async ({ selectedSeats, ticketDetails, paymentMethod }) => {
  if (!localStorage.getItem('token')) {
    alert('Please sign in or register to complete your ticket booking.');
    router.push({ path: '/login', query: { redirect: route.fullPath } });
    return;
  }

  booking.value = true
  try {
    const payload = {
      seat_numbers: selectedSeats,
      ticket_details: ticketDetails,
      payment_method: paymentMethod || 'telebirr'
    }

    if (selectedShowtime.value) {
      payload.showtime_id = selectedShowtime.value.id
    } else {
      payload.movie_id = movie.value.id
    }

    const res = await api.post('/bookings', payload)
    createdBooking.value = res.data
    showTicketModal.value = true
  } catch (err) {
    if (err.response?.status === 401 || err.response?.data?.message === 'Unauthenticated.') {
        alert('Your session has expired. Please sign in to complete your reservation.');
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        router.push({ path: '/login', query: { redirect: route.fullPath } });
    } else if (err.response && err.response.data && err.response.data.errors) {
        const errors = err.response.data.errors;
        const firstKey = Object.keys(errors)[0];
        alert(errors[firstKey][0]);
    } else {
        alert(err.response?.data?.message || 'Booking failed. Check your connection.')
    }
  } finally {
    booking.value = false
  }
}

const closeTicketModal = () => {
  showTicketModal.value = false
  router.push('/my-bookings')
}

onMounted(() => {
  fetchMovieAndShowtimes()
  checkWatchlistState()
})
</script>
