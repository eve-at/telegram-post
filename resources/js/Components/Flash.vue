<template>
    <div v-if="$page.props.flash && $page.props.flash.message" 
        class="z-50 text-blue-600 mb-4 border border-blue-900 rounded-md px-3 py-2 bg-blue-300 fixed right-10 bottom-10"
    >
        {{ $page.props.flash.message }}
    </div>
    <div v-if="$page.props.flash && $page.props.flash.success" 
        class="z-50 text-green-900 mb-4 border border-green-900 rounded-md px-3 py-2 bg-green-300 fixed right-10 bottom-10"
    >
        {{ $page.props.flash.success }}
    </div>
    <div v-if="$page.props.flash && $page.props.flash.error" 
        class="z-50 text-red-600 mb-4 border border-red-900 rounded-md px-3 py-2 bg-red-300 fixed right-10 bottom-10"
    >
        {{ $page.props.flash.error }}
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUpdated } from 'vue';

const page = usePage();

let showingFlash = ref(false);
onMounted(() => {
    showingFlash.value = true;
    setTimeout(() => page.props.flash = null, 2000)
});
</script>