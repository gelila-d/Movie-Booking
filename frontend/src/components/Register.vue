<script setup>
import { ref } from "vue"
import api from "../services/api"

const name = ref("")
const email = ref("")
const password = ref("")

const register = async () => {

try {

const res = await api.post("/register", {
name: name.value,
email: email.value,
password: password.value
})

localStorage.setItem("token", res.data.token)

alert("Registration successful!")

}
catch(error){
alert("Registration failed")
}

}
</script>

<template>

<div class="flex justify-center mt-20">

<div class="w-96 p-6 bg-white shadow rounded">

<h2 class="text-2xl mb-4">Register</h2>

<input
v-model="name"
placeholder="Name"
class="border p-2 w-full mb-3"
/>

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
@click="register"
class="bg-green-500 text-white px-4 py-2 w-full"
>
Register
</button>

</div>

</div>

</template>