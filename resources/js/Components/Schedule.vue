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
                    :timezone="$page.props.channel.timezone"
                    :attributes="datePickerAttributes"
                    :rules="datePickerRules"
                    :time-accuracy="datePickerTimeAccuracy"
                    is24hr 
                    :first-day-of-week="2"
                    :min-date="(new Date)"
                    @dayclick="onDayClick"
                    @update:modelValue="onDayTimeChange"
                />

                <div 
                    v-if="canSchedule && isAd" 
                    class="flex flex-col px-4 space-y-1"
                >
                    <div class="flex justify-center items-center">
                        <div class="w-5/12">
                            <InputLabel for="hoursOnTop">
                                Hours on top
                            </InputLabel>
                        </div>
                        <div class="w-5/12">
                            <NumberInput 
                                id="hoursOnTop" 
                                v-model="scheduleData.hoursOnTop" 
                                :step="1" 
                                :min="1"
                            />
                        </div>
                    </div>
                    <div class="flex justify-center items-center">
                        <div class="w-5/12">
                            <InputLabel for="removeAfterHours">
                                Remove after (hours)
                            </InputLabel>
                        </div>
                        <div class="w-5/12">
                            <NumberInput 
                                id="removeAfterHours" 
                                v-model="scheduleData.removeAfterHours" 
                                :step="24" 
                                :min="0"
                                :max="48"
                            />
                        </div>
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

                <div class="text-center">
                    <PrimaryButton 
                        :disabled="!scheduleData.publishAt || processing || ! canSchedule"
                        @click.prevent="schedule"
                    >
                        Schedule
                    </PrimaryButton> 

                    <span 
                        v-if="! canSchedule" 
                        class="block italic text-center mt-2"
                    >
                        Save changes before scheduling
                    </span>
                </div>
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
                    &lang; No posts for this day &rang;
                </span>
                <div 
                    v-for="message in messages"
                    class="group border rounded mb-1 px-2 py-1"
                    :class="{
                        'bg-gray-100': !message.id,
                        'bg-green-100': message.id && message.ad,
                        'bg-blue-100': message.id && !message.ad,
                        'border-blue-300': !message.ad && !sameMessagable(message),
                        'border-green-300': message.ad && !sameMessagable(message),
                        'border-black border-2': message.id && sameMessagable(message),
                    }"
                >
                    <div class="flex justify-between text-sm">
                        <div
                            class="font-bold"
                        >
                            <span 
                                v-text="message.published_at ? message.published_at : message.publish_at"
                            ></span>
                            <span 
                                v-if="message.ad"
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
                    <div class="flex text-xs justify-between space-x-2">
                        <span
                            class="italic text-gray-600 whitespace-nowrap overflow-hidden"
                        >
                            <span v-if="message.id && message.ad" class="font-semibold">[ Ad ]</span>
                            <span v-if="message.id && !message.ad">[ {{ message.messagable_type }} ]</span>
                            {{ message.title }}
                        </span>

                        <span 
                            v-if="message.status"
                            v-text="'Published'"
                        ></span>
                        <div v-if="!message.status && message.id">
                            <span 
                                v-if="sameMessagable(message)"
                                class="hidden group-hover:block cursor-pointer hover:text-blue-600 hover:underline"
                                v-text="'Unshedule'"
                                @click="messageUnschedule(message.id)"
                            ></span>
                            <Link 
                                v-else
                                :href="route(message.route, message.messagable_id)"
                                class="hidden group-hover:block cursor-pointer hover:text-blue-600 hover:underline"
                                v-text="'Edit'"
                            ></Link>
                        </div>
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
import { Link, usePage } from '@inertiajs/vue3';

const emitter = useEmitter();

defineProps({
    processing: {
        type: Boolean,
        default: false
    },
    isAd: {
        type: Boolean,
        default: false
    },
    canSchedule: {
        type: Boolean,
        default: false
    },
});

let scheduleStatus = ref(null);
const datePickerTimeAccuracy = ref(2); //1 => hour, 2 => minute, 3 => second
let choosenDate = ref((new Date).toDateString());
//let channel_id = ref(0);

const messages = ref([]);

const scheduleData = ref({
    publishAt: new Date,
    hoursOnTop: "1",
    removeAfterHours: "24",
});

const datePickerRules = ref({
    minutes: { interval: 5 },
});
const datePickerAttributes = ref([
    // {
    //     dot: 'blue',
    //     dates: [
    //         new Date(2024, 0, 19),
    //         new Date(2024, 0, 21),
    //         new Date(2024, 0, 26),
    //     ],
    // },
    // {
    //     dot: 'red',
    //     dates: [
    //         new Date(2024, 0, 18),
    //         new Date(2024, 0, 22),
    //         new Date(2024, 0, 27),
    //     ],
    // },    
]);

