<template>
  <div class="space-y-6 font-sans">
    <!-- Header & Price Breakdown (Black Glassy) -->
    <div class="flex justify-between items-start flex-wrap gap-4 bg-black/60 backdrop-blur-2xl p-5 rounded-2xl border border-white/15 shadow-[0_12px_40px_rgba(0,0,0,0.8),inset_0_1px_1px_rgba(255,255,255,0.12)]">
      <div>
        <h3 class="text-orange-500 font-bold font-sans tracking-wider text-sm">SELECT YOUR SEATS & TICKET TYPES</h3>
        <p class="text-slate-400 text-xs font-sans mt-1">{{ selectedSeats.length }} seat{{ selectedSeats.length !== 1 ? 's' : '' }} selected</p>
      </div>
      <div class="text-right">
        <span class="text-xs text-slate-400 font-sans block uppercase">Total Amount</span>
        <span class="text-xl font-bold text-emerald-400 font-sans">{{ totalPrice }} Birr</span>
      </div>
    </div>
    
    <!-- Casual Cinematic Seat Map -->
    <div class="relative">
      <!-- Outer container -->
      <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-red-900/20 via-orange-900/20 to-red-900/20 p-[1px]">
        <div class="h-full w-full rounded-2xl bg-black/60 backdrop-blur-xl"></div>
      </div>
      
      <div class="relative bg-black/60 py-8 px-6 rounded-2xl border border-white/15 backdrop-blur-xl overflow-x-auto">
        <!-- Cinema Screen -->
        <div class="w-full max-w-md mx-auto mb-10 relative">
          <div class="h-9 bg-gradient-to-r from-red-900/30 via-orange-500/30 to-red-900/30 rounded-t-3xl border-t border-l border-r border-orange-500/30 flex justify-center items-center backdrop-blur-sm">
            <span class="text-xs font-bold text-orange-300 uppercase tracking-widest font-cinematic">── MOVIE SCREEN ──</span>
          </div>
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-orange-500/20 to-transparent blur-sm rounded-t-3xl"></div>
        </div>
        
        <!-- Seat Grid -->
        <div class="flex flex-col gap-3 min-w-max pb-4 justify-center items-center">
          <div v-for="(row, rIndex) in seatGrid" :key="row.label" class="flex items-center gap-4">
            <!-- Row label & VIP indicator -->
            <div class="w-12 text-center flex items-center justify-center gap-1 font-sans">
              <span class="text-xs font-bold text-orange-400">{{ row.label }}</span>
              <span v-if="row.isVip" class="text-[10px] text-orange-300 font-bold bg-orange-950/80 px-1 rounded border border-orange-500/30">VIP</span>
            </div>
            
            <!-- Seats in row -->
            <div class="flex gap-2">
              <button 
                v-for="(seat, cIndex) in row.seats" 
                :key="seat.id"
                @click="toggleSeat(seat)"
                :disabled="seat.unavailable"
                class="seat-button w-10 h-10 rounded-lg flex items-center justify-center text-xs font-bold transition-all duration-300 relative group flex-shrink-0 border"
                :class="{
                  'bg-red-950/40 border-red-900/40 text-red-500/50 cursor-not-allowed opacity-50 line-through': seat.unavailable,
                  'bg-gradient-to-br from-orange-500 to-orange-600 border-orange-400 text-white shadow-lg shadow-orange-950/40 scale-105': !seat.unavailable && isSelected(seat.id),
                  'bg-orange-950/50 border-orange-500/50 text-orange-200 hover:border-orange-400 hover:bg-orange-900/50 cursor-pointer hover:scale-105': !seat.unavailable && !isSelected(seat.id) && seat.isVip,
                  'bg-slate-900/70 border-slate-700 text-slate-300 hover:border-orange-500/60 hover:bg-orange-950/40 hover:text-white cursor-pointer hover:scale-105': !seat.unavailable && !isSelected(seat.id) && !seat.isVip
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
    <div class="flex items-center justify-center gap-6 py-3 flex-wrap text-xs font-sans">
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-slate-900/70 border border-slate-700 rounded"></div>
        <span class="text-slate-300 font-medium">REGULAR ({{ price }} Birr)</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-orange-950/50 border border-orange-500/50 rounded"></div>
        <span class="text-orange-300 font-medium">VIP ({{ vipPrice }} Birr)</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-gradient-to-br from-orange-500 to-orange-600 border border-orange-400 rounded"></div>
        <span class="text-orange-400 font-medium">SELECTED</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="w-4 h-4 bg-red-950/40 border border-red-900/40 rounded opacity-50"></div>
        <span class="text-red-400/70 font-medium">BOOKED</span>
      </div>
    </div>

    <!-- Ticket Category Selection for Selected Seats (Black Glassy) -->
    <div v-if="selectedSeatDetails.length > 0" class="bg-black/60 backdrop-blur-2xl border border-white/15 p-5 rounded-2xl space-y-3 shadow-[0_12px_40px_rgba(0,0,0,0.8),inset_0_1px_1px_rgba(255,255,255,0.12)]">
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

    <!-- Submit Area & Payment Trigger -->
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
      
      <!-- Trigger Payment Modal Button -->
      <button 
        @click="openPaymentModal" 
        :disabled="loading || selectedSeatDetails.length === 0"
        class="btn-primary px-8 py-3 text-sm font-bold tracking-wider relative overflow-hidden group/btn min-w-[200px] w-full sm:w-auto"
      >
        <span class="relative z-10">
          PROCEED TO PAYMENT ({{ totalPrice }} BIRR)
        </span>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-700"></div>
      </button>
    </div>

    <!-- MOCK ETHIOPIAN PAYMENT MODAL -->
    <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md overflow-y-auto">
      <div class="bg-slate-900 border border-slate-700 w-full max-w-lg my-auto max-h-[90vh] overflow-y-auto rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl relative">
        <!-- Close button -->
        <button @click="showPaymentModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-white font-bold text-lg">✕</button>

        <div class="border-b border-white/10 pb-4">
          <h3 class="text-xl font-bold text-white font-orbitron flex items-center gap-2">
            💳 ETHIOPIAN PAYMENT GATEWAY
          </h3>
          <p class="text-xs text-slate-400 font-mono mt-1">Select payment method to complete booking of {{ selectedSeatDetails.length }} tickets</p>
        </div>

        <!-- Order Summary Pill -->
        <div class="p-4 bg-black/50 border border-emerald-500/30 rounded-2xl flex justify-between items-center">
          <div>
            <span class="text-xs text-slate-400 font-mono uppercase block">Total Due</span>
            <span class="text-xs font-bold text-slate-200">Seats: {{ selectedSeats.join(', ') }}</span>
          </div>
          <span class="text-2xl font-bold font-mono text-emerald-400">{{ totalPrice }} Birr</span>
        </div>

        <!-- Payment Method Selection Tabs -->
        <div class="space-y-3">
          <label class="text-xs font-bold uppercase tracking-wider text-slate-300 font-mono">Select Payment Provider:</label>
          
          <div class="grid grid-cols-2 gap-3">
            <!-- Telebirr -->
            <button 
              @click="selectedProvider = 'telebirr'" 
              class="p-3.5 rounded-xl border flex flex-col items-center justify-center gap-1.5 transition-all"
              :class="selectedProvider === 'telebirr' ? 'bg-cyan-950/60 border-cyan-400 text-cyan-300 ring-2 ring-cyan-400/30' : 'bg-slate-800/60 border-slate-700 text-slate-300 hover:border-slate-500'"
            >
              <span class="text-xl">📱</span>
              <span class="text-xs font-bold font-mono">Telebirr</span>
              <span class="text-[10px] text-cyan-400/80 font-mono">Ethio Telecom</span>
            </button>

            <!-- CBE Birr -->
            <button 
              @click="selectedProvider = 'cbe_birr'" 
              class="p-3.5 rounded-xl border flex flex-col items-center justify-center gap-1.5 transition-all"
              :class="selectedProvider === 'cbe_birr' ? 'bg-purple-950/60 border-purple-400 text-purple-300 ring-2 ring-purple-400/30' : 'bg-slate-800/60 border-slate-700 text-slate-300 hover:border-slate-500'"
            >
              <span class="text-xl">🏦</span>
              <span class="text-xs font-bold font-mono">CBE Birr</span>
              <span class="text-[10px] text-purple-400/80 font-mono">Comm. Bank of Eth.</span>
            </button>

            <!-- Chapa -->
            <button 
              @click="selectedProvider = 'chapa'" 
              class="p-3.5 rounded-xl border flex flex-col items-center justify-center gap-1.5 transition-all"
              :class="selectedProvider === 'chapa' ? 'bg-green-950/60 border-green-400 text-green-300 ring-2 ring-green-400/30' : 'bg-slate-800/60 border-slate-700 text-slate-300 hover:border-slate-500'"
            >
              <span class="text-xl">💳</span>
              <span class="text-xs font-bold font-mono">Chapa</span>
              <span class="text-[10px] text-green-400/80 font-mono">Card / Gateway</span>
            </button>

            <!-- Bank of Abyssinia -->
            <button 
              @click="selectedProvider = 'boa'" 
              class="p-3.5 rounded-xl border flex flex-col items-center justify-center gap-1.5 transition-all"
              :class="selectedProvider === 'boa' ? 'bg-amber-950/60 border-amber-400 text-amber-300 ring-2 ring-amber-400/30' : 'bg-slate-800/60 border-slate-700 text-slate-300 hover:border-slate-500'"
            >
              <span class="text-xl">🏛️</span>
              <span class="text-xs font-bold font-mono">Abyssinia Pay</span>
              <span class="text-[10px] text-amber-400/80 font-mono">Bank of Abyssinia</span>
            </button>
          </div>
        </div>

        <!-- Provider Specific Input Simulation -->
        <div class="space-y-3 bg-black/40 p-4 rounded-2xl border border-white/10">
          <div v-if="selectedProvider === 'telebirr' || selectedProvider === 'cbe_birr'" class="space-y-2">
            <label class="text-xs font-mono text-slate-300">Registered Phone Number (09XXXXXXXX):</label>
            <input v-model="phoneNumber" type="tel" placeholder="0911223344" class="w-full bg-slate-800 text-white border border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#ef6a26] font-mono" />
          </div>

          <div v-else-if="selectedProvider === 'chapa' || selectedProvider === 'boa'" class="space-y-2">
            <label class="text-xs font-mono text-slate-300">Account / Card Holder Email:</label>
            <input v-model="accountEmail" type="email" placeholder="customer@example.com" class="w-full bg-slate-800 text-white border border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#ef6a26] font-mono" />
          </div>

          <div class="space-y-1">
            <label class="text-xs font-mono text-slate-300">PIN / OTP Verification Code (Simulated):</label>
            <input v-model="pinCode" type="password" maxlength="6" placeholder="1234" class="w-full bg-slate-800 text-white border border-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#ef6a26] font-mono tracking-widest text-center font-bold" />
          </div>
        </div>

        <!-- Payment Processing Spinner / Button -->
        <div class="pt-2">
          <button 
            @click="processMockPayment" 
            :disabled="processingPayment"
            class="btn-primary w-full py-3.5 text-sm font-bold tracking-wider flex items-center justify-center gap-2"
          >
            <span v-if="processingPayment" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
            <span>{{ processingPayment ? `CONNECTING TO ${selectedProvider.toUpperCase()}...` : `CONFIRM & PAY ${totalPrice} BIRR` }}</span>
          </button>
        </div>
      </div>
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
const showPaymentModal = ref(false)
const selectedProvider = ref('telebirr')
const phoneNumber = ref('0911223344')
const accountEmail = ref('user@telebirr.et')
const pinCode = ref('1234')
const processingPayment = ref(false)

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
        const isVipRow = r < vipRowsThreshold;
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

const openPaymentModal = () => {
  if (selectedSeatDetails.value.length < 1) return
  if (!localStorage.getItem('token')) {
    alert('Please sign in or create an account to proceed to ticket payment.')
    window.location.href = '#/login'
    return
  }
  showPaymentModal.value = true
}

const processMockPayment = () => {
  processingPayment.value = true
  
  // Simulate network latency for payment processing
  setTimeout(() => {
    processingPayment.value = false
    showPaymentModal.value = false
    
    emit('book', {
      selectedSeats: selectedSeats.value,
      ticketDetails: selectedSeatDetails.value,
      paymentMethod: selectedProvider.value
    })
  }, 1200)
}
</script>
