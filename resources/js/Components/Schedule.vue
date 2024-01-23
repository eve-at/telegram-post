<template>
    <RadioGroup 
        name="scheduleView"
        v-model="scheduleView"
        :options="scheduleOptions"
        @change="scheduleViewChange($event)"
    />

    <div class="flex mt-4">
        <div 
            v-if="scheduleView === 'calendar'"
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
                :attributes="datePickerAttributes"
            />

            <PrimaryButton 
                @click.prevent="emitter.emit('schedule', 'calendar')"
            >
                Add to the calendar
            </PrimaryButton>
        </div>
        <div 
            v-if="scheduleView === 'feed'"
            class="mx-auto flex flex-col items-start space-y-5"
        >
            <span class="text-gray-600">
                Feed view

                <PrimaryButton 
                    @click.prevent="emitter.emit('schedule', 'feed')"
                >
                    Add to the feed
                </PrimaryButton>
            </span>
        </div>
        <div 
            v-if="scheduleView === 'now'"
            class="mx-auto flex flex-col items-start space-y-5"
        >
            <span class="text-gray-600">
                Publish your post right now

                <PrimaryButton 
                    @click.prevent="emitter.emit('schedule', 'now')"
                >
                    Publish it now
                </PrimaryButton>
            </span>
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

const scheduleOptions = ref({
    'calendar': 'Calendar',
    'feed': 'Feed',
    'now': 'Publish now'
});
const scheduleView = ref('calendar');

const scheduleViewChange = (e) => {
    scheduleView.value = e.target.value;
}

const datePickerTimeAccuracy = ref(2); //1 => hour, 2 => minute, 3 => second
const publishedAt = ref('');
const datePickerAttributes = ref([
    {
        // Boolean
        dot: true,
        dates: [
            new Date(2024, 1, 18),
            new Date(2024, 1, 22),
            new Date(2024, 1, 27),
        ],
    }
]);

</script>

<style>
.scheduleCalendar .vc-weekday-1, .scheduleCalendar .vc-weekday-7 {
  color: #6366f1;
}
</style>