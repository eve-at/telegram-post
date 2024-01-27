<template>
    <Head :title="'Channel - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Channel - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="bodyTemplate" 
            :show-sidebar="false"
            :media="[]"
            :show-signature="showSignature"
            :signature="channelForm.signature"
            :conceptable="false"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:save="onFormSave"
            @form:reset="onFormReset"
        >
            <template #form>
                <span 
                    v-if="!props.theChannel.id"
                    class="bg-gray-200 block py-2 px-3 mt-2 mb-4 italic rounded-md"
                >
                    Note: Before filling out this form, create the channel through the Telegram application.
                </span>
                <form @submit.prevent="createChannel">
                    <div class="flex">
                        <div class="w-9/12">
                            <div class="mb-3">
                                <InputLabel for="name">Name*</InputLabel>
                                <TextInput id="name" v-model="channelForm.name"/>
                                <InputError :message="channelForm.errors.name" />
                            </div>

                            <div class="mb-3">
                                <InputLabel for="chat_id">Chat ID*</InputLabel>
                                <TextInput id="chat_id" v-model="channelForm.chat_id"/>
                                <InputError :message="channelForm.errors.chat_id" />
                            </div>

                            <div class="mb-3">
                                <InputLabel for="signature">Message signature</InputLabel>
                                <TextInput id="signature" v-model="channelForm.signature"/>
                                <InputError :message="channelForm.errors.signature" />
                            </div>

                            <div class="mb-3">
                                <InputLabel for="timezones">Timezone</InputLabel>
                                <Select id="timezones" v-model="channelForm.timezone" :options="props.timezones"/>
                                <InputError :message="channelForm.errors.timezone" />
                            </div>
                        </div>
                        <div class="w-3/12 flex flex-wrap pl-4">
                            <div class="w-full">
                                <InputLabel for="">Auto-posting hours</InputLabel>
                            </div>
                            <div class="w-1/2">
                                <div 
                                    v-for="hour in [...Array(12).keys()]"
                                    class="font-mono space-x-1 whitespace-nowrap flex items-center"
                                >
                                    <Checkbox 
                                        :id="'hour' + hour" 
                                        :value="hour"
                                        :checked="channelForm.hours.includes(hour)"
                                        @update:checked="checkHour(hour, $event)"
                                    />
                                    <InputLabel 
                                        class="ml-2 cursor-pointer" 
                                        :for="'hour' + hour"
                                    >
                                        {{ ('0'+hour).slice(-2) }}
                                    </InputLabel>
                                </div>
                            </div>
                            <div class="w-1/2">
                                <div 
                                    v-for="hour in [...Array(12).keys()].map(i => 12 + i)"
                                    class="font-mono space-x-1 whitespace-nowrap flex items-center"
                                >
                                    <Checkbox 
                                        :id="'hour' + hour" 
                                        :value="hour"
                                        :checked="channelForm.hours.includes(hour)" 
                                        @update:checked="checkHour(hour, $event)"
                                    />
                                    <InputLabel 
                                        class="ml-2 cursor-pointer" 
                                        :for="'hour' + hour"
                                    >
                                        {{ ('0'+hour).slice(-2) }}
                                    </InputLabel>
                                </div>
                            </div>
                            <div class="w-full pt-2">
                                <InputError :message="channelForm.errors.hours" />
                            </div>
                        </div>
                    </div>
                </form>
            </template>            
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Select from '@/Components/Select.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    toRoute: {
        type: String,
        required: true
    },
    cancelRoute: {
        type: String,
        required: true
    },
    theChannel: {
        type: Object,
        default: null,
        required: false
    },
    timezones: {
        type: Object,
        required: true
    },
});

const channelForm = useForm({
    name: props.theChannel.name,
    chat_id: props.theChannel.chat_id,
    signature: props.theChannel.signature,    
    hours: props.theChannel.hours,    
    timezone: props.theChannel.timezone,    
    comeback: false, // return after form submition
})

const showSignature = ref(true);
const bodyTemplate = `<div class="relative">
    <span class="text-base text-bold block mr-8">Post title</span><br /><br />
    <span class="italic text-gray-400 p-2">< Post body ><span><br /><br />           
</div>`;

const onFormSubmit = () => {
    if (channelForm.processing) {
        return;
    }
    createChannel();
}

const onFormSave = () => {
    if (channelForm.processing) {
        return;
    }
    channelForm.comeback = true;
    createChannel();
}

const onFormReset = () => {
    if (channelForm.processing) {
        return;
    }
    channelForm.reset();
}

const onFormCancel = () => {
    if (channelForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const createChannel = () => {
    if (props.theChannel.id) { //update
        channelForm.patch(route(props.toRoute, props.theChannel.id), {
            preserveScroll: true
        })
    } else { //create
        channelForm.post(route(props.toRoute), {
            preserveScroll: true
        })
    }
}

const checkHour = (hour, checked) => {
    channelForm.hours = checked 
        ? [...channelForm.hours, hour] 
        : channelForm.hours.filter(h => h !== hour);
}
</script>