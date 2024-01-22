<template>
    <Head :title="'Media Group - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Media Group - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="preview" 
            :hasMedia="filepaths.length > 0"
            :media="filepaths"
            :show-signature="groupForm.show_signature"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:concept="onFormConcept"
        >
            <template #form>
                <form @submit.prevent="createGroup">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <div class="flex space-x-2">
                            <TextInput id="title" v-model="groupForm.title"/>
                            <div class="whitespace-nowrap flex items-center">
                                <Checkbox id="show_title" :checked="groupForm.show_title" @update:checked="updateShowTitle"/>
                                <InputLabel class="ml-2 cursor-pointer" for="show_title">Show</InputLabel>
                            </div>
                        </div>
                        <InputError :message="groupForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="title">Media (up to 10 JPG or MP4)</InputLabel>
                        <input type="hidden" name="filenames[]" v-model="groupForm.filenames"/>
                        <file-pond 
                            name="filename"
                            ref="pond"
                            label-idle="Click to choose image, or drag here..."
                            accepted-file-types="video/mp4,image/jpeg"
                            @init="filepondInitialized"
                            @processfile="handleProcessedFile"
                            @removefile="handleRemoveFile"
                            @reorderfiles="handleReorderFiles"
                            allow-multiple="true" 
                            max-files="10" 
                            :allow-reorder="true"
                            :files="groupForm.filepaths"
                        />
                        <InputError :message="groupForm.errors.filenames" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="body">Body</InputLabel>
                        <TextArea id="body" v-model="groupForm.body" rows="10" />
                        <InputError :message="groupForm.errors.body" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="source">Source</InputLabel>
                        <TextInput id="source" v-model="groupForm.source"/>
                        <InputError :message="groupForm.errors.source" />
                    </div>

                    <div class="mb-3 flex space-x-2">
                        <Checkbox id="show_signature" :checked="groupForm.show_signature" @update:checked="updateShowSignature"/>
                        <InputLabel class="ml-2 cursor-pointer" for="show_signature">Show Channel signature</InputLabel>
                    </div> 
                </form>
            </template>            
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script>

</script>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
import Checkbox from '@/Components/Checkbox.vue';
import vueFilePond, { setOptions } from 'vue-filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import 'filepond/dist/filepond.min.css';
//import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
//import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

let serverMessage = {};

setOptions({
    server: {
        //upload file
        process: {
            url: '/media/upload',
            onerror: (response) => {
                serverMessage = JSON.parse(response);
            },
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },

        //delete uploaded file
        revert: {
            url: '/media/upload-undo',

            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },

        //preload existed files
        load: '/storage/media/' + usePage().props.channel.id + '/',
    },
    labelFileProcessingError: () => {
        return serverMessage.error;
    }
});

//const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);
const FilePond = vueFilePond(FilePondPluginFileValidateType);

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
    group: {
        type: Object,
        default: null,
        required: false
    },
});

const groupForm = useForm({
    title: props.group.title,
    body: props.group.body,
    source: props.group.source,
    show_title: props.group.show_title,
    show_signature: props.group.show_signature,
    filenames: props.group.filenames,
    filepaths: props.group.filepaths,
    concept: false,    
})

let filepathsInitial = [];
let filepaths = ref([]);
let filenameIds = ref([]);
let preview = ref('');
const updateShowTitle = (val) => groupForm.show_title = val;
const updateShowSignature = (val) => groupForm.show_signature = val;

const updatePreview = () => {
    const title = groupForm.show_title 
        ? `<span class="text-base text-bold leading-4 block mr-8">${groupForm.title}</span><br />`
        : '';

    const source = groupForm.source.length 
        ? `<span class="block italic mt-2">${groupForm.source}</span><br />`
        : '';

    preview.value = 
        `<div class="relative">
            ${title}
            ${groupForm.body}<br />
            ${source}
        </div>`;
}

onMounted(updatePreview);

watch(    
    groupForm,
    updatePreview,
    { deep: true }
);

const updateFilepaths = (init = false) => {
    if (init) {
        groupForm.filenames.length && groupForm.filenames.forEach(filename => {
            filepathsInitial.push(filename);
            filepaths.value.push('/storage/media/' + usePage().props.channel.id + '/' + filename);
        });
        return;
    }

    filepaths.value = [];
    groupForm.filenames.forEach(filename => {
        let path = filepathsInitial.indexOf(filename) >=0 
            ? '/storage/media/' + usePage().props.channel.id + '/' 
            : '/storage/tmp/';
        filepaths.value.push(path + filename);
    });
}

const filepondInitialized = () => {
    updateFilepaths(true);
}

const createGroup = () => {
    if (props.group.id) { //update
        groupForm.patch(route(props.toRoute, props.group.id), {
            preserveScroll: true
        })
    } else { //create
        groupForm.post(route(props.toRoute), {
            preserveScroll: true
        })
    }
}

const handleProcessedFile = (error, file) => {
    if (error) {
        console.error('Filepond Processed File', error);
        return;
    }
    
    filenameIds.value[file.id] = file.serverId;

    // prepend the new file
    console.log('uploaded', file);
    groupForm.filenames.unshift(file.serverId);
    updateFilepaths();
}

const handleRemoveFile = (error, removedFile) => {
    if (error) {
        console.error('Filepond Remove File', error);
        return;
    }
    
    const filename = removedFile.serverId ?? filenameIds.value[removedFile.id];

    //groupForm.filenames = groupForm.filenames.filter((file) => file !== removedFile.serverId);
    groupForm.filenames = groupForm.filenames.filter((file) => file !== filename);
    filenameIds.value[removedFile.id] && delete filenameIds.value[removedFile.id];
    updateFilepaths();
}

const handleReorderFiles = (files) => {
    groupForm.filenames = files.map((file) => file.serverId);
    updateFilepaths();
}

const onFormSubmit = () => {
    if (groupForm.processing) {
        return;
    }
    groupForm.concept = false;
    createGroup();
}

const onFormCancel = () => {
    if (groupForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const onFormConcept = () => {
    if (groupForm.processing) {
        return;
    }
    groupForm.concept = true;
    createGroup();
    groupForm.concept = false;
}
</script>