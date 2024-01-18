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
            :medias="[]"
            :show-signature="showSignature"
            :signature="channelForm.signature"
            :conceptable="false"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
        >
            <template #form>
                <span 
                    v-if="!props.channel.id"
                    class="bg-gray-200 block py-2 px-3 mt-2 mb-4 italic rounded-md"
                >
                    Note: Before filling out this form, create the channel through the Telegram application.
                </span>
                <form @submit.prevent="createChannel">
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
                </form>
            </template>            
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
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
    channel: {
        type: Object,
        default: null,
        required: false
    },
});

const channelForm = useForm({
    name: props.channel.name,
    chat_id: props.channel.chat_id,
    signature: props.channel.signature,    
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
    channelForm.concept = false;
    createChannel();
}

const onFormCancel = () => {
    if (channelForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const createChannel = () => {
    if (props.channel.id) { //update
        channelForm.patch(route(props.toRoute, props.channel.id), {
            preserveScroll: true
        })
    } else { //create
        channelForm.post(route(props.toRoute), {
            preserveScroll: true
        })
    }
}
</script>