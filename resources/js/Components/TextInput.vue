<script setup>
import { onMounted, ref, nextTick } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    placeholder: {
        type: String,
        required: false,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(async () => {
    if (input.value.hasAttribute('autofocus')) {
        document.activeElement && document.activeElement.blur();        
        await nextTick();
        input.value.focus();
    }
    if (input.value.hasAttribute('autoselect') && input.value.value.length) {
        await nextTick();
        input.value.select();
    }
});

defineExpose({ 
    focus: () => input.value.focus(),
    select: () => input.value.select(),
});
</script>

<template>
    <input
        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
