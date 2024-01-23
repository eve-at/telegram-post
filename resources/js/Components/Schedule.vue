<template>
    <div class="flex mt-4">
        <div 
            class="mx-auto flex flex-col items-center space-y-5"
        >
            <span class="text-gray-600">
                Shoose desired date and time
            </span>
            <DatePicker 
                class="scheduleCalendar"
                v-model="publisheAt" 
                mode="dateTime" 
                :attributes="datePickerAttributes"
                :rules="datePickerRules"
                :time-accuracy="datePickerTimeAccuracy"
                is24hr 
                :first-day-of-week="2"
                :min-date="(new Date)"
                @update:modelValue="onDayTimeChange"
            />

            <PrimaryButton 
                :disabled="!publisheAt || processing"
                @click.prevent="schedule"
            >
                Schedule
            </PrimaryButton>

            <div 
                v-if="!! scheduleStatus"
                class="border rounded-md py-2 pl-6 pr-2 mt-2 text-sm "
                :class="{
                    'bg-red-100 border-red-300 text-red-600': scheduleStatus.status === 'error',
                    'bg-green-100 border-green-300 text-green-600': scheduleStatus.status === 'success',
                }"
            >
                <span 
                    v-if="scheduleStatus.status === 'error'"
                    class="font-semibold"
                >Error(s):</span>
                <ul>
                    <li 
                        v-for="message in scheduleStatus.messages"
                        v-text="message"
                    ></li>
                </ul>
            </div>
        </div>
        
    </div>
</template>

<script setup>
import PrimaryButton from './PrimaryButton.vue';
import 'v-calendar/dist/style.css';
import { Calendar, DatePicker } from 'v-calendar';
import { ref, onMounted, onUnmounted } from 'vue';
import useEmitter from '@/Composables/useEmitter.js';

const emitter = useEmitter();

defineProps({
    processing: {
        type: Boolean,
        default: false,
    }
});

let scheduleStatus = ref(null);
const datePickerTimeAccuracy = ref(2); //1 => hour, 2 => minute, 3 => second
const publisheAt = ref(null);
const datePickerRules = ref({
    minutes: { interval: 5 },
});
const datePickerAttributes = ref([
    {
        dot: 'blue',
        dates: [
            new Date(2024, 0, 19),
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

const onDayTimeChange = (dateTime) => {
    publisheAt.value = dateTime;
    scheduleStatus.value = null;
}

const schedule = () => {
    scheduleStatus.value = null;
    emitter.emit('schedule', publisheAt.value);
}

onMounted(() => {
    emitter.on('schedule.status', (data) => {
        console.log(data);
        scheduleStatus.value = null;

        switch (data.status) {
            case 'success':
                scheduleStatus.value = {
                    status: 'success',
                    messages: data.messages,
                }
                break;
            case 'error':
                scheduleStatus.value = {
                    status: 'error',
                    messages: data.messages,
                }
                break;
        }
    });
});

onUnmounted(() => {
    emitter.off('schedule.status');
});

</script>