<template>
  <div class="container mx-auto px-4 py-8 max-w-2xl">
    <div v-if="loading" class="text-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-yellow-500 border-t-transparent rounded-full mx-auto"></div>
    </div>

    <div v-else-if="movie" class="card space-y-6 border-yellow-200 shadow-sm">
      <router-link to="/movies" class="text-yellow-600 hover:text-yellow-700 hover:underline text-sm font-medium block">
        &larr; Back to Movies
      </router-link>
      
      <div v-if="movie.image" class="w-full mb-6 max-h-96 bg-gray-100 rounded-lg overflow-hidden flex justify-center items-center">
        <img :src="getImageUrl(movie.image)" alt="Movie Poster" class="h-full max-h-96 w-auto object-contain" />
      </div>

      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ movie.title }}</h1>
        <p class="text-gray-600 leading-relaxed">{{ movie.description }}</p>
      </div>

      <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-200">
        <div>
          <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Show Time</p>
          <p class="text-gray-900 font-medium">{{ movie.show_time ? new Date(movie.show_time).toLocaleString() : 'TBD' }}</p>
        </div>
        <div>
          <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Available Seats</p>
          <p class="text-gray-900 font-medium">{{ movie.available_seats }} / {{ movie.total_seats }}</p>
        </div>
      </div>

      <div class="pt-4">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Book Your Tickets</h2>
        <BookingForm 
          :movieId="movie.id" 
          :availableSeats="movie.available_seats" 
          :loading="booking"
          @book="handleBooking"
        />
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
const loading = ref(true)
const booking = ref(false)

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

const fetchMovie = async () => {
  try {
    const res = await api.get('/movies')
    movie.value = res.data.find(m => m.id == route.params.id)
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const handleBooking = async (seats) => {
  booking.value = true
  try {
    await api.post('/bookings', {
      movie_id: movie.value.id,
      seats_booked: seats
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
