<template>
  <div class="space-y-4">
    <div class="space-y-2">
      <div class="flex justify-between items-center mb-2">
        <label class="text-sm font-bold text-gray-900">Select Your Seats</label>
        <span class="text-xs font-medium text-gray-600">{{ selectedSeats.length }} selected</span>
      </div>
      
      <!-- Visual Seat Map -->
      <div class="bg-gray-100 py-6 px-4 md:px-6 rounded-xl overflow-x-auto border border-gray-200 shadow-inner overflow-y-hidden w-full max-w-full">
        <!-- Screen shape -->
        <div class="w-full max-w-sm mx-auto h-8 bg-gray-300 rounded-t-3xl mb-10 shadow-[0_4px_10px_rgba(0,0,0,0.1)] flex justify-center items-center">
            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Screen</span>
        </div>
        
        <div class="flex flex-col gap-2 sm:gap-3 min-w-max pb-4 justify-center items-center">
          <div v-for="(row, rIndex) in seatGrid" :key="row.label" class="flex items-center gap-2 sm:gap-3">
            <span class="w-4 sm:w-6 text-center text-[10px] sm:text-xs font-bold text-gray-500">{{ row.label }}</span>
            <div class="flex gap-1 sm:gap-2">
              <button 
                v-for="(seat, cIndex) in row.seats" 
                :key="seat.id"
                @click="toggleSeat(seat.id)"
                :disabled="seat.unavailable"
                class="w-7 h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 rounded-t-lg rounded-b-sm flex items-center justify-center text-[9px] sm:text-[10px] md:text-xs font-bold transition-all relative group flex-shrink-0"
                :class="{
                  'bg-gray-300 text-gray-400 cursor-not-allowed': seat.unavailable,
                  'bg-yellow-500 text-black shadow-md scale-110': !seat.unavailable && selectedSeats.includes(seat.id),
                  'bg-white text-gray-700 border border-gray-300 hover:border-yellow-500 hover:text-yellow-600 cursor-pointer': !seat.unavailable && !selectedSeats.includes(seat.id)
                }"
              >
                {{ cIndex + 1 }}
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Legend -->
      <div class="flex items-center justify-center gap-6 mt-4 pb-4">
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-white border border-gray-300 rounded-t-sm"></div>
          <span class="text-xs text-gray-600">Available</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-yellow-500 rounded-t-sm"></div>
          <span class="text-xs text-gray-600">Selected</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-gray-300 rounded-t-sm"></div>
          <span class="text-xs text-gray-600">Booked</span>
        </div>
      </div>
    </div>

    <!-- Submit Area -->
    <div class="flex justify-between items-center border-t border-gray-200 pt-4 mt-2">
      <div class="text-sm border flex items-center px-3 py-1 bg-yellow-50 rounded-lg text-yellow-800 font-medium">
        Seats: <span class="font-bold ml-1">{{ selectedSeats.length ? selectedSeats.join(', ') : 'None' }}</span>
      </div>
      <button 
        @click="handleSubmit" 
        :disabled="loading || selectedSeats.length === 0"
        class="btn-primary h-[42px] px-8"
      >
        {{ loading ? 'Booking...' : 'Book Selected' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  movieId: {
    type: Number,
    required: true
  },
  availableSeats: {
    type: Number,
    required: true
  },
  totalSeats: {
    type: Number,
    required: true
  },
  bookedSeats: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['book'])
const selectedSeats = ref([])

// Generate a grid. Let's make it 10 columns wide maximum.
const seatGrid = computed(() => {
    const grid = [];
    const maxCols = 10;
    const total = props.totalSeats;
    let seatCount = 0;
    
    // Row labels A, B, C...
    const getRowLabel = (index) => String.fromCharCode(65 + index);

    let rIndex = 0;
    while (seatCount < total) {
        const rowSeats = [];
        const colsInThisRow = Math.min(maxCols, total - seatCount);
        
        for (let c = 0; c < colsInThisRow; c++) {
            const seatId = `${getRowLabel(rIndex)}${c + 1}`;
            rowSeats.push({
                id: seatId,
                unavailable: props.bookedSeats.includes(seatId)
            });
            seatCount++;
        }
        
        grid.push({
            label: getRowLabel(rIndex),
            seats: rowSeats
        });
        rIndex++;
    }
    
    return grid;
});

const toggleSeat = (id) => {
    const index = selectedSeats.value.indexOf(id);
    if (index === -1) {
        selectedSeats.value.push(id);
    } else {
        selectedSeats.value.splice(index, 1);
    }
}

const handleSubmit = () => {
  if (selectedSeats.value.length < 1) return
  emit('book', selectedSeats.value)
}
</script>
