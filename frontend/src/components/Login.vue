<script setup>
import { ref } from "vue"
import api from "../services/api"

const email = ref("")
const password = ref("")

const login = async () => {

    const res = await api.post("/login", {
        email: email.value,
        password: password.value
    })

    localStorage.setItem("token", res.data.token)

    alert("Login success")
}
</script>

<template>

<div class="flex justify-center mt-20">

<div class="w-96 p-6 bg-white shadow rounded">

<h2 class="text-2xl mb-4">Login</h2>

<input
v-model="email"
placeholder="Email"
class="border p-2 w-full mb-3"
/>

<input
type="password"
v-model="password"
placeholder="Password"
class="border p-2 w-full mb-3"
/>

<button
@click="login"
class="bg-blue-500 text-white px-4 py-2 w-full"
>
Login
</button>

</div>

</div>

</template>