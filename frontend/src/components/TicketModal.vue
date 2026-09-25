<template>
  <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4 bg-black/90 backdrop-blur-md overflow-y-auto print:bg-white print:p-0">
    <div class="relative w-full max-w-lg my-auto bg-slate-950 border border-orange-500/40 rounded-2xl sm:rounded-3xl shadow-[0_25px_60px_-15px_rgba(239,106,38,0.3)] max-h-[92vh] overflow-y-auto print:max-h-none print:overflow-visible print:shadow-none print:border-none print:w-full print:max-w-none print:rounded-none text-white font-sans">
      
      <!-- Top Ticket Header / Film Strip Pattern -->
      <div class="bg-gradient-to-r from-orange-600 via-orange-500 to-orange-600 p-3 sm:p-4 text-black flex justify-between items-center print:bg-slate-900 print:text-white sticky top-0 z-30">
        <div class="flex items-center gap-1.5 sm:gap-2">
          <span class="text-base sm:text-xl">🎟️</span>
          <span class="font-cinematic font-extrabold tracking-wider text-xs sm:text-sm uppercase">OFFICIAL E-TICKET PASS</span>
        </div>
        <div class="flex items-center gap-1.5 sm:gap-2">
          <button @click="downloadTicketImage" :disabled="downloading" class="px-2.5 sm:px-3 py-1 bg-black/20 hover:bg-black/30 text-black font-bold text-[11px] sm:text-xs rounded-lg transition-colors print:hidden flex items-center gap-1 cursor-pointer">
            <span v-if="downloading" class="animate-spin h-3.5 w-3.5 border-2 border-black border-t-transparent rounded-full"></span>
            <span>📥</span> {{ downloading ? 'Saving...' : 'Download PNG' }}
          </button>
          <button @click="printTicket" class="px-2.5 sm:px-3 py-1 bg-black/20 hover:bg-black/30 text-black font-bold text-[11px] sm:text-xs rounded-lg transition-colors print:hidden flex items-center gap-1 cursor-pointer">
            <span>🖨️</span> Print
          </button>
          <button @click="close" class="text-black font-extrabold hover:opacity-75 text-lg print:hidden p-1 cursor-pointer">
            ✕
          </button>
        </div>
      </div>

      <!-- Main Ticket Body (Captured for Download & Screen Display) -->
      <div ref="ticketPassRef" class="p-4 sm:p-8 space-y-4 sm:space-y-6 relative bg-gradient-to-b from-slate-900 via-slate-950 to-black">
        <!-- Watermark / Background Glow -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Movie Header -->
        <div class="flex gap-3 sm:gap-4 items-start">
          <div v-if="posterSrc" class="w-16 sm:w-20 h-22 sm:h-28 rounded-xl overflow-hidden bg-slate-800 flex-shrink-0 border border-white/20 shadow-md">
            <img :src="posterSrc" alt="Poster" class="w-full h-full object-cover" />
          </div>
          <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-bold font-cinematic text-white leading-tight">
              {{ movieTitle }}
            </h2>
            <p class="text-xs text-orange-400 font-sans font-semibold">
              🏛️ {{ cinemaHallName }}
            </p>
            <div class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold font-sans uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 mt-1">
              ✓ {{ bookingStatusText }}
            </div>
          </div>
        </div>

        <!-- Perforated Ticket Divider -->
        <div class="relative my-3 sm:my-4 flex items-center justify-between">
          <div class="w-4 sm:w-5 h-4 sm:h-5 bg-black rounded-full -ml-6 sm:-ml-8 border-r border-orange-500/30"></div>
          <div class="flex-grow border-t-2 border-dashed border-slate-700 mx-2"></div>
          <div class="w-4 sm:w-5 h-4 sm:h-5 bg-black rounded-full -mr-6 sm:-mr-8 border-l border-orange-500/30"></div>
        </div>

        <!-- Ticket Details Grid (Black Glassy) -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs font-sans bg-black/60 backdrop-blur-2xl p-3.5 sm:p-5 rounded-2xl border border-white/15 shadow-[0_12px_40px_rgba(0,0,0,0.8),inset_0_1px_1px_rgba(255,255,255,0.12)]">
          <div>
            <span class="text-slate-400 block text-[9px] sm:text-[10px] uppercase font-bold">DATE & TIME</span>
            <span class="text-white font-bold text-xs sm:text-sm block mt-0.5">
              {{ formattedDate }}
            </span>
            <span class="text-orange-400 font-bold block text-xs sm:text-sm">
              {{ formattedTime }}
            </span>
          </div>

          <div>
            <span class="text-slate-400 block text-[9px] sm:text-[10px] uppercase font-bold">SEAT ASSIGNMENTS</span>
            <span class="text-emerald-400 font-extrabold text-xs sm:text-sm block mt-0.5 truncate">
              {{ seatList }}
            </span>
            <span class="text-slate-400 text-[10px] block">
              ({{ booking?.seats_booked || 0 }} Seat{{ booking?.seats_booked !== 1 ? 's' : '' }})
            </span>
          </div>

          <div>
            <span class="text-slate-400 block text-[9px] sm:text-[10px] uppercase font-bold">BOOKING ID</span>
            <span class="text-orange-300 font-extrabold text-xs sm:text-sm block mt-0.5">
              {{ bookingId }}
            </span>
          </div>

          <div>
            <span class="text-slate-400 block text-[9px] sm:text-[10px] uppercase font-bold">TOTAL AMOUNT</span>
            <span class="text-emerald-400 font-extrabold text-xs sm:text-sm block mt-0.5">
              {{ formattedAmount }} ETB
            </span>
          </div>
        </div>

        <!-- QR Code & Scanner Instructions Section -->
        <div class="bg-black p-3 sm:p-4 rounded-2xl border border-white/20 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
          <div class="space-y-1 text-center sm:text-left">
            <span class="text-[10px] font-bold uppercase tracking-wider text-white font-sans block">ENTRANCE SCANNABLE PASS</span>
            <p class="text-xs text-slate-300 font-sans">Present this QR code to cinema usher at entry.</p>
            <span class="text-[10px] text-slate-500 font-sans block">Ref: {{ booking?.transaction_ref || bookingId }}</span>
          </div>

          <!-- Dynamic Real QR Code -->
          <div class="bg-white p-2 rounded-xl shadow-lg flex-shrink-0">
            <img :src="localQrCodeUrl" alt="Ticket QR Code" class="w-24 h-24 sm:w-32 sm:h-32 object-contain" />
          </div>
        </div>
      </div>

      <!-- Ticket Stub Footer -->
      <div class="bg-slate-900 border-t border-slate-800 p-4 text-center text-[10px] text-slate-400 font-sans flex justify-between items-center print:hidden">
        <span>movies • Ethiopian Cinema Pass</span>
        <div class="flex items-center gap-3">
          <button @click="downloadTicketImage" :disabled="downloading" class="text-orange-300 font-bold hover:underline flex items-center gap-1 cursor-pointer">
            <span>📥</span> {{ downloading ? 'Saving Image...' : 'Download Ticket PNG' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, watchEffect } from 'vue'
import html2canvas from 'html2canvas'
import QRCode from 'qrcode'

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

const ticketPassRef = ref(null)
const downloading = ref(false)
const localQrCodeUrl = ref('')
const posterSrc = ref('')

const close = () => {
  emit('close')
}

const printTicket = () => {
  window.print()
}

const movieObj = computed(() => {
  return props.booking?.movie || props.booking?.showtime?.movie || null;
})

const movieTitle = computed(() => {
  return movieObj.value?.title || 'Movie Title';
})

const movieImage = computed(() => {
  return movieObj.value?.image || '';
})

const bookingId = computed(() => {
  if (!props.booking) return 'MV-82931';
  if (props.booking.transaction_ref) return props.booking.transaction_ref;
  return `MV-${String(props.booking.id).padStart(5, '0')}`;
})

const bookingStatusText = computed(() => {
  if (!props.booking) return 'CONFIRMED & PAID';
  const dStr = props.booking.showtime?.start_time || movieObj.value?.show_time;
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
  const dStr = props.booking?.showtime?.start_time || movieObj.value?.show_time;
  if (!dStr) return 'August 25, 2026';
  return new Date(dStr).toLocaleDateString(undefined, {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
})

const formattedTime = computed(() => {
  const dStr = props.booking?.showtime?.start_time || movieObj.value?.show_time;
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

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('data:')) {
    return path;
  }
  const cleanPath = path.startsWith('/') ? path.substring(1) : path;
  return `http://localhost:8000/storage/${cleanPath}`;
}

// Convert poster to base64 data URL to eliminate cross-origin issues during html2canvas capture
watch(movieImage, async (newImg) => {
  if (!newImg) {
    posterSrc.value = '';
    return;
  }
  const fullUrl = getImageUrl(newImg);
  posterSrc.value = fullUrl;

  try {
    const res = await fetch(fullUrl, { mode: 'cors' });
    const blob = await res.blob();
    const reader = new FileReader();
    reader.onloadend = () => {
      if (reader.result) {
        posterSrc.value = reader.result;
      }
    };
    reader.readAsDataURL(blob);
  } catch (e) {
    // Keep raw URL if fetch fails
  }
}, { immediate: true })

// Generate pure local base64 QR Data URL
watchEffect(async () => {
  const payload = `TICKET:${bookingId.value}|MOVIE:${movieTitle.value}|HALL:${cinemaHallName.value}|SEATS:${seatList.value}|TOTAL:${formattedAmount.value}ETB`;
  try {
    localQrCodeUrl.value = await QRCode.toDataURL(payload, { width: 180, margin: 1, color: { dark: '#000000', light: '#ffffff' } });
  } catch (err) {
    console.error('Failed to generate local QR code base64:', err);
  }
})

// Native 2D Canvas Ticket Generator (Fast, HD 2x resolution, zero CORS/DOM bugs)
const generateTicketCanvas = async () => {
  const canvas = document.createElement('canvas');
  const scale = 2; // 2x HD resolution
  const width = 520;
  const height = 750;
  canvas.width = width * scale;
  canvas.height = height * scale;
  const ctx = canvas.getContext('2d');
  ctx.scale(scale, scale);

  // 1. Background Gradient
  const bgGrad = ctx.createLinearGradient(0, 0, 0, height);
  bgGrad.addColorStop(0, '#0f172a');
  bgGrad.addColorStop(0.5, '#090d16');
  bgGrad.addColorStop(1, '#020617');
  ctx.fillStyle = bgGrad;
  ctx.fillRect(0, 0, width, height);

  // Outer Border
  ctx.strokeStyle = 'rgba(249, 115, 22, 0.4)';
  ctx.lineWidth = 2;
  ctx.strokeRect(1, 1, width - 2, height - 2);

  // 2. Header Banner
  const headerGrad = ctx.createLinearGradient(0, 0, width, 0);
  headerGrad.addColorStop(0, '#ea580c');
  headerGrad.addColorStop(0.5, '#f97316');
  headerGrad.addColorStop(1, '#ea580c');
  ctx.fillStyle = headerGrad;
  ctx.fillRect(0, 0, width, 56);

  ctx.fillStyle = '#000000';
  ctx.font = 'bold 15px sans-serif';
  ctx.fillText('🎟️  OFFICIAL E-TICKET PASS', 20, 34);

  ctx.font = 'bold 12px sans-serif';
  ctx.textAlign = 'right';
  ctx.fillText('CONFIRMED', width - 20, 34);
  ctx.textAlign = 'left';

  // 3. Movie Poster & Title Section
  let startY = 85;
  if (posterSrc.value) {
    try {
      const img = new Image();
      await new Promise((resolve) => {
        img.onload = resolve;
        img.onerror = resolve;
        img.src = posterSrc.value;
      });
      if (img.complete && img.naturalWidth > 0) {
        ctx.drawImage(img, 24, startY, 80, 110);
      }
    } catch (e) {}
  }

  // Movie Title
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 20px sans-serif';
  ctx.fillText(movieTitle.value, 120, startY + 24);

  // Cinema Hall
  ctx.fillStyle = '#f97316';
  ctx.font = 'bold 13px sans-serif';
  ctx.fillText(`🏛️ ${cinemaHallName.value}`, 120, startY + 50);

  // Status Badge
  ctx.fillStyle = 'rgba(34, 197, 94, 0.2)';
  ctx.fillRect(120, startY + 65, 150, 24);
  ctx.strokeStyle = '#22c55e';
  ctx.lineWidth = 1;
  ctx.strokeRect(120, startY + 65, 150, 24);
  ctx.fillStyle = '#4ade80';
  ctx.font = 'bold 11px sans-serif';
  ctx.fillText(`✓ ${bookingStatusText.value}`, 130, startY + 81);

  // 4. Perforated Line
  const perfY = 225;
  ctx.strokeStyle = '#334155';
  ctx.lineWidth = 2;
  ctx.setLineDash([8, 6]);
  ctx.beginPath();
  ctx.moveTo(30, perfY);
  ctx.lineTo(width - 30, perfY);
  ctx.stroke();
  ctx.setLineDash([]);

  // Side Cutouts (Circles)
  ctx.fillStyle = '#050505';
  ctx.beginPath();
  ctx.arc(0, perfY, 14, 0, Math.PI * 2);
  ctx.fill();
  ctx.beginPath();
  ctx.arc(width, perfY, 14, 0, Math.PI * 2);
  ctx.fill();

  // 5. Details Card Box
  const cardY = 255;
  ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
  ctx.fillRect(24, cardY, width - 48, 220);
  ctx.strokeStyle = 'rgba(255, 255, 255, 0.15)';
  ctx.lineWidth = 1;
  ctx.strokeRect(24, cardY, width - 48, 220);

  // Grid Info: Date & Time | Seats
  ctx.fillStyle = '#94a3b8';
  ctx.font = 'bold 10px sans-serif';
  ctx.fillText('DATE & TIME', 44, cardY + 30);
  ctx.fillText('SEAT ASSIGNMENTS', 270, cardY + 30);

  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 14px sans-serif';
  ctx.fillText(formattedDate.value, 44, cardY + 52);
  ctx.fillStyle = '#f97316';
  ctx.fillText(formattedTime.value, 44, cardY + 72);

  ctx.fillStyle = '#4ade80';
  ctx.font = 'bold 14px sans-serif';
  ctx.fillText(seatList.value, 270, cardY + 52);
  ctx.fillStyle = '#94a3b8';
  ctx.font = '11px sans-serif';
  ctx.fillText(`(${props.booking?.seats_booked || 0} Seats)`, 270, cardY + 72);

  // Grid Info: Booking ID | Total Amount
  ctx.fillStyle = '#94a3b8';
  ctx.font = 'bold 10px sans-serif';
  ctx.fillText('BOOKING ID', 44, cardY + 120);
  ctx.fillText('TOTAL AMOUNT', 270, cardY + 120);

  ctx.fillStyle = '#fdba74';
  ctx.font = 'bold 15px sans-serif';
  ctx.fillText(bookingId.value, 44, cardY + 144);

  ctx.fillStyle = '#4ade80';
  ctx.font = 'bold 15px sans-serif';
  ctx.fillText(`${formattedAmount.value} ETB`, 270, cardY + 144);

  // 6. QR Code Section Box
  const qrY = 500;
  ctx.fillStyle = '#000000';
  ctx.fillRect(24, qrY, width - 48, 180);
  ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)';
  ctx.strokeRect(24, qrY, width - 48, 180);

  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 12px sans-serif';
  ctx.fillText('ENTRANCE SCANNABLE PASS', 44, qrY + 35);
  ctx.fillStyle = '#94a3b8';
  ctx.font = '11px sans-serif';
  ctx.fillText('Present this QR code to cinema usher at entry.', 44, qrY + 58);
  ctx.fillStyle = '#64748b';
  ctx.font = '10px sans-serif';
  ctx.fillText(`Ref: ${props.booking?.transaction_ref || bookingId.value}`, 44, qrY + 80);

  // Draw QR Code Image
  if (localQrCodeUrl.value) {
    try {
      const qrImg = new Image();
      await new Promise((resolve) => {
        qrImg.onload = resolve;
        qrImg.onerror = resolve;
        qrImg.src = localQrCodeUrl.value;
      });
      if (qrImg.complete) {
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(width - 174, qrY + 20, 140, 140);
        ctx.drawImage(qrImg, width - 169, qrY + 25, 130, 130);
      }
    } catch (e) {}
  }

  // Footer branding
  ctx.fillStyle = '#475569';
  ctx.font = '10px sans-serif';
  ctx.textAlign = 'center';
  ctx.fillText('movies • Official Ethiopian Cinema Digital E-Ticket Pass', width / 2, height - 18);
  ctx.textAlign = 'left';

  return canvas.toDataURL('image/png');
}

const downloadTicketImage = async () => {
  if (downloading.value) return;
  downloading.value = true;

  try {
    // 1. Native HTML5 Canvas Generator (Bulletproof, HD 2x quality, instant)
    const dataUrl = await generateTicketCanvas();
    const link = document.createElement('a');
    link.href = dataUrl;
    link.download = `Ticket-Pass-${bookingId.value}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (err) {
    console.warn('Canvas generator fallback to html2canvas:', err);
    try {
      // 2. Fallback to html2canvas
      const canvas = await html2canvas(ticketPassRef.value, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#09090b',
        logging: false
      });
      const dataUrl = canvas.toDataURL('image/png');
      const link = document.createElement('a');
      link.href = dataUrl;
      link.download = `Ticket-Pass-${bookingId.value}.png`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } catch (e2) {
      alert('Could not download ticket image. Please use the Print button to save as PDF.');
    }
  } finally {
    downloading.value = false;
  }
}
</script>
