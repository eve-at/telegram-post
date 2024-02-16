<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    small: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center bg-white border rounded-md border-gray-300 font-semibold text-xs text-gray-700 tracking-widest shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
        :class="{
            'px-4 py-2 uppercase': !small,
            'px-2': small,
        }"
    >
        <slot />
    </button>
</template>
