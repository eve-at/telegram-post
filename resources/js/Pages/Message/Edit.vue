<template>
    <Head :title="'Message - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Message - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="messagePreview" 
            :has-media="hasMedia" 
            :show-signature="props.message.messagable.show_signature"
            :conceptable="false"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
        >
            <template #form>
                <form @submit.prevent="createMessage">
                    <div class="mb-3">
                        <InputLabel for="body">Body</InputLabel>
                        <TextArea id="body" v-model="messageForm.body" rows="10" />
                        <InputError :message="messageForm.errors.body" />
                    </div>

                    <div 
                        class="mb-3" 
                        v-if="props.message.status"
                    >
                        <InputLabel>Published At</InputLabel>
                        <span>{{ props.message.published_at }}</span>
                    </div>
                    <div 
                        class="mb-3" 
                        v-if="!props.message.status"
                    >
                        <InputLabel>Publish At</InputLabel> 
                        <DatePicker 
                            v-model="props.message.published_at" 
                            mode="dateTime" 
                            :time-accuracy="timeAccuracy"
                            is24hr 
                            :first-day-of-week="2"
                            :min-date="(new Date)"
                        />
                        <InputError :message="messageForm.errors.published_at" />
                    </div>
                </form>
            </template>
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { Calendar, DatePicker } from 'v-calendar';
import 'v-calendar/dist/style.css';

const timeAccuracy = ref(2); //1 => hour, 2 => minute, 3 => second
let messagePreview = ref('');

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
    message: {
        type: Object,
        default: null,
        required: false
    }
});

const hasMedia = computed(() => {
    return [
        'App\\Models\\Photo',
        'App\\Models\\Video',
        'App\\Models\\MediaGroup',
    ].includes(props.message.messagable_type);
});

const messageForm = useForm({
    title: props.message.title,
    body: props.message.body,
    //
})

const updateShowTitle = (val) => messageForm.show_title = val;
const updateShowSignature = (val) => messageForm.show_signature = val;

const updateMessagePreview = () => {
    //
}

onMounted(updateMessagePreview);

watch(    
    messageForm,
    updateMessagePreview,
    { deep: true }
);

const createMessage = () => {
    if (props.message.id) { //update
        messageForm.patch(route(props.toRoute, props.message.id), {
            preserveScroll: true
        })
    } else { //create
        messageForm.post(route(props.toRoute), {
            preserveScroll: true
        })
    }
}

const onFormSubmit = () => {
    if (messageForm.processing) {
        return;
    }
    createMessage();
}

const onFormCancel = () => {
    if (messageForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}
</script>