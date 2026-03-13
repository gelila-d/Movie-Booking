<template>
  <div class="space-y-4">
    <div class="flex items-end space-x-4">
      <div class="flex-grow space-y-1">
        <label class="text-xs font-medium text-gray-700">Number of Seats</label>
        <input 
          v-model="seats" 
          @input="seats = Math.abs($event.target.value) || 1"
          type="number" 
          min="1" 
          :max="availableSeats" 
          class="w-full"
        />
      </div>
      <button 
        @click="handleSubmit" 
        :disabled="loading || availableSeats < 1"
        class="btn-primary h-[42px] px-8"
      >
        {{ loading ? 'Booking...' : (availableSeats < 1 ? 'Sold Out' : 'Book Now') }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  movieId: {
    type: Number,
    required: true
  },
  availableSeats: {
    type: Number,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['book'])
const seats = ref(1)

const handleSubmit = () => {
  if (seats.value < 1) return
  emit('book', seats.value)
}
</script>
