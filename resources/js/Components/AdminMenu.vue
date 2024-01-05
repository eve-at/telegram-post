<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex flex-col divide-y divide-solid">
        <Link href="/dashboard" class="px-3 py-2">Dashboard</Link>
        <Link href="/messages" class="px-3 py-2">Messages</Link>
        <Link href="/posts" class="px-3 py-2">Posts</Link>
        <Link href="/channel/edit" class="px-3 py-2">Settings</Link>
    </div>
</template>