const datetimeToTime = (d) => 
    ('0' + d.getHours()).slice(-2) 
    + ':' + ('0' + d.getMinutes()).slice(-2); 

const updateSchedulesMessages = () => {
    const date = new Date(choosenDate.value).toISOString().slice(0, 10);
    
    axios.get(route('messages.date', date))
        .then((response) => {
            let arr = response.data.map((m) => {
                const type = m.messagable_type.replace('App\\Models\\', '');

                return {
                    id: m.id,
                    ad: m.ad,
                    ad_hours_on_top: m.ad_hours_on_top,
                    ad_remove_after_hours: m.ad_remove_after_hours,
                    ad_removed_at: m.ad_removed_at ? datetimeToTime(new Date(m.ad_removed_at)) : null,
                    ad_top_until: m.ad_top_until ? datetimeToTime(new Date(m.ad_top_until)) : null,
                    messagable_id: m.messagable_id,
                    messagable_type: type,
                    publish_at: datetimeToTime(new Date(m.publish_at)),
                    published_at: m.published_at ? datetimeToTime(new Date(m.published_at)) : null,
                    status: m.status,
                    title: m.messagable.title,
                    route: type === 'Poll' ? 'polls.edit' : 'posts.edit'
                };
            });

            if (usePage().props.channel.hours.length) {
                let timestampNow = new Date(new Date().toLocaleString('en', {timeZone: usePage().props.channel.timezone})).getTime();
                let d = date.split('-')

                usePage().props.channel.hours.forEach((hour) => {
                    let postDate = new Date(d[0], d[1]-1, d[2], hour, 0);

                    if (timestampNow > postDate.getTime()) {
                        return;
                    }

                    arr.push({
                        id: null,
                        ad: 0,
                        ad_hours_on_top: null,
                        ad_remove_after_hours: null,
                        ad_removed_at: null,
                        ad_top_until: null,
                        messagable_id: null,
                        messagable_type: 'Post',
                        publish_at: datetimeToTime(postDate),
                        published_at: datetimeToTime(postDate),
                        status: 0,
                        title: 'Channel auto-post',
                        route: null
                    });
                });

                arr.sort((a, b) => a.publish_at.replace(':', '') > b.publish_at.replace(':', '') ? 1 : -1)
            }

            messages.value = arr;
        });
}

const onDayClick = (date) => {
    choosenDate.value = date.id; //2024-01-26
    updateSchedulesMessages();       
}

const onDayTimeChange = (dateTime) => {
    scheduleData.value.publishAt = dateTime;
    scheduleStatus.value = null;
}

const schedule = () => {
    scheduleStatus.value = null;
    scheduleCreate();
}

const messageUnschedule = (id) => {
    if (! confirm('Are you sure?')) {
        return;
    }

    axios.delete(route('messages.destroy', {
        id: id
    }))
    .then((response) => {
        updateSchedulesMessages();     
    });
}

onMounted(() => {
    //channel_id.value = usePage().props.channel.id;
    updateSchedulesMessages();

    emitter.on('schedule.status', (data) => {
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

    // window.Echo.private('schedule.' + channel_id.value)
    //     //.listen('App\\Events\\ScheduledInChannel', (e) => {
    //     .listen('ScheduledInChannel', (e) => {
    //         updateSchedulesMessages();
    //     });
});

// onUnmounted(() => {
//     window.Echo.leave('schedule.' + channel_id.value);
// });

const scheduleCreate = () => {
    axios.post(route('messages.store'), {
        'schedulable_type': usePage().props.messagable_type,
        'schedulable_id': usePage().props.messagable_id,
        'publish_at': scheduleData.value.publishAt,
        'ad_hours_on_top': scheduleData.value.hoursOnTop,
        'ad_remove_after_hours': scheduleData.value.removeAfterHours,
    }).then((response) => {
        emitter.emit('schedule.status', {
            status: 'success', 
            messages: ['Post was scheduled.']
        });

        updateSchedulesMessages();
    }).catch((error) => {
        let errors;
        try {
            errors = error.response.data.errors;
        } catch (e) {
            errors = {error: ['An error occured. Please try later']};
        }

        let errErrors = [];
        for (let key in errors) {
            errErrors = [...errErrors, ...errors[key]];
        }

        emitter.emit('schedule.status', {
            status: 'error', 
            messages: errErrors
        });
    });
};

const sameMessagable = (message) => {
    return message.messagable_id === usePage().props.messagable_id 
        && message.messagable_type.toLowerCase() === usePage().props.messagable_type;
}

</script>