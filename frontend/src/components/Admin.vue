<script setup>

import { ref, onMounted } from "vue"
import api from "../services/api"

const movies = ref([])

const title = ref("")
const description = ref("")
const show_time = ref("")
const total_seats = ref("")

const token = localStorage.getItem("token")

const headers = {
Authorization: `Bearer ${token}`
}

const loadMovies = async () => {

const res = await api.get("/movies")

movies.value = res.data

}

const createMovie = async () => {

await api.post("/movies",
{
title: title.value,
description: description.value,
show_time: show_time.value,
total_seats: total_seats.value
},
{ headers }
)

loadMovies()

}

const deleteMovie = async (id) => {

await api.delete(`/movies/${id}`, { headers })

loadMovies()

}

onMounted(loadMovies)

</script>

<template>

<div class="p-10">

<h1 class="text-3xl mb-6">Admin Panel</h1>

<div class="mb-10">

<input v-model="title" placeholder="Title" class="border p-2 mr-2"/>
<input v-model="description" placeholder="Description" class="border p-2 mr-2"/>
<input v-model="show_time" placeholder="Show Time" class="border p-2 mr-2"/>
<input v-model="total_seats" placeholder="Seats" class="border p-2 mr-2"/>

<button
@click="createMovie"
class="bg-blue-500 text-white px-4 py-2"
>
Create Movie
</button>

</div>

<div
v-for="movie in movies"
:key="movie.id"
class="border p-4 mb-4"
>

<h2>{{ movie.title }}</h2>

<button
@click="deleteMovie(movie.id)"
class="bg-red-500 text-white px-3 py-1 mt-2"
>
Delete
</button>

</div>

</div>

</template>