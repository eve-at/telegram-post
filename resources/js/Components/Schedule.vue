<template>
    <div class="flex mt-4">
        <div 
            class="mx-auto flex flex-col items-start space-y-5"
        >
            <span class="text-gray-600">
                Shoose desired date and time
            </span>
            <DatePicker 
                class="scheduleCalendar"
                v-model="publishedAt" 
                mode="dateTime" 
                :time-accuracy="datePickerTimeAccuracy"
                is24hr 
                :first-day-of-week="2"
                :min-date="(new Date)"
                :expanded="true"
                :attributes="datePickerAttributes"
            />

            <PrimaryButton 
                @click.prevent="emitter.emit('schedule', 'calendar')"
            >
                Add to the calendar
            </PrimaryButton>
        </div>
        
    </div>
</template>

<script setup>
import RadioGroup from '@/Components/RadioGroup.vue';
import PrimaryButton from './PrimaryButton.vue';
import 'v-calendar/dist/style.css';
import { Calendar, DatePicker } from 'v-calendar';
import { ref } from 'vue';
import useEmitter from '@/Composables/useEmitter.js';

const emitter = useEmitter();

const datePickerTimeAccuracy = ref(2); //1 => hour, 2 => minute, 3 => second
const publishedAt = ref('');
const datePickerAttributes = ref([
    {
        dot: 'blue',
        dates: [
            new Date(2024, 0, 18),
            new Date(2024, 0, 21),
            new Date(2024, 0, 26),
        ],
    },
    {
        dot: 'red',
        dates: [
            new Date(2024, 0, 18),
            new Date(2024, 0, 22),
            new Date(2024, 0, 27),
        ],
    },
    
]);

</script>

<style>
.scheduleCalendar .vc-weekday-1, .scheduleCalendar .vc-weekday-7 {
  color: #6366f1;
}
</style>