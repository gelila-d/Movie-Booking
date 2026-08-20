<template>
  <div class="min-h-screen flex items-center justify-center bg-[#050505] p-4 relative select-none overflow-hidden">
    <!-- Light film strip background pattern -->
    <div class="absolute inset-0 pointer-events-none film-strip-dark"></div>

    <!-- Ambient Glowing Backdrop Effect -->
    <div class="absolute w-[500px] h-[500px] bg-[#ea580c]/15 rounded-full blur-[140px] pointer-events-none top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>

    <!-- Card Container -->
    <div class="relative z-10 bg-[#0f1015]/95 border border-gray-800/80 shadow-[0_20px_60px_rgba(0,0,0,0.8)] backdrop-blur-xl p-8 sm:p-10 rounded-2xl w-full max-w-md">
      
      <!-- Brand Logo Header -->
      <div class="flex flex-col items-center mb-8">
        <router-link to="/" class="flex items-center space-x-2.5 mb-3 group">
          <div class="w-10 h-10 rounded-full bg-[#ea580c] flex items-center justify-center shadow-lg shadow-[#ea580c]/30 group-hover:scale-105 transition-transform duration-300">
            <svg class="w-6 h-6 fill-current text-white" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
              <circle cx="12" cy="12" r="1.5"/>
            </svg>
          </div>
          <span class="text-3xl font-extrabold tracking-tight text-white">movies</span>
        </router-link>
        <h1 class="text-xl font-bold text-gray-200">Create an Account</h1>
        <p class="text-xs text-gray-400 mt-1">Join Movies today to start booking tickets</p>
      </div>

      <!-- Error Message Box -->
      <div v-if="errorMessage" class="mb-5 p-3.5 bg-red-950/60 border border-red-800/60 text-red-300 rounded-xl text-xs text-center font-medium">
        {{ errorMessage }}
      </div>
      
      <form @submit.prevent="register" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Full Name</label>
          <input 
            v-model="name" 
            type="text" 
            required
            placeholder="John Doe" 
            class="w-full px-4 py-3 bg-[#161820] text-white border border-gray-700/80 rounded-xl focus:outline-none focus:border-[#ea580c] focus:ring-2 focus:ring-[#ea580c]/30 placeholder-gray-500 text-sm transition-all shadow-inner"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Email Address</label>
          <input 
            v-model="email" 
            type="email" 
            required
            placeholder="name@example.com" 
            class="w-full px-4 py-3 bg-[#161820] text-white border border-gray-700/80 rounded-xl focus:outline-none focus:border-[#ea580c] focus:ring-2 focus:ring-[#ea580c]/30 placeholder-gray-500 text-sm transition-all shadow-inner"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-1.5">Password</label>
          <input 
            v-model="password" 
            type="password" 
            required
            placeholder="•••••••• (min. 6 chars)" 
            class="w-full px-4 py-3 bg-[#161820] text-white border border-gray-700/80 rounded-xl focus:outline-none focus:border-[#ea580c] focus:ring-2 focus:ring-[#ea580c]/30 placeholder-gray-500 text-sm transition-all shadow-inner"
          />
        </div>

        <button 
          type="submit"
          :disabled="loading" 
          class="w-full bg-[#ea580c] hover:bg-[#d64800] text-white font-extrabold py-3.5 px-4 rounded-xl transition-all duration-300 shadow-lg shadow-[#ea580c]/25 uppercase tracking-wider text-xs disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer transform hover:-translate-y-0.5 mt-2"
        >
          {{ loading ? 'Creating account...' : 'Create Account' }}
        </button>
      </form>

      <div class="mt-8 text-center text-xs text-gray-400">
        Already have an account? 
        <router-link to="/login" class="text-[#ea580c] hover:text-orange-400 font-bold ml-1 transition-colors">Sign in here</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const router = useRouter()
const name = ref("")
const email = ref("")
const password = ref("")
const loading = ref(false)
const errorMessage = ref("")

const register = async () => {
    if (!name.value || !email.value || !password.value) return
    loading.value = true
    errorMessage.value = ""
    
    try {
        const res = await api.post("/register", { name: name.value, email: email.value, password: password.value })
        localStorage.setItem("token", res.data.token)
        localStorage.setItem("user", JSON.stringify(res.data.user))
        window.dispatchEvent(new Event('storage'))
        router.push("/movies")
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            const errors = err.response.data.errors;
            const firstKey = Object.keys(errors)[0];
            errorMessage.value = errors[firstKey][0];
        } else {
            errorMessage.value = "Registration failed. Check your connection."
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
</style>