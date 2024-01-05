<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="w-11/12 flex bg-white overflow-hidden">
            <div class="p-6 text-gray-900">You're logged in!</div>
        </div>
    </AuthenticatedLayout>
</template>
