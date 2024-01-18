<template>
    <Link
        :method="method"
        as="button"
        :href="href"
        class="block w-full max-w-40 px-4 py-2 text-start text-sm leading-3 lg:leading-4 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out overflow-x-hidden whitespace-nowrap"

        :class="{
            'bg-gray-100' : active
        }"
    >
        <slot />
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false
    },
    method: {
        type: String,
        default: 'get'
    }
});
</script>