<template>
  <div class="container mx-auto px-4 py-8 max-w-2xl">
    <div v-if="loading" class="text-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
    </div>

    <div v-else-if="movie" class="card space-y-6 border-gray-800">
      <router-link to="/movies" class="text-blue-500 hover:underline text-sm font-medium">
        &larr; Back to Movies
      </router-link>
      
      <div>
        <h1 class="text-3xl font-bold text-white mb-2">{{ movie.title }}</h1>
        <p class="text-gray-400 leading-relaxed">{{ movie.description }}</p>
      </div>

      <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-700">
        <div>
          <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Show Time</p>
          <p class="text-white font-medium">{{ movie.show_time }}</p>
        </div>
        <div>
          <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Available Seats</p>
          <p class="text-white font-medium">{{ movie.available_seats }} / {{ movie.total_seats }}</p>
        </div>
      </div>

      <div class="pt-4">
        <h2 class="text-xl font-bold text-white mb-4">Book Your Tickets</h2>
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
      seats: seats
    })
    alert('Tickets booked successfully!')
    router.push('/my-bookings')
  } catch (err) {
    alert(err.response?.data?.message || 'Booking failed')
  } finally {
    booking.value = false
  }
}

onMounted(fetchMovie)
</script>
