<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <div v-if="loading" class="flex justify-center py-20">
      <div class="relative">
        <div class="w-16 h-16 border-4 border-slate-700 border-t-purple-500 rounded-full animate-spin"></div>
        <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-violet-500 rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
      </div>
    </div>

    <div v-else-if="movie" class="space-y-8">
      <!-- Back button -->
      <router-link 
        to="/movies" 
        class="inline-flex items-center gap-2 text-purple-400 hover:text-purple-300 transition-colors text-sm font-medium group"
      >
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        BACK TO MOVIES
      </router-link>
      
      <!-- Movie details card -->
      <div class="relative">
        <!-- Holographic border effect -->
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-purple-500/30 via-violet-500/30 to-fuchsia-500/30 p-[2px]">
          <div class="h-full w-full rounded-3xl bg-slate-900/90 backdrop-blur-xl"></div>
        </div>
        
        <div class="relative card border-slate-700/50 rounded-3xl overflow-hidden">
          <div class="grid lg:grid-cols-2 gap-8 p-8">
            <!-- Movie poster -->
            <div class="relative">
              <div v-if="movie.image" class="relative w-full h-[500px] rounded-2xl overflow-hidden group">
                <!-- Enhanced poster visibility -->
                <img 
                  :src="getImageUrl(movie.image)" 
                  alt="Movie Poster" 
                  class="w-full h-full object-cover opacity-95 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700" 
                />
                <!-- Subtle gradient overlay for text readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/20 via-transparent to-transparent"></div>
                
                <!-- Rating badge -->
                <div class="absolute top-4 right-4">
                  <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-black px-4 py-2 rounded-full text-sm font-bold">
                    ★ {{ (Math.random() * 2 + 7).toFixed(1) }}
                  </div>
                </div>
              </div>
              
              <div v-else class="w-full h-[500px] bg-slate-800/50 rounded-2xl flex items-center justify-center border border-slate-600/50">
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
                <!-- Enhanced description visibility -->
                <div class="bg-slate-800/30 border border-slate-600/30 rounded-xl p-6 backdrop-blur-sm">
                  <p class="text-slate-200 leading-relaxed text-lg font-light">
                    {{ movie.description }}
                  </p>
                </div>
              </div>

              <!-- Movie details with futuristic styling -->
              <div class="grid grid-cols-2 gap-6">
                <div class="bg-slate-800/20 border border-purple-500/20 rounded-xl p-4 backdrop-blur-sm">
                  <p class="text-purple-400 text-xs uppercase font-bold tracking-wider mb-2 font-mono">Show Time</p>
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                    <p class="text-slate-200 font-medium font-mono text-sm">
                      {{ movie.show_time ? new Date(movie.show_time).toLocaleString() : 'TBD' }}
                    </p>
                  </div>
                </div>
                
                <div class="bg-slate-800/20 border border-green-500/20 rounded-xl p-4 backdrop-blur-sm">
                  <p class="text-green-400 text-xs uppercase font-bold tracking-wider mb-2 font-mono">Available Seats</p>
                  <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <p class="text-slate-200 font-medium font-mono text-sm">
                      {{ movie.available_seats }} / {{ movie.total_seats }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking section -->
          <div class="border-t border-slate-700/50 p-8 bg-slate-800/10">
            <h2 class="text-2xl font-bold text-purple-300 mb-6 font-orbitron tracking-wide">
              BOOK YOUR TICKETS
            </h2>
            <BookingForm 
              :movieId="movie.id" 
              :availableSeats="movie.available_seats" 
              :totalSeats="movie.total_seats"
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
const bookedSeats = ref([])
const loading = ref(true)
const booking = ref(false)

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

const fetchMovie = async () => {
  try {
    const res = await api.get(`/movies/${route.params.id}`)
    movie.value = res.data
    
    if (movie.value) {
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
    await api.post('/bookings', {
      movie_id: movie.value.id,
      seat_numbers: selectedSeats
    })
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

onMounted(fetchMovie)
</script>
