<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
      <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Login to MovieBooking</h1>

      <!-- Error Message Box -->
      <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
        {{ errorMessage }}
      </div>
      
      <form @submit.prevent="login" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
          <input 
            v-model="email" 
            type="email" 
            required
            placeholder="Enter your email" 
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input 
            v-model="password" 
            type="password" 
            required
            placeholder="Enter your password" 
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button 
          type="submit"
          :disabled="loading" 
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors disabled:bg-blue-400"
        >
          {{ loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-600">
        Don't have an account? 
        <router-link to="/register" class="text-blue-600 hover:underline">Register here</router-link>
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
    errorMessage.value = "" // Clear previous errors

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
/* Removed complex animations and custom styles for simplicity */
</style>