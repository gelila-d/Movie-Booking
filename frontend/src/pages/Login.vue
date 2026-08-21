<template>
  <div class="min-h-screen flex items-center justify-center bg-[#050505] p-4 relative select-none overflow-hidden">
    <!-- Light film strip background pattern -->
    <div class="absolute inset-0 pointer-events-none film-strip-dark"></div>

    <!-- Ambient Glowing Backdrop Effect -->
    <div class="absolute w-[500px] h-[500px] bg-[#ef6a26]/15 rounded-full blur-[140px] pointer-events-none top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>

    <!-- Card Container -->
    <div class="relative z-10 bg-black/50 border border-white/10 hover:border-[#ef6a26]/40 transition-colors duration-500 shadow-[0_25px_60px_rgba(0,0,0,0.9),inset_0_1px_0_rgba(255,255,255,0.15)] backdrop-blur-2xl p-8 sm:p-10 rounded-3xl w-full max-w-md">
      
      <!-- Brand Logo Header -->
      <div class="flex flex-col items-center mb-8">
        <router-link to="/" class="flex items-center space-x-2.5 mb-3 group">
          <svg class="w-9 h-9 text-[#ef6a26] group-hover:scale-105 transition-transform duration-300" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
            <circle cx="12" cy="12" r="2.5"/>
            <circle cx="12" cy="6.5" r="1.5"/>
            <circle cx="17.23" cy="10.3" r="1.5"/>
            <circle cx="15.23" cy="16.45" r="1.5"/>
            <circle cx="8.77" cy="16.45" r="1.5"/>
            <circle cx="6.77" cy="10.3" r="1.5"/>
          </svg>
          <span class="text-3xl font-extrabold tracking-tight text-white">movies</span>
        </router-link>
        <h1 class="text-2xl font-bold text-white font-orbitron tracking-wide">WELCOME BACK</h1>
        <p class="text-xs text-slate-300 mt-1">Sign in to book your movie tickets</p>
      </div>

      <!-- Error Message Box -->
      <div v-if="errorMessage" class="mb-5 p-3.5 bg-red-950/60 border border-red-800/60 text-red-300 rounded-xl text-xs text-center font-medium">
        {{ errorMessage }}
      </div>
      
      <form @submit.prevent="login" class="space-y-5">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-200 mb-1.5 font-mono">Email Address</label>
          <input 
            v-model="email" 
            type="email" 
            required
            placeholder="name@example.com" 
            class="w-full px-4 py-3 bg-black/40 text-white border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400 text-sm transition-all shadow-inner"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold uppercase tracking-wider text-slate-200 mb-1.5 font-mono">Password</label>
          <input 
            v-model="password" 
            type="password" 
            required
            placeholder="••••••••" 
            class="w-full px-4 py-3 bg-black/40 text-white border border-white/10 rounded-xl focus:outline-none focus:border-[#ef6a26] focus:ring-2 focus:ring-[#ef6a26]/30 placeholder-slate-400 text-sm transition-all shadow-inner"
          />
        </div>

        <button 
          type="submit"
          :disabled="loading" 
          class="btn-primary w-full py-3.5 px-4 text-xs font-extrabold tracking-wider disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer mt-2"
        >
          {{ loading ? 'SIGNING IN...' : 'SIGN IN' }}
        </button>
      </form>

      <div class="mt-8 text-center text-xs text-slate-400">
        Don't have an account? 
        <router-link to="/register" class="text-[#ef6a26] hover:text-orange-400 font-bold ml-1 transition-colors">Register here</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useRouter } from "vue-router"
import api from "../services/api"

const router = useRouter()
const email = ref("")
const password = ref("")
const loading = ref(false)
const errorMessage = ref("")

const login = async () => {
    if (!email.value || !password.value) return
    loading.value = true
    errorMessage.value = ""

    try {
        const res = await api.post("/login", { email: email.value, password: password.value })
        localStorage.setItem("token", res.data.token)
        localStorage.setItem("user", JSON.stringify(res.data.user))
        window.dispatchEvent(new Event('storage'))
        router.push("/movies")
    } catch (err) {
        if (err.response && err.response.status === 401) {
             errorMessage.value = "Incorrect email or password.";
        } else if (err.response && err.response.data && err.response.data.message) {
            errorMessage.value = err.response.data.message;
        } else {
            errorMessage.value = "Login failed. Check your connection.";
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
</style>