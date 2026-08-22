<template>
  <div class="container space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white mb-1 font-orbitron">ADMIN DASHBOARD</h1>
        <p class="text-slate-300">Manage movies, showtimes, ticket pricing (Birr), cinemas & auditoriums</p>
      </div>
      <div class="flex gap-3 flex-wrap">
        <button 
          v-if="!showForm && activeTab === 'catalog'" 
          @click="openCreate" 
          class="btn-primary"
        >
          + Add New Movie
        </button>
        <button 
          v-if="!showShowtimeForm && activeTab === 'showtimes'" 
          @click="openCreateShowtime" 
          class="btn-primary"
        >
          + Schedule Showtime
        </button>
        <button 
          v-if="activeTab === 'cinemas' && !showCinemaForm && !showAuditoriumForm" 
          @click="openCreateCinema" 
          class="btn-primary"
        >
          + Add Cinema
        </button>
        <button 
          v-if="activeTab === 'cinemas' && !showCinemaForm && !showAuditoriumForm" 
          @click="openCreateAuditorium" 
          class="px-5 py-2.5 rounded-xl border border-emerald-500/40 text-emerald-300 bg-emerald-950/40 hover:bg-emerald-900/60 font-bold text-sm transition-all"
        >
          + Add Auditorium / Hall
        </button>
      </div>
    </div>

    <!-- Statistics Dashboard -->
    <div v-if="stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Movies</span>
        <span class="text-3xl font-bold text-white font-orbitron">{{ stats.summary.total_movies }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Showtimes</span>
        <span class="text-3xl font-bold text-purple-400 font-orbitron">{{ stats.summary.total_showtimes ?? 0 }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Total Tickets Sold</span>
        <span class="text-3xl font-bold text-[#ef6a26] font-orbitron">{{ stats.summary.booked_seats }}</span>
      </div>
      <div class="bg-black/50 backdrop-blur-2xl p-6 rounded-2xl shadow-xl border border-white/10 flex flex-col">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1 font-mono">Overall Fill Rate</span>
        <div class="flex items-end gap-2">
          <span class="text-3xl font-bold text-blue-400 font-orbitron">{{ stats.summary.overall_fill_rate }}%</span>
          <div class="flex-grow h-2 bg-slate-800 rounded-full mb-2 overflow-hidden">
            <div class="h-full bg-blue-500" :style="{ width: stats.summary.overall_fill_rate + '%' }"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-white/10 gap-4">
      <div class="flex space-x-2 overflow-x-auto">
        <button 
          @click="activeTab = 'catalog'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
          :class="activeTab === 'catalog' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Movie Catalog
        </button>
        <button 
          @click="activeTab = 'showtimes'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
          :class="activeTab === 'showtimes' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Showtimes & Ticket Pricing
        </button>
        <button 
          @click="activeTab = 'cinemas'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
          :class="activeTab === 'cinemas' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Cinemas & Auditoriums
        </button>
        <button 
          @click="activeTab = 'bookings'" 
          class="px-6 py-3 text-sm font-bold transition-colors border-b-2 whitespace-nowrap"
          :class="activeTab === 'bookings' ? 'border-[#ef6a26] text-[#ef6a26]' : 'border-transparent text-slate-400 hover:text-white'"
        >
          Recent Bookings
        </button>
      </div>
      <div class="relative w-full md:w-64 mb-2 md:mb-0">
        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">🔍</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          :placeholder="activeTab === 'catalog' ? 'Search movies...' : activeTab === 'showtimes' ? 'Search showtimes...' : activeTab === 'cinemas' ? 'Search cinemas/halls...' : 'Search users or movies...'" 
          class="pl-10 pr-4 py-2 w-full border border-slate-700/80 rounded-xl focus:ring-2 focus:ring-[#ef6a26] outline-none text-sm bg-slate-900/90 text-white placeholder-slate-400"
        />
      </div>
    </div>

    <!-- Movie Form (Create/Edit) -->
    <div v-if="showForm && activeTab === 'catalog'" class="card space-y-6 border-[#ef6a26]/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
      <div class="flex justify-between items-center border-b border-white/10 pb-4">
        <h2 class="text-xl font-bold text-white font-orbitron">{{ editingId ? 'Edit Movie' : 'Add New Movie' }}</h2>
        <button @click="closeForm" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">Cancel</button>
      </div>

      <div v-if="errorMessage" class="p-3.5 bg-red-950/60 border border-red-800/60 text-red-300 rounded-xl text-xs text-center font-medium">
        {{ errorMessage }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Movie Title</label>
          <input v-model="form.title" placeholder="Title" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Default Release / Show Date</label>
          <input v-model="form.show_time" type="datetime-local" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30" />
        </div>
        <div class="md:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Description</label>
          <input v-model="form.description" placeholder="Movie description..." class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="md:col-span-2 space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Movie Poster Image</label>
          <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-slate-200 px-4 py-2.5 border border-white/10 rounded-xl bg-black/40 text-sm file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#ef6a26] file:text-white hover:file:bg-orange-600" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono">Default Capacity (Seats)</label>
          <input v-model="form.total_seats" @input="form.total_seats = Math.abs($event.target.value) || ''" type="number" min="1" placeholder="50" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400" />
        </div>
        <div class="flex items-end">
          <button @click="saveMovie" :disabled="saving" class="btn-primary w-full py-3">
            {{ saving ? 'SAVING...' : 'SAVE MOVIE' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Showtime Form (Create/Edit) -->
    <div v-if="showShowtimeForm && activeTab === 'showtimes'" class="card space-y-6 border-purple-500/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
      <div class="flex justify-between items-center border-b border-white/10 pb-4">
        <h2 class="text-xl font-bold text-white font-orbitron">{{ editingShowtimeId ? 'Edit Showtime & Pricing' : 'Schedule New Showtime & Pricing' }}</h2>
        <button @click="closeShowtimeForm" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">Cancel</button>
      </div>

      <div v-if="showtimeError" class="p-4 bg-red-950/80 border border-red-500/60 text-red-200 rounded-xl text-xs text-center font-bold tracking-wide">
        ⚠️ {{ showtimeError }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="space-y-1.5 lg:col-span-2">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Select Movie</label>
          <select v-model="showtimeForm.movie_id" class="w-full text-white bg-slate-900 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30">
            <option value="" disabled>Choose a movie...</option>
            <option v-for="m in movies" :key="m.id" :value="m.id">{{ m.title }}</option>
          </select>
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Cinema Venue</label>
          <select v-model="selectedCinemaId" @change="handleCinemaChange" class="w-full text-white bg-slate-900 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30">
            <option value="">Select Cinema...</option>
            <option v-for="c in cinemas" :key="c.id" :value="c.id">{{ c.name }} ({{ c.location || 'Main' }})</option>
          </select>
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Auditorium / Hall</label>
          <select v-model="showtimeForm.auditorium_id" @change="handleAuditoriumSelect" class="w-full text-white bg-slate-900 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30">
            <option value="">Choose Hall...</option>
            <option v-for="a in availableAuditoriums" :key="a.id" :value="a.id">
              {{ a.name }} — {{ a.total_seats }} seats ({{ a.rows_count }} R x {{ a.seats_per_row }} S)
            </option>
          </select>
        </div>

        <div class="space-y-1.5 lg:col-span-2">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">Start Time</label>
          <input v-model="showtimeForm.start_time" type="datetime-local" @change="autoSetEndTime" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30" />
        </div>

        <div class="space-y-1.5 lg:col-span-2">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">End Time</label>
          <input v-model="showtimeForm.end_time" type="datetime-local" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30" />
        </div>

        <!-- Ticket Pricing Tiers in Birr -->
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-emerald-400 font-mono">Regular Seat Price (Birr)</label>
          <input v-model="showtimeForm.price" type="number" min="100" placeholder="100" class="w-full text-white bg-black/40 px-4 py-2.5 border border-emerald-500/30 rounded-xl focus:outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/30 placeholder-slate-400 font-mono font-bold" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">VIP Seat Price (Birr)</label>
          <input v-model="showtimeForm.vip_price" type="number" min="100" placeholder="150" class="w-full text-white bg-black/40 px-4 py-2.5 border border-purple-500/30 rounded-xl focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-400/30 placeholder-slate-400 font-mono font-bold" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-blue-300 font-mono">Student Ticket Price (Birr)</label>
          <input v-model="showtimeForm.student_price" type="number" min="50" placeholder="80" class="w-full text-white bg-black/40 px-4 py-2.5 border border-blue-500/30 rounded-xl focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 placeholder-slate-400 font-mono font-bold" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-amber-300 font-mono">Child Ticket Price (Birr)</label>
          <input v-model="showtimeForm.child_price" type="number" min="40" placeholder="60" class="w-full text-white bg-black/40 px-4 py-2.5 border border-amber-500/30 rounded-xl focus:outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/30 placeholder-slate-400 font-mono font-bold" />
        </div>

        <div class="lg:col-span-4 flex justify-end gap-3 pt-2 border-t border-white/10">
          <button @click="closeShowtimeForm" class="px-6 py-2.5 rounded-xl border border-slate-700 text-slate-300 text-sm font-bold hover:bg-slate-800 transition-colors">
            Cancel
          </button>
          <button @click="saveShowtime" :disabled="savingShowtime" class="btn-primary py-2.5 px-8">
            {{ savingShowtime ? 'SAVING...' : (editingShowtimeId ? 'UPDATE SHOWTIME' : 'CREATE SHOWTIME') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Cinema Form (Create/Edit) -->
    <div v-if="showCinemaForm && activeTab === 'cinemas'" class="card space-y-6 border-blue-500/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
      <div class="flex justify-between items-center border-b border-white/10 pb-4">
        <h2 class="text-xl font-bold text-white font-orbitron">{{ editingCinemaId ? 'Edit Cinema' : 'Add New Cinema' }}</h2>
        <button @click="closeCinemaForm" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">Cancel</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-blue-300 font-mono">Cinema Name</label>
          <input v-model="cinemaForm.name" placeholder="e.g. Starlight Multiplex" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 placeholder-slate-400" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-blue-300 font-mono">Location / Address</label>
          <input v-model="cinemaForm.location" placeholder="e.g. Downtown Mall, Level 3" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30 placeholder-slate-400" />
        </div>
        <div class="md:col-span-2 flex justify-end gap-3 pt-2">
          <button @click="saveCinema" class="btn-primary py-2.5 px-8">
            SAVE CINEMA
          </button>
        </div>
      </div>
    </div>

    <!-- Auditorium Form (Create/Edit) -->
    <div v-if="showAuditoriumForm && activeTab === 'cinemas'" class="card space-y-6 border-emerald-500/40 bg-black/60 backdrop-blur-2xl shadow-2xl">
      <div class="flex justify-between items-center border-b border-white/10 pb-4">
        <h2 class="text-xl font-bold text-white font-orbitron">{{ editingAuditoriumId ? 'Edit Auditorium / Hall' : 'Add New Auditorium / Hall' }}</h2>
        <button @click="closeAuditoriumForm" class="text-slate-400 hover:text-white transition-colors text-sm font-bold">Cancel</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-emerald-300 font-mono">Select Cinema Venue</label>
          <select v-model="auditoriumForm.cinema_id" class="w-full text-white bg-slate-900 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30">
            <option value="" disabled>Choose cinema...</option>
            <option v-for="c in cinemas" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-emerald-300 font-mono">Hall Name / Number</label>
          <input v-model="auditoriumForm.name" placeholder="e.g. Hall A, IMAX Hall, VIP Lounge" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 placeholder-slate-400" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-emerald-300 font-mono">Number of Rows (A to Z)</label>
          <input v-model="auditoriumForm.rows_count" type="number" min="1" max="26" placeholder="10" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30" />
        </div>
        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-emerald-300 font-mono">Seats Per Row</label>
          <input v-model="auditoriumForm.seats_per_row" type="number" min="1" max="30" placeholder="12" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-purple-300 font-mono">VIP Rows Count (Top Rows)</label>
          <input v-model="auditoriumForm.vip_rows_count" type="number" min="0" max="10" placeholder="2" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/30" />
        </div>

        <div class="space-y-1.5">
          <label class="text-xs font-semibold uppercase tracking-wider text-emerald-300 font-mono">Base Hall Ticket Price (Birr)</label>
          <input v-model="auditoriumForm.base_price" type="number" min="100" placeholder="100" class="w-full text-white bg-black/40 px-4 py-2.5 border border-white/10 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 font-mono font-bold" />
        </div>

        <div class="md:col-span-2 bg-emerald-950/30 border border-emerald-500/30 p-4 rounded-xl flex justify-between items-center">
          <span class="text-xs font-mono text-emerald-300 font-bold uppercase">Computed Total Capacity:</span>
          <span class="text-2xl font-bold font-mono text-emerald-400">{{ (auditoriumForm.rows_count || 0) * (auditoriumForm.seats_per_row || 0) }} Seats</span>
        </div>

        <div class="md:col-span-2 flex justify-end gap-3 pt-2">
          <button @click="saveAuditorium" class="btn-primary py-2.5 px-8">
            SAVE AUDITORIUM
          </button>
        </div>
      </div>
    </div>

    <!-- Catalog Tab Content -->
    <div v-if="activeTab === 'catalog'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">Catalog ({{ filteredMovies.length }})</h2>
        <button @click="loadMovies" class="text-xs font-bold text-[#ef6a26] hover:underline">Refresh</button>
      </div>
      
      <div v-if="loading" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-[#ef6a26] border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredMovies.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No movies found matching your search.</p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-2 text-[#ef6a26] text-sm font-bold hover:underline">Clear Search</button>
      </div>

      <div v-for="movie in filteredMovies" :key="movie.id" class="card flex flex-col md:flex-row md:items-center justify-between border-[#ef6a26]/20 bg-black/50 backdrop-blur-xl hover:border-[#ef6a26]/50 transition-all">
        <div class="flex-grow">
          <h3 class="text-lg font-bold text-white mb-1">{{ movie.title }}</h3>
          <p class="text-xs text-slate-300 mb-2 truncate max-w-md">{{ movie.description }}</p>
          <div class="flex flex-wrap items-center text-xs text-slate-300 gap-4 mb-2">
            <span class="font-medium">🕒 Release: {{ movie.show_time ? new Date(movie.show_time).toLocaleString() : 'N/A' }}</span>
            <span v-if="getFillRate(movie.id) !== null" class="px-2.5 py-0.5 rounded-full font-bold text-[11px]" :class="getFillRateColor(getFillRate(movie.id))">
              {{ getFillRate(movie.id) }}% Booked
            </span>
          </div>
          <div v-if="movie.image" class="mt-2">
            <img :src="getImageUrl(movie.image)" alt="Movie Image" class="h-20 w-auto rounded-lg border border-white/10 object-cover" />
          </div>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
          <button @click="openEdit(movie)" class="text-orange-400 hover:text-orange-300 text-xs font-bold bg-orange-500/10 border border-orange-500/20 px-3.5 py-2 rounded-lg transition-colors">
            Edit
          </button>
          <button @click="deleteMovie(movie.id)" class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-3.5 py-2 rounded-lg transition-colors">
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Showtimes Management Tab Content -->
    <div v-if="activeTab === 'showtimes'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">Scheduled Showtimes & Pricing ({{ filteredShowtimes.length }})</h2>
        <button @click="loadShowtimes" class="text-xs font-bold text-purple-400 hover:underline">Refresh</button>
      </div>

      <div v-if="loadingShowtimes" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-purple-500 border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredShowtimes.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No scheduled showtimes found.</p>
        <button @click="openCreateShowtime" class="mt-3 text-purple-400 text-sm font-bold hover:underline">+ Schedule a Showtime</button>
      </div>

      <div v-else class="grid grid-cols-1 gap-4">
        <div 
          v-for="st in filteredShowtimes" 
          :key="st.id" 
          class="card flex flex-col md:flex-row md:items-center justify-between border-purple-500/30 bg-black/50 backdrop-blur-xl hover:border-purple-500/70 transition-all gap-4"
        >
          <div class="flex items-start md:items-center gap-4">
            <div v-if="st.movie?.image" class="w-16 h-20 bg-slate-800 rounded-lg overflow-hidden flex-shrink-0 border border-white/10">
              <img :src="getImageUrl(st.movie.image)" alt="Poster" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-16 h-20 bg-purple-950/40 rounded-lg flex items-center justify-center text-xl flex-shrink-0 border border-purple-500/20">
              🎬
            </div>

            <div class="space-y-1.5">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="text-lg font-bold text-white">{{ st.movie?.title || 'Unknown Movie' }}</h3>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold font-mono bg-purple-500/20 border border-purple-500/40 text-purple-300">
                  🏛️ {{ st.auditoriumDetail?.cinema?.name ? `${st.auditoriumDetail.cinema.name} - ${st.auditoriumDetail.name}` : st.auditorium }}
                </span>
              </div>

              <!-- Price Tiers Badges in Birr -->
              <div class="flex items-center gap-2 flex-wrap text-xs font-mono font-bold">
                <span class="px-2.5 py-0.5 rounded bg-emerald-500/15 border border-emerald-500/30 text-emerald-300">
                  Regular: {{ Number(st.price).toFixed(0) }} Birr
                </span>
                <span class="px-2.5 py-0.5 rounded bg-purple-500/15 border border-purple-500/30 text-purple-300">
                  VIP: {{ Number(st.vip_price || st.price * 1.5).toFixed(0) }} Birr
                </span>
                <span class="px-2.5 py-0.5 rounded bg-blue-500/15 border border-blue-500/30 text-blue-300">
                  Student: {{ Number(st.student_price || st.price * 0.8).toFixed(0) }} Birr
                </span>
                <span class="px-2.5 py-0.5 rounded bg-amber-500/15 border border-amber-500/30 text-amber-300">
                  Child: {{ Number(st.child_price || st.price * 0.6).toFixed(0) }} Birr
                </span>
              </div>

              <div class="flex flex-wrap items-center text-xs text-slate-300 gap-y-1 gap-x-4 pt-1 font-mono">
                <span>🕒 {{ new Date(st.start_time).toLocaleString(undefined, {dateStyle: 'short', timeStyle: 'short'}) }} → {{ new Date(st.end_time).toLocaleTimeString(undefined, {timeStyle: 'short'}) }}</span>
                <span>🪑 {{ st.available_seats }} / {{ st.total_seats }} Available Seats</span>
                <span 
                  class="px-2 py-0.5 rounded text-[10px] font-bold"
                  :class="getFillRateColor(getShowtimeFillRate(st))"
                >
                  {{ getShowtimeFillRate(st) }}% Booked
                </span>
              </div>
            </div>
          </div>

          <div class="flex space-x-2 self-end md:self-center">
            <button @click="openEditShowtime(st)" class="text-purple-300 hover:text-purple-100 text-xs font-bold bg-purple-500/20 border border-purple-500/40 px-3.5 py-2 rounded-lg transition-colors">
              Edit
            </button>
            <button @click="deleteShowtime(st.id)" class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-3.5 py-2 rounded-lg transition-colors">
              Cancel Showtime
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Cinemas & Auditoriums Tab Content -->
    <div v-if="activeTab === 'cinemas'" class="space-y-6">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">Cinemas & Auditorium Layouts ({{ cinemas.length }})</h2>
        <button @click="loadCinemas" class="text-xs font-bold text-blue-400 hover:underline">Refresh</button>
      </div>

      <div v-if="loadingCinemas" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="cinemas.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No cinemas configured yet.</p>
        <button @click="openCreateCinema" class="mt-3 text-blue-400 text-sm font-bold hover:underline">+ Add Cinema</button>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div v-for="cinema in filteredCinemas" :key="cinema.id" class="card bg-black/50 backdrop-blur-xl border-blue-500/30 space-y-4">
          <div class="flex justify-between items-start border-b border-white/10 pb-3">
            <div>
              <h3 class="text-xl font-bold text-white font-orbitron">{{ cinema.name }}</h3>
              <p class="text-xs text-slate-400 font-mono mt-0.5">📍 {{ cinema.location || 'Main Location' }}</p>
            </div>
            <div class="flex space-x-2">
              <button @click="openEditCinema(cinema)" class="text-blue-300 hover:text-blue-100 text-xs font-bold bg-blue-500/20 border border-blue-500/40 px-2.5 py-1 rounded">
                Edit
              </button>
              <button @click="deleteCinema(cinema.id)" class="text-red-400 hover:text-red-300 text-xs font-bold bg-red-500/10 border border-red-500/20 px-2.5 py-1 rounded">
                Delete
              </button>
            </div>
          </div>

          <!-- Auditoriums List inside Cinema -->
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-xs font-mono font-bold text-slate-300 uppercase">Halls / Auditoriums ({{ cinema.auditoriums?.length || 0 }})</span>
              <button @click="openCreateAuditoriumForCinema(cinema.id)" class="text-xs text-emerald-400 font-bold hover:underline">
                + Add Hall
              </button>
            </div>

            <div v-if="!cinema.auditoriums || cinema.auditoriums.length === 0" class="p-4 bg-slate-900/60 rounded-xl border border-dashed border-white/10 text-xs text-slate-400 text-center">
              No halls added to this cinema yet.
            </div>

            <div v-else class="space-y-2">
              <div 
                v-for="aud in cinema.auditoriums" 
                :key="aud.id" 
                class="p-3 bg-slate-900/80 rounded-xl border border-white/10 flex justify-between items-center hover:border-emerald-500/40 transition-colors"
              >
                <div>
                  <div class="text-sm font-bold text-emerald-300 font-mono">🏛️ {{ aud.name }}</div>
                  <div class="text-xs text-slate-400 font-mono mt-0.5 flex gap-3">
                    <span>🪑 {{ aud.total_seats }} Seats ({{ aud.rows_count }} R x {{ aud.seats_per_row }} S)</span>
                    <span class="text-emerald-400 font-bold">Base: {{ Number(aud.base_price || 100).toFixed(0) }} Birr</span>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button @click="openEditAuditorium(aud)" class="text-xs text-emerald-400 hover:underline">Edit</button>
                  <button @click="deleteAuditorium(aud.id)" class="text-xs text-red-400 hover:underline">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bookings Tab Content -->
    <div v-if="activeTab === 'bookings'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-white">All Bookings ({{ filteredBookings.length }})</h2>
        <button @click="loadAllBookings" class="text-xs font-bold text-[#ef6a26] hover:underline">Refresh</button>
      </div>

      <div v-if="loadingBookings" class="flex justify-center p-10">
        <div class="animate-spin h-6 w-6 border-2 border-[#ef6a26] border-t-transparent rounded-full"></div>
      </div>

      <div v-else-if="filteredBookings.length === 0" class="text-center py-20 bg-black/40 border border-dashed border-white/10 rounded-xl">
        <p class="text-slate-300">No bookings found matching your search.</p>
        <button v-if="searchQuery" @click="searchQuery = ''" class="mt-2 text-[#ef6a26] text-sm font-bold hover:underline">Clear Search</button>
      </div>

      <div v-else class="overflow-x-auto rounded-2xl border border-white/10 shadow-2xl backdrop-blur-xl">
        <table class="w-full text-left bg-black/60">
          <thead class="bg-black/80 border-b border-white/10">
            <tr>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Movie / Showtime</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Cinema / Hall</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Seats</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Total Amount</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-300 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="booking in filteredBookings" :key="booking.id" class="hover:bg-white/5 transition-colors">
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-white">{{ booking.user?.name || 'Unknown' }}</span>
                  <span class="text-xs text-slate-400">{{ booking.user?.email || 'N/A' }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-slate-200 font-medium">
                <div>{{ booking.movie?.title || 'Deleted Movie' }}</div>
                <div v-if="booking.showtime" class="text-xs text-purple-300 font-mono">
                  {{ new Date(booking.showtime.start_time).toLocaleString(undefined, {dateStyle: 'short', timeStyle: 'short'}) }}
                </div>
              </td>
              <td class="px-6 py-4 text-xs text-slate-300 font-mono">
                {{ booking.showtime?.auditoriumDetail?.cinema?.name ? `${booking.showtime.auditoriumDetail.cinema.name} - ${booking.showtime.auditoriumDetail.name}` : (booking.showtime?.auditorium || 'Main Screen') }}
              </td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold rounded-md block w-max">
                  {{ booking.seats_booked }} Seats ({{ booking.seat_numbers?.join(', ') || 'N/A' }})
                </span>
              </td>
              <td class="px-6 py-4 text-xs font-bold text-emerald-400 font-mono">
                {{ booking.total_price ? `${Number(booking.total_price).toFixed(0)} Birr` : 'N/A' }}
              </td>
              <td class="px-6 py-4 text-xs text-slate-400 font-mono">{{ new Date(booking.created_at).toLocaleDateString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue"
import api from "../services/api"

const movies = ref([])
const showtimes = ref([])
const cinemas = ref([])
const stats = ref(null)
const allBookings = ref([])
const loading = ref(true)
const loadingShowtimes = ref(false)
const loadingCinemas = ref(false)
const loadingBookings = ref(false)
const saving = ref(false)
const savingShowtime = ref(false)

const showForm = ref(false)
const showShowtimeForm = ref(false)
const showCinemaForm = ref(false)
const showAuditoriumForm = ref(false)

const editingId = ref(null)
const editingShowtimeId = ref(null)
const editingCinemaId = ref(null)
const editingAuditoriumId = ref(null)

const errorMessage = ref("")
const showtimeError = ref("")
const imageFile = ref(null)
const activeTab = ref('catalog')
const searchQuery = ref("")
const selectedCinemaId = ref("")

const filteredMovies = computed(() => {
    if (!searchQuery.value) return movies.value
    const query = searchQuery.value.toLowerCase()
    return movies.value.filter(movie => 
        movie.title.toLowerCase().includes(query) || 
        (movie.description && movie.description.toLowerCase().includes(query))
    )
})

const filteredShowtimes = computed(() => {
    if (!searchQuery.value) return showtimes.value
    const query = searchQuery.value.toLowerCase()
    return showtimes.value.filter(st => 
        (st.movie?.title && st.movie.title.toLowerCase().includes(query)) ||
        (st.auditorium && st.auditorium.toLowerCase().includes(query))
    )
})

const filteredCinemas = computed(() => {
    if (!searchQuery.value) return cinemas.value
    const query = searchQuery.value.toLowerCase()
    return cinemas.value.filter(c => 
        c.name.toLowerCase().includes(query) ||
        (c.location && c.location.toLowerCase().includes(query))
    )
})

const filteredBookings = computed(() => {
    if (!searchQuery.value) return allBookings.value
    const query = searchQuery.value.toLowerCase()
    return allBookings.value.filter(booking => 
        (booking.user?.name && booking.user.name.toLowerCase().includes(query)) ||
        (booking.user?.email && booking.user.email.toLowerCase().includes(query)) ||
        (booking.movie?.title && booking.movie.title.toLowerCase().includes(query)) ||
        (booking.showtime?.auditorium && booking.showtime.auditorium.toLowerCase().includes(query))
    )
})

const availableAuditoriums = computed(() => {
    if (!selectedCinemaId.value) return []
    const cinema = cinemas.value.find(c => c.id === Number(selectedCinemaId.value))
    return cinema ? cinema.auditoriums || [] : []
})

const getImageUrl = (path) => {
    if (!path) return '';
    return `http://localhost:8000/storage/${path}`;
}

const form = ref({
    title: "",
    description: "",
    show_time: "",
    total_seats: "50"
})

const showtimeForm = ref({
    movie_id: "",
    auditorium_id: "",
    auditorium: "",
    start_time: "",
    end_time: "",
    price: 100,
    vip_price: 150,
    student_price: 80,
    child_price: 60,
    total_seats: 50
})

const cinemaForm = ref({
    name: "",
    location: ""
})

const auditoriumForm = ref({
    cinema_id: "",
    name: "",
    rows_count: 10,
    seats_per_row: 12,
    vip_rows_count: 2,
    base_price: 100
})

const formatForDateTimeInput = (dateStr) => {
    if (!dateStr) return "";
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return "";
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

const autoSetEndTime = () => {
    if (showtimeForm.value.start_time) {
        const start = new Date(showtimeForm.value.start_time)
        const end = new Date(start.getTime() + 2 * 60 * 60 * 1000)
        showtimeForm.value.end_time = formatForDateTimeInput(end)
    }
}

const handleCinemaChange = () => {
    showtimeForm.value.auditorium_id = ""
    showtimeForm.value.total_seats = 50
}

const handleAuditoriumSelect = () => {
    if (showtimeForm.value.auditorium_id) {
        const aud = availableAuditoriums.value.find(a => a.id === Number(showtimeForm.value.auditorium_id))
        if (aud) {
            showtimeForm.value.total_seats = aud.total_seats
            showtimeForm.value.auditorium = aud.name
            if (aud.base_price) {
                showtimeForm.value.price = aud.base_price
                showtimeForm.value.vip_price = Math.round(aud.base_price * 1.5)
                showtimeForm.value.student_price = Math.round(aud.base_price * 0.8)
                showtimeForm.value.child_price = Math.round(aud.base_price * 0.6)
            }
        }
    }
}

const getFillRate = (movieId) => {
    if (!stats.value || !stats.value.movie_stats) return null;
    const movieStat = stats.value.movie_stats.find(s => s.id === movieId);
    return movieStat ? movieStat.fill_rate : null;
}

const getShowtimeFillRate = (st) => {
    if (!st.total_seats) return 0;
    const booked = st.total_seats - st.available_seats;
    return Math.round((booked / st.total_seats) * 100);
}

const getFillRateColor = (rate) => {
    if (rate >= 90) return 'bg-red-500/20 text-red-400 border border-red-500/30';
    if (rate >= 50) return 'bg-orange-500/20 text-orange-400 border border-orange-500/30';
    return 'bg-green-500/20 text-green-400 border border-green-500/30';
}

const handleFileUpload = (event) => {
    imageFile.value = event.target.files[0]
}

const loadMovies = async () => {
    loading.value = true
    try {
        const res = await api.get("/movies")
        movies.value = res.data
    } catch (err) {
        console.error("Failed to load movies:", err)
    } finally {
        loading.value = false
    }
}

const loadShowtimes = async () => {
    loadingShowtimes.value = true
    try {
        const res = await api.get("/showtimes")
        showtimes.value = res.data
    } catch (err) {
        console.error("Failed to load showtimes:", err)
    } finally {
        loadingShowtimes.value = false
    }
}

const loadCinemas = async () => {
    loadingCinemas.value = true
    try {
        const res = await api.get("/cinemas")
        cinemas.value = res.data
    } catch (err) {
        console.error("Failed to load cinemas:", err)
    } finally {
        loadingCinemas.value = false
    }
}

const loadStats = async () => {
    try {
        const res = await api.get("/admin/stats")
        stats.value = res.data
    } catch (err) {
        console.error("Failed to load stats:", err)
    }
}

const loadAllBookings = async () => {
    loadingBookings.value = true
    try {
        const res = await api.get("/admin/bookings")
        allBookings.value = res.data
    } catch (err) {
        console.error("Failed to load bookings:", err)
    } finally {
        loadingBookings.value = false
    }
}

const openCreate = () => {
    editingId.value = null
    form.value = { title: "", description: "", show_time: "", total_seats: "50" }
    imageFile.value = null
    errorMessage.value = ""
    showForm.value = true
}

const openEdit = (movie) => {
    editingId.value = movie.id
    const dateStr = formatForDateTimeInput(movie.show_time)
    form.value = { ...movie, show_time: dateStr }
    imageFile.value = null
    errorMessage.value = ""
    showForm.value = true
}

const closeForm = () => {
    showForm.value = false
    editingId.value = null
    errorMessage.value = ""
}

const openCreateShowtime = () => {
    editingShowtimeId.value = null
    selectedCinemaId.value = cinemas.value.length ? cinemas.value[0].id : ""
    showtimeForm.value = {
        movie_id: movies.value.length ? movies.value[0].id : "",
        auditorium_id: availableAuditoriums.value.length ? availableAuditoriums.value[0].id : "",
        auditorium: availableAuditoriums.value.length ? availableAuditoriums.value[0].name : "",
        start_time: "",
        end_time: "",
        price: 100,
        vip_price: 150,
        student_price: 80,
        child_price: 60,
        total_seats: availableAuditoriums.value.length ? availableAuditoriums.value[0].total_seats : 50
    }
    showtimeError.value = ""
    showShowtimeForm.value = true
}

const openEditShowtime = (st) => {
    editingShowtimeId.value = st.id
    selectedCinemaId.value = st.auditoriumDetail?.cinema_id || ""
    showtimeForm.value = {
        movie_id: st.movie_id,
        auditorium_id: st.auditorium_id,
        auditorium: st.auditorium,
        start_time: formatForDateTimeInput(st.start_time),
        end_time: formatForDateTimeInput(st.end_time),
        price: st.price || 100,
        vip_price: st.vip_price || 150,
        student_price: st.student_price || 80,
        child_price: st.child_price || 60,
        total_seats: st.total_seats
    }
    showtimeError.value = ""
    showShowtimeForm.value = true
}

const closeShowtimeForm = () => {
    showShowtimeForm.value = false
    editingShowtimeId.value = null
    showtimeError.value = ""
}

const openCreateCinema = () => {
    editingCinemaId.value = null
    cinemaForm.value = { name: "", location: "" }
    showCinemaForm.value = true
}

const openEditCinema = (c) => {
    editingCinemaId.value = c.id
    cinemaForm.value = { name: c.name, location: c.location }
    showCinemaForm.value = true
}

const closeCinemaForm = () => {
    showCinemaForm.value = false
    editingCinemaId.value = null
}

const openCreateAuditorium = () => {
    editingAuditoriumId.value = null
    auditoriumForm.value = {
        cinema_id: cinemas.value.length ? cinemas.value[0].id : "",
        name: "",
        rows_count: 10,
        seats_per_row: 12,
        vip_rows_count: 2,
        base_price: 100
    }
    showAuditoriumForm.value = true
}

const openCreateAuditoriumForCinema = (cinemaId) => {
    editingAuditoriumId.value = null
    auditoriumForm.value = {
        cinema_id: cinemaId,
        name: "",
        rows_count: 10,
        seats_per_row: 12,
        vip_rows_count: 2,
        base_price: 100
    }
    showAuditoriumForm.value = true
}

const openEditAuditorium = (aud) => {
    editingAuditoriumId.value = aud.id
    auditoriumForm.value = {
        cinema_id: aud.cinema_id,
        name: aud.name,
        rows_count: aud.rows_count,
        seats_per_row: aud.seats_per_row,
        vip_rows_count: aud.vip_rows_count || 2,
        base_price: aud.base_price || 100
    }
    showAuditoriumForm.value = true
}

const closeAuditoriumForm = () => {
    showAuditoriumForm.value = false
    editingAuditoriumId.value = null
}

const saveMovie = async () => {
    if (!form.value.title || !form.value.description) {
        errorMessage.value = "Title and description are required"
        return
    }

    saving.value = true
    errorMessage.value = ""
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('description', form.value.description);
        if (form.value.show_time) formData.append('show_time', form.value.show_time);
        if (form.value.total_seats) formData.append('total_seats', form.value.total_seats);
        if (imageFile.value) {
            formData.append('image', imageFile.value);
        }

        if (editingId.value) {
            formData.append('_method', 'PUT');
            await api.post(`/movies/${editingId.value}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await api.post("/movies", formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }
        closeForm()
        loadMovies()
        loadStats()
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            const errors = err.response.data.errors;
            const firstKey = Object.keys(errors)[0];
            errorMessage.value = errors[firstKey][0];
        } else {
            errorMessage.value = err.response?.data?.message || "Failed to save movie."
        }
        console.error(err)
    } finally {
        saving.value = false
    }
}

const saveShowtime = async () => {
    if (!showtimeForm.value.movie_id || (!showtimeForm.value.auditorium_id && !showtimeForm.value.auditorium) || !showtimeForm.value.start_time || !showtimeForm.value.end_time || !showtimeForm.value.price) {
        showtimeError.value = "Movie, auditorium, start/end times, and base price are required."
        return
    }

    savingShowtime.value = true
    showtimeError.value = ""
    try {
        if (editingShowtimeId.value) {
            await api.put(`/showtimes/${editingShowtimeId.value}`, showtimeForm.value)
        } else {
            await api.post("/showtimes", showtimeForm.value)
        }
        closeShowtimeForm()
        loadShowtimes()
        loadStats()
    } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
            showtimeError.value = err.response.data.message
        } else if (err.response && err.response.data && err.response.data.errors) {
            const errors = err.response.data.errors;
            const firstKey = Object.keys(errors)[0];
            showtimeError.value = errors[firstKey][0];
        } else {
            showtimeError.value = "Failed to save showtime. Please check inputs."
        }
    } finally {
        savingShowtime.value = false
    }
}

const saveCinema = async () => {
    if (!cinemaForm.value.name) return
    try {
        if (editingCinemaId.value) {
            await api.put(`/cinemas/${editingCinemaId.value}`, cinemaForm.value)
        } else {
            await api.post('/cinemas', cinemaForm.value)
        }
        closeCinemaForm()
        loadCinemas()
    } catch (err) {
        alert("Failed to save cinema")
    }
}

const saveAuditorium = async () => {
    if (!auditoriumForm.value.cinema_id || !auditoriumForm.value.name || !auditoriumForm.value.rows_count || !auditoriumForm.value.seats_per_row) return
    try {
        if (editingAuditoriumId.value) {
            await api.put(`/auditoriums/${editingAuditoriumId.value}`, auditoriumForm.value)
        } else {
            await api.post('/auditoriums', auditoriumForm.value)
        }
        closeAuditoriumForm()
        loadCinemas()
    } catch (err) {
        alert("Failed to save auditorium")
    }
}

const deleteMovie = async (id) => {
    if(!confirm("Remove this movie from the catalog?")) return
    try {
        await api.delete(`/movies/${id}`)
        loadMovies()
        loadStats()
    } catch (err) {
        alert("Deletion failed")
    }
}

const deleteShowtime = async (id) => {
    if(!confirm("Cancel and delete this showtime schedule?")) return
    try {
        await api.delete(`/showtimes/${id}`)
        loadShowtimes()
        loadStats()
    } catch (err) {
        alert("Deletion failed")
    }
}

const deleteCinema = async (id) => {
    if (!confirm("Delete this cinema and all its auditoriums?")) return
    try {
        await api.delete(`/cinemas/${id}`)
        loadCinemas()
    } catch (err) {
        alert("Deletion failed")
    }
}

const deleteAuditorium = async (id) => {
    if (!confirm("Delete this auditorium hall?")) return
    try {
        await api.delete(`/auditoriums/${id}`)
        loadCinemas()
    } catch (err) {
        alert("Deletion failed")
    }
}

watch(activeTab, (newTab) => {
    if (newTab === 'showtimes') {
        loadShowtimes()
        loadCinemas()
    } else if (newTab === 'cinemas') {
        loadCinemas()
    } else if (newTab === 'bookings') {
        loadAllBookings()
    }
})

onMounted(() => {
    loadMovies()
    loadShowtimes()
    loadCinemas()
    loadStats()
})
</script>