<template>
    <div v-if="showingFlash && $page.props.flash.message" 
        class="text-blue-600 mb-4 border border-blue-900 rounded-md px-3 py-2 bg-blue-300 fixed right-10 bottom-10"
    >
        {{ $page.props.flash.message }}
    </div>
    <div v-if="showingFlash && $page.props.flash.success" 
        class="text-green-900 mb-4 border border-green-900 rounded-md px-3 py-2 bg-green-300 fixed right-10 bottom-10"
    >
        {{ $page.props.flash.success }}
    </div>
    <div v-if="showingFlash && $page.props.flash.error" 
        class="text-red-600 mb-4 border border-red-900 rounded-md px-3 py-2 bg-red-300 fixed right-10 bottom-10"
    >
        {{ $page.props.flash.error }}
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

let showingFlash = ref(false);
onMounted(() => {
    showingFlash.value = true;
    setTimeout(() => showingFlash.value = false, 2000)
})
</script>