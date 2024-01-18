<template>
    <Head :title="'Photo - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Photo - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="preview" 
            :medias="filepaths" 
            :show-signature="photoForm.show_signature"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:concept="onFormConcept"
        >
            <template #form>
                <form @submit.prevent="createPhoto">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <div class="flex space-x-2">
                            <TextInput id="title" v-model="photoForm.title"/>
                            <div class="whitespace-nowrap flex items-center">
                                <Checkbox id="show_title" :checked="photoForm.show_title" @update:checked="updateShowTitle"/>
                                <InputLabel class="ml-2 cursor-pointer" for="show_title">Show</InputLabel>
                            </div>
                        </div>
                        <InputError :message="photoForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="title">Photo (one JPG image)</InputLabel>
                        <input type="hidden" name="filename" v-model="photoForm.filename"/>
                        <file-pond 
                            name="filename"
                            ref="pond"
                            label-idle="Click to choose image, or drag here..."
                            accepted-file-types="image/jpeg"
                            @init="filepondInitialized"
                            @processfile="handleProcessedFile"
                            @removefile="handleRemoveFile"
                            allow-multiple="false" 
                            max-files="1" 
                            :files="photoForm.filepaths"
                        />
                        <InputError :message="photoForm.errors.filename" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="body">Body</InputLabel>
                        <TextArea id="body" v-model="photoForm.body" rows="10" />
                        <InputError :message="photoForm.errors.body" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="source">Source</InputLabel>
                        <TextInput id="source" v-model="photoForm.source"/>
                        <InputError :message="photoForm.errors.source" />
                    </div>

                    <div class="mb-3 flex space-x-2">
                        <Checkbox id="show_signature" :checked="photoForm.show_signature" @update:checked="updateShowSignature"/>
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
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import Checkbox from '@/Components/Checkbox.vue';

let serverMessage = {};

setOptions({
    server: {
        process: {
            url: '/photos/upload',
            onerror: (response) => {
                serverMessage = JSON.parse(response);
            },
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },
        revert: {
            url: '/photos/upload-undo',
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        }
    },
    labelFileProcessingError: () => {
        return serverMessage.error;
    }
});

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);
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
    photo: {
        type: Object,
        default: null,
        required: true
    }
});

const photoForm = useForm({
    title: props.photo.title,
    body: props.photo.body,
    show_title: props.photo.show_title,
    show_signature: props.photo.show_signature,
    source: props.photo.source,
    filename: props.photo.filename,
    filepaths: props.photo.filepaths,
    concept: false,    
})

let filepathInitial = null;
let filepaths = ref([]);
let preview = ref('');
const updateShowTitle = (val) => photoForm.show_title = val;
const updateShowSignature = (val) => photoForm.show_signature = val;

const updatePreview = () => {
    const title = photoForm.show_title 
        ? `<span class="text-base text-bold leading-4 block mr-8">${photoForm.title}</span><br />`
        : '';

    const source = photoForm.source.length 
        ? `<span class="block font-mono mt-3">${photoForm.source}</span><br />`
        : '';

    preview.value = 
        `<div class="relative">
            ${title}
            ${photoForm.body}<br />
            ${source}
        </div>`;
}

onMounted(updatePreview);

watch(    
    photoForm,
    updatePreview,
    { deep: true }
);

const updateFilepaths = (init = false) => {
    if (init) {
        filepathInitial = photoForm.filename;
        filepathInitial && filepaths.value.push('/storage/medias/' + usePage().props.channel.id + '/' + photoForm.filename);
        return;
    }
    
    filepaths.value = [];
    if (photoForm.filename) {
        filepaths.value.push((filepathInitial.indexOf(photoForm.filename) >=0 ? '/storage/medias/' + usePage().props.channel.id + '/' : '/storage/tmp/') + photoForm.filename);
    }
}

const createPhoto = () => {
    if (props.photo.id) { //update
        photoForm.patch(route(props.toRoute, props.photo.id), {
            preserveScroll: true
        })
    } else { //create
        photoForm.post(route(props.toRoute), {
            preserveScroll: true
        })
    }
}

const filepondInitialized = (error, file) => {
    updateFilepaths(true);
}

const handleProcessedFile = (error, file) => {
    photoForm.filename = '';

    if (error) {
        console.error('Filepond Processed File', error);
        return;
    }
    
    photoForm.filename = file.serverId;
    updateFilepaths();
}

const handleRemoveFile = (error, file) => {
    if (error) {
        console.error('Filepond Remove File', error);
        return;
    }
    
    photoForm.filename = null;
    updateFilepaths();
}

const onFormSubmit = () => {
    if (photoForm.processing) {
        return;
    }
    photoForm.concept = false;
    createPhoto();
}

const onFormCancel = () => {
    if (photoForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const onFormConcept = () => {
    if (photoForm.processing) {
        return;
    }
    photoForm.concept = true;
    createPhoto();
    photoForm.concept = false;
}
</script>