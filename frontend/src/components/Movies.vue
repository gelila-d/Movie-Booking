<script setup>

import { ref, onMounted } from "vue"
import api from "../services/api"

const movies = ref([])

const loadMovies = async () => {

const res = await api.get("/movies")

movies.value = res.data

}

const bookMovie = async (id) => {

const token = localStorage.getItem("token")

await api.post("/bookings",
{
movie_id: id
},
{
headers: {
Authorization: `Bearer ${token}`
}
})

alert("Movie booked!")

}

onMounted(loadMovies)

</script>

<template>

<div class="p-10">

<h1 class="text-3xl mb-6">Movies</h1>

<div
v-for="movie in movies"
:key="movie.id"
class="border p-4 mb-4"
>

<h2 class="text-xl">{{ movie.title }}</h2>

<p>{{ movie.description }}</p>

<p>Seats: {{ movie.available_seats }}</p>

<button
@click="bookMovie(movie.id)"
class="bg-green-500 text-white px-3 py-1 mt-2"
>
Book
</button>

</div>

</div>

</template>