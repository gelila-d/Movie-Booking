<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h3 class="text-[#ef6a26] font-bold font-mono tracking-wider">SELECT YOUR SEATS</h3>
        <p class="text-slate-400 text-sm font-mono mt-1">{{ selectedSeats.length }} seat{{ selectedSeats.length !== 1 ? 's' : '' }} selected</p>
      </div>
    </div>
    
    <!-- Futuristic Seat Map -->
    <div class="relative">
      <!-- Holographic container -->
      <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-orange-500/20 via-amber-500/20 to-orange-500/20 p-[1px]">
        <div class="h-full w-full rounded-2xl bg-slate-800/40 backdrop-blur-xl"></div>
      </div>
      
      <div class="relative bg-slate-800/20 py-8 px-6 rounded-2xl border border-slate-600/30 backdrop-blur-sm overflow-x-auto">
        <!-- Futuristic Screen -->
        <div class="w-full max-w-md mx-auto mb-12 relative">
          <div class="h-10 bg-gradient-to-r from-orange-500/30 via-amber-400/50 to-orange-500/30 rounded-t-3xl border-t border-l border-r border-[#ef6a26]/40 flex justify-center items-center backdrop-blur-sm">
            <span class="text-xs font-bold text-orange-300 uppercase tracking-widest font-mono">◢ SCREEN ◣</span>
          </div>
          <!-- Screen glow effect -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#ef6a26]/30 to-transparent blur-sm rounded-t-3xl"></div>
        </div>
        
        <!-- Seat Grid -->
        <div class="flex flex-col gap-3 min-w-max pb-4 justify-center items-center">
          <div v-for="(row, rIndex) in seatGrid" :key="row.label" class="flex items-center gap-4">
            <!-- Row label -->
            <span class="w-8 text-center text-sm font-bold text-[#ef6a26] font-mono">{{ row.label }}</span>
            
            <!-- Seats in row -->
            <div class="flex gap-2">
              <button 
                v-for="(seat, cIndex) in row.seats" 
                :key="seat.id"
                @click="toggleSeat(seat.id)"
                :disabled="seat.unavailable"
                class="seat-button w-10 h-10 rounded-lg flex items-center justify-center text-xs font-bold transition-all duration-300 relative group flex-shrink-0 border-2"
                :class="{
                  'bg-red-950/30 border-red-800/40 text-red-400/50 cursor-not-allowed opacity-60 line-through': seat.unavailable,
                  'bg-gradient-to-br from-[#ef6a26] to-orange-600 border-orange-400 text-white shadow-lg shadow-[#ef6a26]/30 scale-110 animate-pulse': !seat.unavailable && selectedSeats.includes(seat.id),
                  'bg-emerald-950/40 border-emerald-500/40 text-emerald-300 hover:border-[#ef6a26]/70 hover:bg-[#ef6a26]/20 hover:text-orange-200 cursor-pointer hover:scale-105': !seat.unavailable && !selectedSeats.includes(seat.id)
                }"
              >
                {{ cIndex + 1 }}
                
                <!-- Holographic effect on hover -->
                <div v-if="!seat.unavailable && !selectedSeats.includes(seat.id)" 
                     class="absolute inset-0 bg-gradient-to-r from-transparent via-[#ef6a26]/20 to-transparent rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"></div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Legend with futuristic styling -->
    <div class="flex items-center justify-center gap-8 py-4">
      <div class="flex items-center gap-2">
        <div class="w-5 h-5 bg-emerald-950/40 border-2 border-emerald-500/50 rounded-lg"></div>
        <span class="text-sm text-emerald-400 font-mono font-medium">AVAILABLE</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-5 h-5 bg-gradient-to-br from-[#ef6a26] to-orange-600 border-2 border-orange-400 rounded-lg animate-pulse"></div>
        <span class="text-sm text-orange-400 font-mono font-medium">SELECTED</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-5 h-5 bg-red-950/30 border-2 border-red-800/40 rounded-lg opacity-60"></div>
        <span class="text-sm text-red-400/70 font-mono font-medium">BOOKED</span>
      </div>
    </div>

    <!-- Submit Area with futuristic styling -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-slate-700/50 pt-6">
      <!-- Selected seats display -->
      <div class="bg-slate-800/30 border border-[#ef6a26]/30 rounded-xl px-4 py-3 backdrop-blur-sm">
        <div class="flex items-center gap-2">
          <div class="w-2 h-2 bg-[#ef6a26] rounded-full animate-pulse"></div>
          <span class="text-sm text-slate-300 font-mono">
            SEATS: <span class="font-bold text-[#ef6a26]">{{ selectedSeats.length ? selectedSeats.join(', ') : 'NONE' }}</span>
          </span>
        </div>
      </div>
      
      <!-- Book button -->
      <button 
        @click="handleSubmit" 
        :disabled="loading || selectedSeats.length === 0"
        class="btn-primary px-8 py-3 text-sm font-bold tracking-wider relative overflow-hidden group/btn min-w-[140px]"
      >
        <span class="relative z-10">
          {{ loading ? 'BOOKING...' : 'BOOK TICKETS' }}
        </span>
        <div v-if="!loading" class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700"></div>
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
