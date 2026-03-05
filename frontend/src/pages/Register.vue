<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="card w-full max-w-sm space-y-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-white mb-2">Register</h1>
        <p class="text-gray-400 text-sm">Create an account to start booking movies</p>
      </div>

      <div class="space-y-4">
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Name</label>
          <input v-model="name" placeholder="Full Name" />
        </div>

        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Email</label>
          <input v-model="email" type="email" placeholder="email@example.com" />
        </div>

        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Password</label>
          <input v-model="password" type="password" placeholder="••••••••" />
        </div>

        <button @click="register" :disabled="loading" class="btn-primary w-full mt-2">
          {{ loading ? 'Creating...' : 'Register' }}
        </button>
      </div>

      <p class="text-center text-sm text-gray-400">
        Already have an account?
        <router-link to="/login" class="text-blue-500 hover:underline inline-block ml-1">Login here</router-link>
      </p>
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

const register = async () => {
    if (!name.value || !email.value || !password.value) return
    loading.value = true
    try {
        const res = await api.post("/register", { name: name.value, email: email.value, password: password.value })
        localStorage.setItem("token", res.data.token)
        localStorage.setItem("user", JSON.stringify(res.data.user))
        window.dispatchEvent(new Event('storage'))
        router.push("/movies")
    } catch (err) {
        alert("Registration failed")
    } finally {
        loading.value = false
    }
}
</script>