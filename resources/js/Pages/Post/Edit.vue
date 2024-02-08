<template>
    <Head :title="'Post - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Post - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="preview" 
            :show-sidebar="true"
            :has-media="filepaths.length > 0"
            :media="filepaths"
            :show-signature="postForm.show_signature"
            :processing="postForm.processing"
            :form-was-saved="formWasSaved"
            :is-ad="postForm.ad"
            @form:save="onFormSave"
            @form:reset="onFormReset"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:concept="onFormConcept"
        >
            <template #form>
                <div 
                    v-if="postForm.ad"
                    class="w-full border border-dashed bg-orange-100 border-orange-300 text-orange-600 font-semibold rounded-md py-1 px-4 mb-4"
                >
                    (!) Advertisement
                </div>
                <form @submit.prevent="createPost">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <div class="flex space-x-2">
                            <TextInput id="title" v-model="postForm.title"/>
                            <div class="whitespace-nowrap flex items-center">
                                <Checkbox id="show_title" :checked="postForm.show_title" @update:checked="updateShowTitle"/>
                                <InputLabel class="ml-2 cursor-pointer" for="show_title">Show</InputLabel>
                            </div>
                        </div>
                        <InputError :message="postForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="title">Media (up to 10 JPG or MP4)</InputLabel>
                        <input type="hidden" name="filenames[]" v-model="postForm.filenames"/>
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
                            :files="postForm.filepaths"
                        />
                        <InputError :message="postForm.errors.filenames" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="body">Body</InputLabel>
                        <TextArea id="body" v-model="postForm.body" rows="6" />
                        <InputError :message="postForm.errors.body" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="source">Source</InputLabel>
                        <TextInput id="source" v-model="postForm.source"/>
                        <InputError :message="postForm.errors.source" />
                    </div>

                    <div class="mb-3 flex space-x-2">
                        <Checkbox id="show_signature" :checked="postForm.show_signature" @update:checked="updateShowSignature"/>
                        <InputLabel class="ml-2 cursor-pointer" for="show_signature">Show Channel signature</InputLabel>
                        <InputError :message="postForm.errors.show_signature" />
                    </div> 

                    <div class="mb-3 flex space-x-2">
                        <Checkbox 
                            id="ad" 
                            :checked="postForm.ad" 
                            :disabled="!!props.post.id" 
                            @update:checked="updateAd"
                            :class="{
                                'cursor-not-allowed': !!props.post.id
                            }"
                        />
                        <InputLabel 
                            for="ad"
                            class="ml-2"
                            :class="{
                                'cursor-pointer': !props.post.id,
                                'cursor-not-allowed': !!props.post.id
                            }"
                        >
                            Advertisement <span class="text-xs italic">(can't be changed)</span>
                        </InputLabel>
                        <InputError :message="postForm.errors.ad" />
                    </div> 

                    <div class="mb-3">
                        <InputLabel for="comment">Comment</InputLabel>
                        <TextArea id="comment" v-model="postForm.comment" rows="2" />
                        <InputError :message="postForm.errors.comment" />
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
import { ref, onUpdated, onMounted, onUnmounted, watch, reactive, computed } from 'vue';
import useEmitter from '@/Composables/useEmitter.js';
import axios from 'axios';

const emitter = useEmitter();
let serverMessage = {};

setOptions({
    server: {
        //upload file
        process: {
            url: route('posts.media.upload'),
            onerror: (response) => {
                serverMessage = JSON.parse(response);
            },
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },

        //delete uploaded file
        revert: {
            url: route('posts.media.upload-undo'),

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
    post: {
        type: Object,
        default: null,
        required: false
    },
});

const postForm = useForm({
    title: props.post.title,
    body: props.post.body,
    source: props.post.source,
    show_title: props.post.show_title,
    show_signature: props.post.show_signature,
    ad: props.post.ad,
    comment: props.post.comment,
    filenames: props.post.filenames,
    filepaths: props.post.filepaths,
    concept: false, // push the post to the test telegram channel
    comeback: false, // return after form submition
})

let filepathsInitial = [];
let filepaths = ref([]);
let filenameIds = ref([]);
let preview = ref('');
const updateShowTitle = (val) => postForm.show_title = val;
const updateShowSignature = (val) => postForm.show_signature = val;
const updateAd = (val) => postForm.ad = val;

const formWasSaved = computed(() => {
    return !! props.post.id && ! postForm.isDirty;
});

const updatePreview = () => {
    const title = postForm.show_title && postForm.title.length
        ? `<span class="text-base text-bold leading-4 block mr-8">${postForm.title}</span>`
        : '';

    const source = postForm.source.length 
        ? `<span class="block italic mt-2">${postForm.source}</span>`
        : '';

    preview.value = 
        `${title}
${postForm.body}
${source}`;
}

const modifiedFormHandler = () => {
    updatePreview();
};

onUpdated(() => {
    usePage().props.messagable_type = 'post';
    usePage().props.messagable_id = props.post.id;
});

onMounted(() => {
    updatePreview();
    usePage().props.messagable_type = 'post';
    usePage().props.messagable_id = props.post.id;
});

onUnmounted(() => {
    delete usePage().props.messagable_type;
    delete usePage().props.messagable_id;
});

watch(    
    postForm,
    modifiedFormHandler,
    { deep: true }
);

const updateFilepaths = (init = false) => {
    if (init) {
        postForm.filenames.length && postForm.filenames.forEach(filename => {
            filepathsInitial.push(filename);
            filepaths.value.push('/storage/media/' + usePage().props.channel.id + '/' + filename);
        });
        return;
    }

    filepaths.value = [];
    postForm.filenames.forEach(filename => {
        let path = filepathsInitial.indexOf(filename) >=0 
            ? '/storage/media/' + usePage().props.channel.id + '/' 
            : '/storage/tmp/';
        filepaths.value.push(path + filename);
    });
}

const filepondInitialized = () => {
    updateFilepaths(true);
}

const createPost = () => {
    if (props.post.id) { //update
        postForm.patch(route(props.toRoute, props.post.id), {
            preserveScroll: true,
        });
    } else { //create
        postForm.post(route(props.toRoute), {
            preserveScroll: true,
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
    postForm.filenames.unshift(file.serverId);
    updateFilepaths();
}

const handleRemoveFile = (error, removedFile) => {
    if (error) {
        console.error('Filepond Remove File', error);
        return;
    }
    
    const filename = removedFile.serverId ?? filenameIds.value[removedFile.id];

    //postForm.filenames = postForm.filenames.filter((file) => file !== removedFile.serverId);
    postForm.filenames = postForm.filenames.filter((file) => file !== filename);
    filenameIds.value[removedFile.id] && delete filenameIds.value[removedFile.id];
    updateFilepaths();
}

const handleReorderFiles = (files) => {
    postForm.filenames = files.map((file) => file.serverId);
    updateFilepaths();
}

const onFormSubmit = () => {
    if (postForm.processing) {
        return;
    }
    postForm.concept = false;
    createPost();
}

const onFormSave = () => {
    if (postForm.processing) {
        return;
    }
    postForm.concept = false;
    postForm.comeback = true;
    createPost();
}

const onFormReset = () => {
    if (postForm.processing) {
        return;
    }
    postForm.reset();
}

const onFormCancel = () => {
    if (postForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const onFormConcept = () => {
    if (postForm.processing) {
        return;
    }
    postForm.concept = true;
    createPost();
    postForm.concept = false;
}
</script>