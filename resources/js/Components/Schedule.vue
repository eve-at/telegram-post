<template>
    <div class="mt-4">
        <div class="flex space-x-2">
            <div 
                class="mx-auto flex w-1/2 flex-col items-center space-y-5"
            >
                <span class="text-gray-600">
                    Shoose desired date and time
                </span>
                <DatePicker 
                    class="scheduleCalendar"
                    v-model="scheduleData.publishAt" 
                    mode="dateTime" 
                    :attributes="datePickerAttributes"
                    :rules="datePickerRules"
                    :time-accuracy="datePickerTimeAccuracy"
                    is24hr 
                    :first-day-of-week="2"
                    :min-date="(new Date)"
                    @dayclick="onDayClick"
                    @update:modelValue="onDayTimeChange"
                />


                <span 
                    v-if="! canSchedule" 
                    class="block italic text-center"
                >
                    Please save your article first
                </span>
                <div 
                    v-if="canSchedule" 
                >
                    <div class="space-y-1">
                        <div class="flex space-x-2 items-center text-right w-full">
                            <InputLabel class="w-2/4" for="hoursOnTop">Hours on top</InputLabel>
                            <NumberInput 
                                class="w-1/4 py-1" 
                                id="hoursOnTop" 
                                v-model="scheduleData.hoursOnTop" 
                                :step="1" 
                                :min="1"
                            />
                        </div>
                        <div class="flex space-x-2 items-center text-right">
                            <InputLabel class="w-2/4" for="removeAfterHours">Remove after (hours)</InputLabel>
                            <NumberInput 
                                class="w-1/4 py-1" 
                                id="removeAfterHours" 
                                v-model="scheduleData.removeAfterHours" 
                                :step="24" 
                                :min="0"
                            />
                        </div>
                    </div>

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
                <PrimaryButton 
                    :disabled="!scheduleData.publishAt || processing || ! canSchedule"
                    @click.prevent="schedule"
                >
                    Schedule
                </PrimaryButton> 
            </div>
            <div 
                class="w-1/2"
            >
                <span 
                    v-text="scheduleData.publishAt.toDateString()"
                    class="block text-center font-semibold mb-5"
                ></span>

                <span
                    v-if="!messages.length"
                    class="block text-gray-600 italic whitespace-nowrap text-center"
                >
                    < No posts for this day >
                </span>
                <div 
                    v-for="message in messages"
                    class="border rounded mb-1 px-2 py-1"
                    :class="{
                        'border-green-300 bg-green-100': message.ad,
                        'border-blue-300 bg-blue-100': !message.ad,
                    }"
                >
                    <div class="flex justify-between text-sm">
                        <div
                            class="font-bold"
                        >
                            <span 
                                v-text="message.published_at"
                            ></span>
                            <span 
                                v-if="message.ad_top_until"
                                v-text="' - ' + message.ad_top_until"
                            ></span>
                        </div>
                        <div 
                            v-if="message.ad"
                        >
                            <span 
                                v-text="message.ad_hours_on_top + '/' + message.ad_remove_after_hours"
                                class="text-gray-600"
                            ></span>
                        </div>
                    </div>
                    <div class="flex">
                        <span
                            class="text-xs italic text-gray-600 whitespace-nowrap overflow-hidden"
                        >
                            <span v-if="message.ad" class="font-semibold">[ Ad ]</span>
                            <span v-if="!message.ad">[ {{ message.type }} ]</span>
                            {{ message.title }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import PrimaryButton from './PrimaryButton.vue';
import InputLabel from './InputLabel.vue';
import NumberInput from './NumberInput.vue';
import 'v-calendar/dist/style.css';
import { Calendar, DatePicker } from 'v-calendar';
import { ref, onMounted, onUnmounted } from 'vue';
import useEmitter from '@/Composables/useEmitter.js';

const emitter = useEmitter();

defineProps({
    processing: {
        type: Boolean,
        default: false,
    },
    canSchedule: {
        type: Boolean,
        default: false
    },
});

let scheduleStatus = ref(null);
const datePickerTimeAccuracy = ref(2); //1 => hour, 2 => minute, 3 => second
let shoosenDate = ref('');

const messages = ref([
    {
        id: 1,
        ad: 0,
        ad_hours_on_top: null,
        ad_remove_after_hours: null,
        title: 'Hello world',
        type: 'Post',
        status: 0,
        published_at: '08:00',
        ad_top_until: null
    },
    {
        id: 2,
        ad: 1,
        ad_hours_on_top: 1,
        ad_remove_after_hours: 24,
        title: 'Some ad',
        type: 'Post',
        status: 0,
        published_at: '09:00',
        ad_top_until: '10:00',
    },
    {
        id: 3,
        ad: 0,
        ad_hours_on_top: null,
        ad_remove_after_hours: null,
        title: 'Another post with a long long long long long text',
        type: 'Post',
        status: 0,
        published_at: '15:00',
        ad_top_until: null
    },
    {
        id: 4,
        ad: 1,
        ad_hours_on_top: 2,
        ad_remove_after_hours: 48,
        title: 'Second ad',
        type: 'Post',
        status: 0,
        published_at: '17:00',
        ad_top_until: '19:00',
    },
    {
        id: 5,
        ad: 0,
        ad_hours_on_top: null,
        ad_remove_after_hours: null,
        title: 'Evening poll',
        type: 'Poll',
        status: 0,
        published_at: '20:00',
        ad_top_until: null,
    }
]);

const scheduleData = ref({
    publishAt: new Date,
    hoursOnTop: "1",
    removeAfterHours: "24",
});

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

const onDayClick = (date) => {
    shoosenDate.value = date.id; //2024-01-26
    
    // TODO: update scheduled post view for shoosen date
}

const onDayTimeChange = (dateTime) => {
    scheduleData.value.publishAt = dateTime;
    scheduleStatus.value = null;
}

const schedule = () => {
    scheduleStatus.value = null;
    emitter.emit('schedule', scheduleData.value);
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