<template>
  <div class="space-y-6">
    <!-- Header & Price Breakdown -->
    <div class="flex justify-between items-start flex-wrap gap-4 bg-slate-900/60 p-4 rounded-2xl border border-white/10">
      <div>
        <h3 class="text-[#ef6a26] font-bold font-mono tracking-wider">SELECT YOUR SEATS & TICKET TYPES</h3>
        <p class="text-slate-400 text-sm font-mono mt-1">{{ selectedSeats.length }} seat{{ selectedSeats.length !== 1 ? 's' : '' }} selected</p>
      </div>
      <div class="text-right">
        <span class="text-xs text-slate-400 font-mono block uppercase">Total Amount</span>
        <span class="text-2xl font-bold text-emerald-400 font-mono">{{ totalPrice }} Birr</span>
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
            <span class="text-xs font-bold text-orange-300 uppercase tracking-widest font-mono">◢ AUDITORIUM SCREEN ◣</span>
          </div>
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#ef6a26]/30 to-transparent blur-sm rounded-t-3xl"></div>
        </div>
        
        <!-- Seat Grid -->
        <div class="flex flex-col gap-3 min-w-max pb-4 justify-center items-center">
          <div v-for="(row, rIndex) in seatGrid" :key="row.label" class="flex items-center gap-4">
            <!-- Row label & VIP indicator -->
            <div class="w-12 text-center flex items-center justify-center gap-1 font-mono">
              <span class="text-xs font-bold text-[#ef6a26]">{{ row.label }}</span>
              <span v-if="row.isVip" class="text-[10px] text-purple-400 font-bold bg-purple-950/80 px-1 rounded border border-purple-500/30">VIP</span>
            </div>
            
            <!-- Seats in row -->
            <div class="flex gap-2">
              <button 
                v-for="(seat, cIndex) in row.seats" 
                :key="seat.id"
                @click="toggleSeat(seat)"
                :disabled="seat.unavailable"
                class="seat-button w-10 h-10 rounded-lg flex items-center justify-center text-xs font-bold transition-all duration-300 relative group flex-shrink-0 border-2"
                :class="{
                  'bg-red-950/30 border-red-800/40 text-red-400/50 cursor-not-allowed opacity-60 line-through': seat.unavailable,
                  'bg-gradient-to-br from-[#ef6a26] to-orange-600 border-orange-400 text-white shadow-lg shadow-[#ef6a26]/30 scale-110 animate-pulse': !seat.unavailable && isSelected(seat.id),
                  'bg-purple-950/60 border-purple-500/60 text-purple-300 hover:border-purple-400 hover:bg-purple-900/60 cursor-pointer hover:scale-105': !seat.unavailable && !isSelected(seat.id) && seat.isVip,
                  'bg-emerald-950/40 border-emerald-500/40 text-emerald-300 hover:border-[#ef6a26]/70 hover:bg-[#ef6a26]/20 hover:text-orange-200 cursor-pointer hover:scale-105': !seat.unavailable && !isSelected(seat.id) && !seat.isVip
                }"
              >
                {{ cIndex + 1 }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Legend with Birr pricing -->
    <div class="flex items-center justify-center gap-6 py-4 flex-wrap text-xs font-mono">
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-emerald-950/40 border-2 border-emerald-500/50 rounded"></div>
        <span class="text-emerald-400 font-medium">REGULAR SEAT ({{ price }} Birr)</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-purple-950/60 border-2 border-purple-500/60 rounded"></div>
        <span class="text-purple-300 font-medium">VIP SEAT ({{ vipPrice }} Birr)</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-gradient-to-br from-[#ef6a26] to-orange-600 border-2 border-orange-400 rounded"></div>
        <span class="text-orange-400 font-medium">SELECTED</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-red-950/30 border-2 border-red-800/40 rounded opacity-60"></div>
        <span class="text-red-400/70 font-medium">BOOKED</span>
      </div>
    </div>

    <!-- Ticket Category Selection for Selected Seats -->
    <div v-if="selectedSeatDetails.length > 0" class="bg-slate-900/80 border border-white/10 p-5 rounded-2xl space-y-3">
      <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider font-mono">Customize Ticket Types Per Seat:</h4>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-48 overflow-y-auto pr-1">
        <div 
          v-for="seat in selectedSeatDetails" 
          :key="seat.seat_id" 
          class="p-3 bg-black/40 rounded-xl border border-white/10 flex items-center justify-between gap-3 text-xs font-mono"
        >
          <div>
            <span class="font-bold text-[#ef6a26] text-sm">Seat {{ seat.seat_id }}</span>
            <span v-if="seat.isVip" class="ml-2 px-1.5 py-0.5 rounded text-[10px] bg-purple-950 text-purple-300 border border-purple-500/30 font-bold">VIP</span>
          </div>

          <div class="flex items-center gap-2">
            <select 
              v-model="seat.type" 
              @change="updateSeatPrice(seat)"
              class="bg-slate-800 text-white border border-slate-700 rounded-lg px-2 py-1 focus:outline-none text-xs"
            >
              <option value="Regular">Regular ({{ price }} Birr)</option>
              <option value="VIP">VIP ({{ vipPrice }} Birr)</option>
              <option value="Student">Student ({{ studentPrice }} Birr)</option>
              <option value="Child">Child ({{ childPrice }} Birr)</option>
            </select>
            <span class="font-bold text-emerald-400 w-16 text-right">{{ seat.price }} Birr</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Area -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-slate-700/50 pt-6">
      <div class="bg-slate-800/30 border border-[#ef6a26]/30 rounded-xl px-4 py-3 backdrop-blur-sm w-full sm:w-auto">
        <div class="flex items-center justify-between sm:justify-start gap-4">
          <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-[#ef6a26] rounded-full animate-pulse"></div>
            <span class="text-sm text-slate-300 font-mono">
              SEATS: <span class="font-bold text-[#ef6a26]">{{ selectedSeatDetails.length ? selectedSeatDetails.map(s => s.seat_id).join(', ') : 'NONE' }}</span>
            </span>
          </div>
        </div>
      </div>
      
      <!-- Book button -->
      <button 
        @click="handleSubmit" 
        :disabled="loading || selectedSeatDetails.length === 0"
        class="btn-primary px-8 py-3 text-sm font-bold tracking-wider relative overflow-hidden group/btn min-w-[160px] w-full sm:w-auto"
      >
        <span class="relative z-10">
          {{ loading ? 'BOOKING...' : `PAY ${totalPrice} BIRR & BOOK` }}
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
  showtimeId: {
    type: Number,
    default: null
  },
  price: {
    type: Number,
    default: 100
  },
  vipPrice: {
    type: Number,
    default: 150
  },
  studentPrice: {
    type: Number,
    default: 80
  },
  childPrice: {
    type: Number,
    default: 60
  },
  vipRowsCount: {
    type: Number,
    default: 2
  },
  availableSeats: {
    type: Number,
    required: true
  },
  totalSeats: {
    type: Number,
    required: true
  },
  rowsCount: {
    type: Number,
    default: null
  },
  seatsPerRow: {
    type: Number,
    default: null
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
const selectedSeatDetails = ref([])

const isSelected = (seatId) => {
  return selectedSeatDetails.value.some(s => s.seat_id === seatId)
}

const selectedSeats = computed(() => {
  return selectedSeatDetails.value.map(s => s.seat_id)
})

const totalPrice = computed(() => {
  return selectedSeatDetails.value.reduce((sum, s) => sum + Number(s.price || 0), 0)
})

const updateSeatPrice = (seat) => {
  if (seat.type === 'VIP') {
    seat.price = props.vipPrice
  } else if (seat.type === 'Student') {
    seat.price = props.studentPrice
  } else if (seat.type === 'Child') {
    seat.price = props.childPrice
  } else {
    seat.price = seat.isVip ? props.vipPrice : props.price
  }
}

const toggleSeat = (seat) => {
  const index = selectedSeatDetails.value.findIndex(s => s.seat_id === seat.id)
  if (index === -1) {
    const defaultType = seat.isVip ? 'VIP' : 'Regular'
    const defaultPrice = seat.isVip ? props.vipPrice : props.price
    selectedSeatDetails.value.push({
      seat_id: seat.id,
      isVip: seat.isVip,
      type: defaultType,
      price: defaultPrice
    })
  } else {
    selectedSeatDetails.value.splice(index, 1)
  }
}

// Generate a grid based on rowsCount and seatsPerRow if provided
const seatGrid = computed(() => {
    const grid = [];
    const getRowLabel = (index) => String.fromCharCode(65 + index); // A, B, C...

    const rCount = props.rowsCount || 6;
    const sCount = props.seatsPerRow || 10;
    const vipRowsThreshold = props.vipRowsCount || 2;

    for (let r = 0; r < rCount; r++) {
        const rowLabel = getRowLabel(r);
        const isVipRow = r < vipRowsThreshold; // Top rows designated as VIP
        const rowSeats = [];
        for (let c = 0; c < sCount; c++) {
            const seatId = `${rowLabel}${c + 1}`;
            rowSeats.push({
                id: seatId,
                isVip: isVipRow,
                unavailable: props.bookedSeats.includes(seatId)
            });
        }
        grid.push({
            label: rowLabel,
            isVip: isVipRow,
            seats: rowSeats
        });
    }
    
    return grid;
});

const handleSubmit = () => {
  if (selectedSeatDetails.value.length < 1) return
  emit('book', {
    selectedSeats: selectedSeats.value,
    ticketDetails: selectedSeatDetails.value
  })
}
</script>
