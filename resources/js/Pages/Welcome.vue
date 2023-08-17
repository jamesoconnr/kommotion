<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <Head title="Welcome" />
    <main class="grid place-items-center min-h-[100svh]">
        <div class="flex flex-col gap-5 items-center">
            <ApplicationLogo class="h-20"/>
            <div class="font-medium text-5xl"><b>Kommotion</b><br>is notion for the<br>
                <h1 class="font-semibold">less organized</h1>
            </div>
            <img class="w-2/5" src='https://dummyimage.com/500x250.png' alt='' />
            <div class="flex gap-10 mx-auto">
                <Link :href="route('notes.index')" class="font-bold text-gray-600 underline underline-offset-2 text-xl">try it out</Link>
            </div>
        </div>
    </main>
</template>

<style></style>
