<template>
  <div class="container">
    <div class="mb-10">
      <h1 class="text-3xl font-bold text-white mb-1">My Bookings</h1>
      <p class="text-gray-500">Your reservations and ticket history</p>
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="animate-spin h-8 w-8 border-2 border-blue-500 border-t-transparent rounded-full"></div>
    </div>

    <div v-else-if="bookings.length === 0" class="card text-center py-12 border-gray-800">
      <p class="text-gray-500 mb-6 font-medium">No bookings found yet.</p>
      <router-link to="/movies" class="btn-primary">Explore Movies</router-link>
    </div>

    <div v-else class="grid gap-4">
      <div v-for="booking in bookings" :key="booking.id" class="card flex flex-col md:flex-row md:items-center justify-between border-gray-800">
        <div>
          <h2 class="text-xl font-bold text-white mb-1">{{ booking.movie.title }}</h2>
          <div class="flex flex-wrap items-center text-sm text-gray-500 gap-y-1 gap-x-4">
            <span class="flex items-center whitespace-nowrap">🕒 {{ booking.movie.show_time }}</span>
            <span class="flex items-center whitespace-nowrap">🪑 {{ booking.seats }} Seats Booked</span>
            <span class="text-green-500 font-bold uppercase text-xs tracking-widest">Confirmed</span>
          </div>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
          <button class="btn-secondary text-sm">View Ticket</button>
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

onMounted(loadBookings)
</script>