<template>
  <footer class="w-full bg-[#050505] text-white font-sans border-t border-gray-900 selection:bg-[#ef6a26] selection:text-white">
    <!-- Top Header Row -->
    <div class="max-w-7xl mx-auto px-6 md:px-12 lg:px-16 pt-10 pb-8 flex flex-col sm:flex-row items-center justify-between gap-6 border-b border-gray-800/70">
      
      <!-- Brand Logo -->
      <router-link to="/" class="flex items-center space-x-2.5 group">
        <div class="w-9 h-9 rounded-full bg-[#ef6a26] flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
          <!-- Film Reel SVG -->
          <svg class="w-5 h-5 fill-current text-white" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
            <circle cx="12" cy="12" r="1.5"/>
            <circle cx="12" cy="7" r="1.2"/>
            <circle cx="12" cy="17" r="1.2"/>
            <circle cx="7" cy="12" r="1.2"/>
            <circle cx="17" cy="12" r="1.2"/>
          </svg>
        </div>
        <span class="text-2xl font-extrabold tracking-tight text-white group-hover:text-gray-200 transition-colors">
          movies
        </span>
      </router-link>

      <!-- Help / Privacy Policy & Social Media Icons -->
      <div class="flex flex-wrap items-center gap-6 sm:gap-8">
        <router-link to="/privacy-policy" class="text-sm font-medium text-gray-200 hover:text-[#ef6a26] transition-colors duration-200">
          Help / Privacy Policy
        </router-link>

        <!-- 4 Social Icons (Twitter/X, Facebook, Pinterest, Instagram) -->
        <div class="flex items-center space-x-3">
          <!-- Twitter / X -->
          <a href="#" class="w-10 h-10 rounded-full bg-gray-900 hover:bg-[#ef6a26] text-white flex items-center justify-center transition-all duration-300 shadow-md transform hover:-translate-y-0.5" aria-label="Twitter">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
          </a>

          <!-- Facebook -->
          <a href="#" class="w-10 h-10 rounded-full bg-gray-900 hover:bg-[#ef6a26] text-white flex items-center justify-center transition-all duration-300 shadow-md transform hover:-translate-y-0.5" aria-label="Facebook">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </a>

          <!-- Pinterest -->
          <a href="#" class="w-10 h-10 rounded-full bg-gray-900 hover:bg-[#ef6a26] text-white flex items-center justify-center transition-all duration-300 shadow-md transform hover:-translate-y-0.5" aria-label="Pinterest">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.367 18.62 0 12.017 0z"/>
            </svg>
          </a>

          <!-- Instagram -->
          <a href="#" class="w-10 h-10 rounded-full bg-gray-900 hover:bg-[#ef6a26] text-white flex items-center justify-center transition-all duration-300 shadow-md transform hover:-translate-y-0.5" aria-label="Instagram">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
          </a>
        </div>
      </div>

    </div>

    <!-- Main Footer Body Section -->
    <div class="max-w-7xl mx-auto px-6 md:px-12 lg:px-16 py-14">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-12">
        
        <!-- Column 1: Brand Pitch & CTA (Span 4) -->
        <div class="lg:col-span-4 flex flex-col justify-between pr-0 lg:pr-4 space-y-6">
          <h2 class="text-2xl sm:text-3xl font-extrabold text-white leading-snug tracking-tight">
            Buy movie tickets easily with Movies system nationwide
          </h2>
          <div>
            <router-link 
              to="/movies" 
              class="inline-block bg-[#ef6a26] hover:bg-orange-600 text-white font-bold text-xs uppercase tracking-wider px-8 py-4 transition-all duration-300 shadow-xl hover:shadow-orange-600/30 hover:-translate-y-0.5"
            >
              Get Your Ticket
            </router-link>
          </div>
        </div>

        <!-- Column 2: Movies Category Links (Span 2) -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Section Title with L-shaped Accent Corner -->
          <div class="relative inline-block pb-1">
            <h3 class="text-lg font-bold text-[#ef6a26]">Movies</h3>
            <div class="absolute bottom-0 left-0 w-6 h-[2px] bg-[#ef6a26]"></div>
            <div class="absolute bottom-0 right-0 w-[2px] h-2 bg-[#ef6a26]"></div>
          </div>

          <ul class="space-y-2.5 text-sm text-gray-400">
            <li><router-link to="/movies" class="hover:text-white transition-colors block py-0.5">Action</router-link></li>
            <li><router-link to="/movies" class="hover:text-white transition-colors block py-0.5">Adventure</router-link></li>
            <li><router-link to="/movies" class="hover:text-white transition-colors block py-0.5">Animation</router-link></li>
            <li><router-link to="/movies" class="hover:text-white transition-colors block py-0.5">Comedy</router-link></li>
            <li><router-link to="/movies" class="hover:text-white transition-colors block py-0.5">Crime</router-link></li>
          </ul>
        </div>

        <!-- Column 3: Quick Links (Span 2) -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Section Title with L-shaped Accent Corner -->
          <div class="relative inline-block pb-1">
            <h3 class="text-lg font-bold text-[#ef6a26]">Links</h3>
            <div class="absolute bottom-0 left-0 w-6 h-[2px] bg-[#ef6a26]"></div>
            <div class="absolute bottom-0 right-0 w-[2px] h-2 bg-[#ef6a26]"></div>
          </div>

          <ul class="space-y-2.5 text-sm text-gray-400">
            <li><router-link to="/about" class="hover:text-white transition-colors block py-0.5">About</router-link></li>
            <li><router-link to="/my-bookings" class="hover:text-white transition-colors block py-0.5">My account</router-link></li>
            <li><router-link to="/news" class="hover:text-white transition-colors block py-0.5">News</router-link></li>
            <li><router-link to="/events" class="hover:text-white transition-colors block py-0.5">Latest Events</router-link></li>
            <li><router-link to="/contact" class="hover:text-white transition-colors block py-0.5">Contact</router-link></li>
          </ul>
        </div>

        <!-- Column 4: Newsletter Subscription (Span 4) -->
        <div class="lg:col-span-4 space-y-5">
          <!-- Section Title with L-shaped Accent Corner -->
          <div class="relative inline-block pb-1">
            <h3 class="text-lg font-bold text-[#ef6a26]">Newsletter</h3>
            <div class="absolute bottom-0 left-0 w-6 h-[2px] bg-[#ef6a26]"></div>
            <div class="absolute bottom-0 right-0 w-[2px] h-2 bg-[#ef6a26]"></div>
          </div>

          <p class="text-sm text-gray-300 font-medium leading-relaxed">
            Subscribe to the Movies newsletter today for the latest movie updates.
          </p>

          <!-- Email Input Box -->
          <form @submit.prevent="handleSubscribe" class="space-y-3">
            <div class="relative flex items-center h-11 sm:h-12 bg-white w-full px-4 shadow-lg border-2 border-transparent focus-within:border-[#ef6a26] focus-within:ring-2 focus-within:ring-[#ef6a26]/30 transition-all rounded-none">
              <input 
                v-model="email" 
                type="email" 
                placeholder="Email Address" 
                required
                class="w-full h-full !p-0 !m-0 !text-gray-900 !bg-transparent text-sm outline-none placeholder:!text-gray-500 placeholder:!opacity-100 pr-8 font-normal"
              />
              <button 
                type="submit" 
                class="absolute right-3.5 text-[#ef6a26] hover:text-orange-600 transition-colors p-1"
                title="Subscribe"
              >
                <!-- Send Arrow SVG -->
                <svg class="w-4 h-4 fill-current transform rotate-45" viewBox="0 0 24 24">
                  <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
              </button>
            </div>

            <!-- Agreement Checkbox -->
            <label class="flex items-center space-x-2.5 cursor-pointer text-xs sm:text-sm text-gray-200 hover:text-white transition-colors pt-1 select-none group">
              <input 
                v-model="agree" 
                type="checkbox" 
                required
                class="w-4 h-4 rounded-full accent-[#ef6a26] cursor-pointer shrink-0 border border-gray-400 focus:ring-1 focus:ring-[#ef6a26]"
              />
              <span class="text-gray-200 font-medium group-hover:text-white">I agree to all terms and policies of the company</span>
            </label>

            <!-- Success Message Toast -->
            <p v-if="subscribed" class="text-xs text-green-400 font-medium">
              ✓ Thank you for subscribing to our newsletter!
            </p>
          </form>
        </div>

      </div>
    </div>

    <!-- Bottom Copyright Bar -->
    <div class="border-t border-gray-900 bg-black/60 py-6 text-center text-xs text-gray-500">
      <p>© {{ new Date().getFullYear() }} Movies. All Rights Reserved.</p>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')
const agree = ref(false)
const subscribed = ref(false)

const handleSubscribe = () => {
  if (email.value && agree.value) {
    subscribed.value = true
    email.value = ''
    agree.value = false
    setTimeout(() => {
      subscribed.value = false
    }, 4000)
  }
}
</script>

<style scoped>
/* Optional specific overrides */
</style>
