<template>
    <select 
        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        @change="$emit('update:modelValue', $event.target.value)"
        :value="modelValue"
        ref="select"
    >
        <option v-for="(lab, val) in options" :value="val" v-text="lab"></option>
    </select>
</template>

<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    options: {
        type: Object,
        default: {}
    }
});

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>