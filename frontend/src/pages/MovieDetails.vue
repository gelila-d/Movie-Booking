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
      
      <!-- Movie details card -->
      <div class="relative">
        <!-- Holographic border effect -->
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-orange-500/30 via-amber-500/30 to-orange-500/30 p-[2px]">
          <div class="h-full w-full rounded-3xl bg-slate-900/90 backdrop-blur-xl"></div>
        </div>
        
        <div class="relative card border-slate-700/50 rounded-3xl overflow-hidden">
          <div class="grid lg:grid-cols-2 gap-8 p-8">
            <!-- Movie poster -->
            <div class="relative">
              <div v-if="movie.image" class="relative w-full h-[480px] rounded-2xl overflow-hidden group">
                <img 
                  :src="getImageUrl(movie.image)" 
                  alt="Movie Poster" 
                  class="w-full h-full object-cover opacity-95 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700" 
                />
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 via-transparent to-transparent"></div>
                
                <!-- Rating badge -->
                <div class="absolute top-4 right-4">
                  <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-4 py-2 rounded-full text-sm font-bold">
                    ★ 9.4
                  </div>
                </div>
              </div>
              
              <div v-else class="w-full h-[480px] bg-slate-800/50 rounded-2xl flex items-center justify-center border border-slate-600/50">
                <div class="text-center">
                  <div class="w-20 h-20 mx-auto mb-4 bg-slate-700/50 rounded-full flex items-center justify-center">
                    <span class="text-4xl">🎬</span>
                  </div>
                  <span class="text-slate-400 font-medium">No Poster Available</span>
                </div>
              </div>
            </div>

            <!-- Movie info -->
            <div class="space-y-6">
              <div>
                <h1 class="neon-text text-2xl sm:text-3xl font-semibold font-orbitron tracking-wider mb-4">
                  {{ movie.title.toUpperCase() }}
                </h1>
                <div class="bg-slate-800/30 border border-slate-600/30 rounded-xl p-6 backdrop-blur-sm">
                  <p class="text-slate-200 leading-relaxed text-lg font-light">
                    {{ movie.description }}
                  </p>
                </div>
              </div>

              <!-- Available Showtimes Selector -->
              <div class="space-y-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-[#ef6a26] font-mono flex items-center gap-2">
                  <span>📅 SELECT SHOWTIME & AUDITORIUM</span>
                </h3>

                <div v-if="showtimes.length === 0" class="p-4 bg-slate-800/30 border border-amber-500/30 rounded-xl text-slate-300 text-sm">
                  No showtimes currently scheduled for this movie. Check back soon or contact support!
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-1">
                  <button
                    v-for="st in showtimes"
                    :key="st.id"
                    @click="selectShowtime(st)"
                    class="text-left p-3.5 rounded-xl border transition-all duration-200 flex flex-col justify-between"
                    :class="selectedShowtime?.id === st.id 
                      ? 'bg-gradient-to-r from-orange-500/20 to-amber-500/20 border-[#ef6a26] shadow-lg shadow-[#ef6a26]/20' 
                      : 'bg-slate-800/40 border-slate-700/60 hover:border-slate-500 hover:bg-slate-800/80'"
                  >
                    <div class="flex items-center justify-between mb-1">
                      <span class="text-xs font-mono font-bold text-purple-300 bg-purple-950/60 border border-purple-500/30 px-2 py-0.5 rounded">
                        🏛️ {{ st.auditorium }}
                      </span>
                      <span class="text-sm font-bold text-emerald-400 font-mono">
                        ${{ Number(st.price).toFixed(2) }}
                      </span>
                    </div>
                    
                    <div class="text-sm font-bold text-white font-mono mt-1">
                      🕒 {{ new Date(st.start_time).toLocaleString(undefined, {dateStyle: 'short', timeStyle: 'short'}) }}
                    </div>

                    <div class="flex items-center justify-between mt-2 text-xs text-slate-400 font-mono">
                      <span>Ends: {{ new Date(st.end_time).toLocaleTimeString(undefined, {timeStyle: 'short'}) }}</span>
                      <span :class="st.available_seats > 0 ? 'text-green-400' : 'text-red-400'">
                        {{ st.available_seats }} left
                      </span>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking section -->
          <div v-if="selectedShowtime || showtimes.length === 0" class="border-t border-slate-700/50 p-8 bg-slate-800/10">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-2">
              <h2 class="text-2xl font-bold text-[#ef6a26] font-orbitron tracking-wide">
                BOOK YOUR TICKETS
              </h2>
              <div v-if="selectedShowtime" class="text-xs font-mono text-slate-300 bg-black/40 px-3 py-1.5 rounded-lg border border-white/10">
                Selected: <span class="text-purple-300 font-bold">{{ selectedShowtime.auditorium }}</span> @ <span class="text-white font-bold">{{ new Date(selectedShowtime.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
              </div>
            </div>

            <BookingForm 
              :movieId="movie.id" 
              :showtimeId="selectedShowtime?.id || null"
              :price="selectedShowtime ? Number(selectedShowtime.price) : 0"
              :availableSeats="selectedShowtime ? selectedShowtime.available_seats : movie.available_seats" 
              :totalSeats="selectedShowtime ? selectedShowtime.total_seats : movie.total_seats"
              :bookedSeats="bookedSeats"
              :loading="booking"
              @book="handleBooking"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import BookingForm from '../components/BookingForm.vue'

const route = useRoute()
const router = useRouter()
const movie = ref(null)
const showtimes = ref([])
const selectedShowtime = ref(null)
const bookedSeats = ref([])
const loading = ref(true)
const booking = ref(false)

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

const handleBooking = async (selectedSeats) => {
  booking.value = true
  try {
    const payload = {
      seat_numbers: selectedSeats
    }

    if (selectedShowtime.value) {
      payload.showtime_id = selectedShowtime.value.id
    } else {
      payload.movie_id = movie.value.id
    }

    await api.post('/bookings', payload)
    alert('Tickets booked successfully!')
    router.push('/my-bookings')
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
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

onMounted(fetchMovieAndShowtimes)
</script>
