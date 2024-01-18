<template>
    <Head :title="'Video - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Video - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="preview" 
            :medias="filepaths"
            :show-signature="videoForm.show_signature"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:concept="onFormConcept"
        >
            <template #form>
                <form @submit.prevent="createVideo">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <div class="flex space-x-2">
                            <TextInput id="title" v-model="videoForm.title"/>
                            <div class="whitespace-nowrap flex items-center">
                                <Checkbox id="show_title" :checked="videoForm.show_title" @update:checked="updateShowTitle"/>
                                <InputLabel class="ml-2 cursor-pointer" for="show_title">Show</InputLabel>
                            </div>
                        </div>
                        <InputError :message="videoForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="title">Video (one MP4 video)</InputLabel>
                        <input type="hidden" name="filename" v-model="videoForm.filename"/>
                        <file-pond 
                            name="filename"
                            ref="pond"
                            label-idle="Click to choose image, or drag here..."
                            accepted-file-types="video/mp4"
                            @init="filepondInitialized"
                            @processfile="handleProcessedFile"
                            @removefile="handleRemoveFile"
                            allow-multiple="false" 
                            max-files="1" 
                            :files="videoForm.filepaths"
                        />                        
                        <InputError :message="videoForm.errors.filename" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="body">Body</InputLabel>
                        <TextArea id="body" v-model="videoForm.body" rows="10" />
                        <InputError :message="videoForm.errors.body" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="source">Source</InputLabel>
                        <TextInput id="source" v-model="videoForm.source"/>
                        <InputError :message="videoForm.errors.source" />
                    </div>

                    <div class="mb-3 flex space-x-2">
                        <Checkbox id="show_signature" :checked="videoForm.show_signature" @update:checked="updateShowSignature"/>
                        <InputLabel class="ml-2 cursor-pointer" for="show_signature">Show Channel signature</InputLabel>
                    </div>
                </form>
            </template>            
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script>
import vueFilePond, { setOptions } from 'vue-filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import 'filepond/dist/filepond.min.css';
import Checkbox from '@/Components/Checkbox.vue';

let serverMessage = {};

setOptions({
    server: {
        process: {
            url: '/videos/upload',
            onerror: (response) => {
                serverMessage = JSON.parse(response);
            },
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },
        revert: {
            url: '/videos/upload-undo',
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        }
    },
    labelFileProcessingError: () => {
        return serverMessage.error;
    }
});

const FilePond = vueFilePond(FilePondPluginFileValidateType);
export default {
    components: {
        FilePond
    }
}
</script>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

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
    video: {
        type: Object,
        default: null,
        required: false
    }
});

const videoForm = useForm({
    title: props.video.title,
    body: props.video.body,
    source: props.video.source,
    show_title: props.video.show_title,
    show_signature: props.video.show_signature,
    filename: props.video.filename,
    filepaths: props.video.filepaths,
    concept: false,  
})

let filepathInitial = null;
let filepaths = ref([]);
let preview = ref('');
const updateShowTitle = (val) => videoForm.show_title = val;
const updateShowSignature = (val) => videoForm.show_signature = val;

const updatePreview = () => {
    const title = videoForm.show_title 
        ? `<span class="text-base text-bold leading-4 block mr-8">${videoForm.title}</span><br />`
        : '';

    const source = videoForm.source.length 
        ? `<span class="block italic mt-2">${videoForm.source}</span><br />`
        : '';

    preview.value = 
        `<div class="relative">
            ${title}
            ${videoForm.body}<br />
            ${source}
        </div>`;
}

onMounted(updatePreview);

watch(    
    videoForm,
    updatePreview,
    { deep: true }
);

const updateFilepaths = (init = false) => {
    if (init) {
        filepathInitial = videoForm.filename;
        filepathInitial && filepaths.value.push('/storage/medias/' + usePage().props.channel.id + '/' + videoForm.filename);
        return;
    }
    
    filepaths.value = [];
    if (videoForm.filename) {
        filepaths.value.push((filepathInitial.indexOf(videoForm.filename) >=0 ? '/storage/medias/' + usePage().props.channel.id + '/' : '/storage/tmp/') + videoForm.filename);
    }
}

const createVideo = () => {
    if (props.video.id) { //update
        videoForm.patch(route(props.toRoute, props.video.id), {
            preserveScroll: true
        })
    } else { //create
        videoForm.post(route(props.toRoute), {
            preserveScroll: true
        })
    }
}

const filepondInitialized = (error, file) => {
    updateFilepaths(true);
}

const handleProcessedFile = (error, file) => {
    videoForm.filename = '';

    if (error) {
        console.error('Filepond Processed File', error);
        return;
    }
    
    videoForm.filename = file.serverId;
    updateFilepaths();
}

const handleRemoveFile = (error, file) => {
    if (error) {
        console.error('Filepond Remove File', error);
        return;
    }
    
    videoForm.filename = null;
    updateFilepaths();
}

const onFormSubmit = () => {
    if (videoForm.processing) {
        return;
    }
    videoForm.concept = false;
    createVideo();
}

const onFormCancel = () => {
    if (videoForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const onFormConcept = () => {
    if (videoForm.processing) {
        return;
    }
    videoForm.concept = true;
    createVideo();
    videoForm.concept = false;
}
</script>