<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="card w-full max-w-sm space-y-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-white mb-2">Login</h1>
        <p class="text-gray-400 text-sm">Sign in to your account to book tickets</p>
      </div>

      <div class="space-y-4">
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Email</label>
          <input v-model="email" type="email" placeholder="email@example.com" />
        </div>

        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Password</label>
          <input v-model="password" type="password" placeholder="••••••••" />
        </div>

        <button @click="login" :disabled="loading" class="btn-primary w-full mt-2">
          {{ loading ? 'Signing in...' : 'Login' }}
        </button>
      </div>

      <p class="text-center text-sm text-gray-400">
        Don't have an account?
        <router-link to="/register" class="text-blue-500 hover:underline inline-block ml-1">Register here</router-link>
      </p>
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

const login = async () => {
    if (!email.value || !password.value) return
    loading.value = true
    try {
        const res = await api.post("/login", { email: email.value, password: password.value })
        localStorage.setItem("token", res.data.token)
        localStorage.setItem("user", JSON.stringify(res.data.user))
        window.dispatchEvent(new Event('storage'))
        router.push("/movies")
    } catch (err) {
        alert("Login failed. Check your credentials.")
    } finally {
        loading.value = false
    }
}
</script>