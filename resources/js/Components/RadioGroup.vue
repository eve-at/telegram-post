<template>
<div class="flex mx-auto border border-gray-300 rounded-md divide-x divide-gray-300 whitespace-nowrap">
    <label 
        v-for="(optionName, optionValue) in options"
        :key="optionValue"
        :for="optionValue" 
        class="w-1/2 py-1 text-center hover:bg-gray-200 cursor-pointer"
        :class="{
            'bg-gray-300': optionValue === modelValue
        }"
    >
        <input 
            type="radio" 
            :id="optionValue" 
            :name="'radioGroup_' + name" 
            :value="optionValue" 
            @change="$emit('update:modelValue', $event.target.value)"
        />
        {{ optionName }}
    </label>
</div>
</template>

<script setup>
defineEmits(['update:modelValue']);
defineProps({
    name: {
        type: String,
        requered: true
    },
    modelValue: {
        type: String,
        required: true,
    },
    options: {
        type: Object,
        requered: true
    }
});
</script>