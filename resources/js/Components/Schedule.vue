<template>
    <RadioGroup 
        name="scheduleView"
        v-model="scheduleView"
        :options="scheduleOptions"
        @change="scheduleViewChange($event)"
    />

    <div v-if="scheduleView === 'calendar'">
        Calendar view
    </div>
    <div v-if="scheduleView === 'feed'">
        Feed view
    </div>
</template>

<script setup>
import RadioGroup from '@/Components/RadioGroup.vue';
import { ref } from 'vue';

const scheduleView = ref('calendar');
const scheduleOptions = ref({
    'calendar': 'Calendar',
    'feed': 'Feed'
});

const scheduleViewChange = (e) => {
    scheduleView.value = e.target.value;
}
</script>