<template>
  <div class="container">
    <div class="mb-10">
      <h1 class="text-3xl font-bold text-gray-900 mb-1">My Bookings</h1>
      <p class="text-gray-600">Your reservations and ticket history</p>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-yellow-500 border-t-transparent rounded-full"></div>
    </div>

    <div v-else-if="bookings.length === 0" class="card text-center py-12 border-yellow-200">
      <p class="text-gray-600 mb-6 font-medium">No bookings found yet.</p>
      <router-link to="/movies" class="btn-primary">Explore Movies</router-link>
    </div>

    <div v-else class="grid gap-3">
      <div v-for="booking in bookings" :key="booking.id" class="card flex flex-col sm:flex-row shadow-sm border-yellow-200 overflow-hidden !p-0 transition-shadow hover:shadow-md">
        <!-- Thumbnail Image -->
        <div v-if="booking.movie.image" class="w-full sm:w-32 h-32 sm:h-auto bg-gray-100 flex-shrink-0">
          <img :src="getImageUrl(booking.movie.image)" alt="Movie Poster" class="w-full h-full object-cover" />
        </div>
        <div v-else class="w-full sm:w-32 h-32 sm:h-auto bg-gray-200 flex items-center justify-center flex-shrink-0">
          <span class="text-gray-400 text-xs font-medium">No Image</span>
        </div>
        
        <!-- Content -->
        <div class="flex-grow p-4 sm:p-5 flex flex-col justify-center">
          <div class="flex justify-between items-start mb-1">
            <h2 class="text-lg font-bold text-gray-900">{{ booking.movie.title }}</h2>
            <span class="text-green-500 bg-green-50 px-2 py-0.5 rounded font-bold uppercase text-[10px] tracking-widest hidden sm:inline-block">Confirmed</span>
          </div>
          <div class="flex flex-wrap items-center text-xs sm:text-sm text-gray-600 gap-y-1.5 gap-x-4 mt-0.5">
            <span class="flex items-center whitespace-nowrap"><span class="mr-1.5 opacity-70">🕒</span> {{ booking.movie.show_time ? new Date(booking.movie.show_time).toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'}) : 'TBD' }}</span>
            <span class="flex items-center whitespace-nowrap"><span class="mr-1.5 opacity-70">🪑</span> {{ booking.seats_booked }} Seats</span>
            <span class="flex items-center whitespace-nowrap font-medium text-yellow-800 bg-yellow-100 px-2 py-0.5 rounded-md" v-if="booking.seat_numbers && booking.seat_numbers.length">
              Seats: {{ booking.seat_numbers.join(', ') }}
            </span>
            <!-- Mobile Badge -->
            <span class="text-green-500 font-bold uppercase text-[10px] tracking-widest sm:hidden w-full mt-1">Confirmed</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import api from "../services/api"

const bookings = ref([])
const loading = ref(true)

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

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

onMounted(loadBookings)
</script>