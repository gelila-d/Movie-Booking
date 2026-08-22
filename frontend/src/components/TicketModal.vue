<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md print:bg-white print:p-0">
    <div class="relative w-full max-w-lg bg-slate-950 border border-amber-500/40 rounded-3xl overflow-hidden shadow-[0_25px_60px_-15px_rgba(239,106,38,0.3)] print:shadow-none print:border-none print:w-full print:max-w-none print:rounded-none text-white font-sans">
      
      <!-- Top Ticket Header / Film Strip Pattern -->
      <div class="bg-gradient-to-r from-orange-600 via-amber-500 to-orange-600 p-4 text-black flex justify-between items-center print:bg-slate-900 print:text-white">
        <div class="flex items-center gap-2">
          <span class="text-xl">🎟️</span>
          <span class="font-orbitron font-extrabold tracking-wider text-sm uppercase">OFFICIAL E-TICKET PASS</span>
        </div>
        <div class="flex items-center gap-2">
          <button @click="downloadOrPrintTicket" class="px-3 py-1 bg-black/20 hover:bg-black/30 text-black font-bold text-xs rounded-lg transition-colors print:hidden flex items-center gap-1">
            <span>📥</span> Download / Print
          </button>
          <button @click="close" class="text-black font-extrabold hover:opacity-75 text-lg print:hidden">
            ✕
          </button>
        </div>
      </div>

      <!-- Main Ticket Body -->
      <div class="p-6 sm:p-8 space-y-6 relative bg-gradient-to-b from-slate-900 via-slate-950 to-black">
        <!-- Watermark / Background Glow -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Movie Header -->
        <div class="flex gap-4 items-start">
          <div v-if="booking?.movie?.image" class="w-20 h-28 rounded-xl overflow-hidden bg-slate-800 flex-shrink-0 border border-white/20 shadow-md">
            <img :src="getImageUrl(booking.movie.image)" alt="Poster" class="w-full h-full object-cover" />
          </div>
          <div class="space-y-1">
            <h2 class="text-2xl font-bold font-orbitron text-white leading-tight">
              {{ booking?.movie?.title || 'Movie Title' }}
            </h2>
            <p class="text-xs text-amber-400 font-mono font-semibold">
              🏛️ {{ cinemaHallName }}
            </p>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 mt-1">
              ✓ {{ bookingStatusText }}
            </div>
          </div>
        </div>

        <!-- Perforated Ticket Divider -->
        <div class="relative my-4 flex items-center justify-between">
          <div class="w-5 h-5 bg-black rounded-full -ml-8 border-r border-amber-500/30"></div>
          <div class="flex-grow border-t-2 border-dashed border-slate-700 mx-2"></div>
          <div class="w-5 h-5 bg-black rounded-full -mr-8 border-l border-amber-500/30"></div>
        </div>

        <!-- Ticket Details Grid -->
        <div class="grid grid-cols-2 gap-4 text-xs font-mono bg-slate-900/80 p-4 rounded-2xl border border-white/10">
          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">DATE & TIME</span>
            <span class="text-white font-bold text-sm block mt-0.5">
              {{ formattedDate }}
            </span>
            <span class="text-orange-400 font-bold block">
              {{ formattedTime }}
            </span>
          </div>

          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">SEAT ASSIGNMENTS</span>
            <span class="text-emerald-400 font-extrabold text-sm block mt-0.5">
              {{ seatList }}
            </span>
            <span class="text-slate-400 text-[10px] block">
              ({{ booking?.seats_booked || 0 }} Seat{{ booking?.seats_booked !== 1 ? 's' : '' }})
            </span>
          </div>

          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">BOOKING ID</span>
            <span class="text-amber-300 font-extrabold text-sm block mt-0.5">
              {{ bookingId }}
            </span>
          </div>

          <div>
            <span class="text-slate-400 block text-[10px] uppercase font-bold">TOTAL AMOUNT</span>
            <span class="text-emerald-400 font-extrabold text-sm block mt-0.5">
              {{ formattedAmount }} ETB
            </span>
          </div>
        </div>

        <!-- QR Code & Scanner Instructions Section -->
        <div class="bg-black p-4 rounded-2xl border border-amber-500/30 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="space-y-1 text-center sm:text-left">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 font-mono block">ENTRANCE SCANNABLE PASS</span>
            <p class="text-xs text-slate-300 font-mono">Present this QR code to cinema usher at entry.</p>
            <span class="text-[10px] text-slate-500 font-mono block">Ref: {{ booking?.transaction_ref || bookingId }}</span>
          </div>

          <!-- Dynamic Real QR Code -->
          <div class="bg-white p-2 rounded-xl shadow-lg flex-shrink-0">
            <img :src="qrCodeUrl" alt="Ticket QR Code" class="w-32 h-32 object-contain" />
          </div>
        </div>
      </div>

      <!-- Ticket Stub Footer -->
      <div class="bg-slate-900 border-t border-slate-800 p-4 text-center text-[10px] text-slate-400 font-mono flex justify-between items-center print:hidden">
        <span>movies • Ethiopian Cinema Pass</span>
        <button @click="downloadOrPrintTicket" class="text-amber-300 font-bold hover:underline flex items-center gap-1">
          <span>📥</span> Download Ticket
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  booking: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}

const downloadOrPrintTicket = () => {
  window.print()
}

const getImageUrl = (path) => {
  if (!path) return '';
  return `http://localhost:8000/storage/${path}`;
}

const bookingId = computed(() => {
  if (!props.booking) return 'MV-82931';
  if (props.booking.transaction_ref) return props.booking.transaction_ref;
  return `MV-${String(props.booking.id).padStart(5, '0')}`;
})

const bookingStatusText = computed(() => {
  if (!props.booking) return 'CONFIRMED & PAID';
  const dStr = props.booking.showtime?.start_time || props.booking.movie?.show_time;
  if (dStr && new Date(dStr) < new Date()) {
    return 'PAID (PAST SHOW)';
  }
  return 'CONFIRMED & PAID';
})

const cinemaHallName = computed(() => {
  if (!props.booking) return 'Cinema Hall 2';
  const detail = props.booking.showtime?.auditoriumDetail;
  if (detail?.cinema?.name && detail?.name) {
    return `${detail.cinema.name} — ${detail.name}`;
  }
  return props.booking.showtime?.auditorium || 'Cinema Hall 2';
})

const formattedDate = computed(() => {
  const dStr = props.booking?.showtime?.start_time || props.booking?.movie?.show_time;
  if (!dStr) return 'August 25, 2026';
  return new Date(dStr).toLocaleDateString(undefined, {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
})

const formattedTime = computed(() => {
  const dStr = props.booking?.showtime?.start_time || props.booking?.movie?.show_time;
  if (!dStr) return '7:30 PM';
  return new Date(dStr).toLocaleTimeString(undefined, {
    hour: 'numeric',
    minute: '2-digit'
  });
})

const seatList = computed(() => {
  if (props.booking?.seat_numbers && props.booking.seat_numbers.length) {
    return props.booking.seat_numbers.join(', ');
  }
  return 'B5, B6';
})

const formattedAmount = computed(() => {
  if (!props.booking?.total_price) return '1,000';
  return Number(props.booking.total_price).toLocaleString();
})

const qrCodeUrl = computed(() => {
  const dataString = encodeURIComponent(
    `TICKET:${bookingId.value}|MOVIE:${props.booking?.movie?.title || 'Movie'}|HALL:${cinemaHallName.value}|SEATS:${seatList.value}|TOTAL:${formattedAmount.value}ETB`
  );
  return `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${dataString}&color=000000&bgcolor=ffffff`;
})
</script>
