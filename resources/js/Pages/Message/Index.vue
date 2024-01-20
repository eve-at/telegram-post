
<template>
    <Head title="Posts - List" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
            <PrimaryButtonLink :href="route('messages.create')">Add new</PrimaryButtonLink>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <pro-calendar
                    :events="messages"
                    :loading="fetchingMessages"
                    :config="cfg"
                    view="week"
                    :date="(new Date).toISOString()"
                    @calendarClosed="close"
                    @fetchEvents="fetchMessages"
                    class=""
                >
                    <template #closeButton>
                        <!-- replace close button with this slot -->
                    </template>
                </pro-calendar>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import "vue-pro-calendar/style";
import { ref, onMounted, toRaw } from "vue";
import { Configs } from "vue-pro-calendar/dist/types/stores/events.d.js";
import { E_CustomEvents } from "vue-pro-calendar"
import axios from 'axios';
import PrimaryButtonLink from '@/Components/PrimaryButtonLink.vue';

const props = defineProps({
    messages: {
        type: Array,
        required: true,
    }
});

let fetchingMessages = ref(false);
let messages = ref(props.messages)

//https://github.com/lbgm/vue-pro-calendar
const cfg = ref<Configs>({
    viewEvent: {
        icon: true,
        text: "View post",
    },
    reportEvent: {
        icon: true,
        text: "Edit message",
    },
    searchPlaceholder: "Search...",
    eventName: "",
    closeText: "",
    nativeDatepicker: false,
    todayButton: true,
    firstDayOfWeek: 1,
});

onMounted(() => {
  [E_CustomEvents.VIEW].forEach((e: string) => {
    document.body.addEventListener(e, (event: Event | CustomEvent) => {
        let message = toRaw(props.messages.find((message) => message.id == event.detail.id));
        window.open(route(message.type + '.edit', message.type_id), '_blank');
    });
  });
  [E_CustomEvents.REPORT].forEach((e: string) => {
    document.body.addEventListener(e, (event: Event | CustomEvent) => {
        window.open(route('messages.edit', event.detail.id), '_blank');
    });
  });
});

const close = (e) => {
    console.log('close', e);
}

const fetchMessages = async (e) => {
    if (fetchingMessages.value) {
        return;
    }
    fetchingMessages.value = true;

    const response = await axios.get(route('messages.dates', {
        start: e.start.slice(0, 10),
        end: e.end.slice(0, 10)
    }));

    messages.value = response.data;

    fetchingMessages.value = false;
}

</script>

<style>
/* loader */
.widget-calendar-wrapper > div:first-child > div:first-child {
    position: relative;
}
.widget-calendar-wrapper > div:first-child > div:first-child > span {
    position: absolute;
    top: 25px;
    right: -20px;
} 

.widget-calendar-wrapper > div:first-child {
    padding-top: 0;
} 
.calendar-base > div:first-child > div:last-child {
    display: none;
}
@media (max-width: 1150px) {
    .widget-calendar-wrapper .time-cell.text-right {
        display: none;
    }
    .widget-calendar-wrapper .calendar-base {
        padding-left: 0;
    }
}
@media (max-width: 1024px) {
    .widget-calendar-wrapper > div:first-child {
        display: none;
    }
    .widget-calendar-wrapper .calendar-base {
        padding: 20px;
    }
    .widget-calendar-wrapper .time-cell.text-right {
        display: block;
    }
}
</style>