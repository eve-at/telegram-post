<template>
    <Head :title="'Video - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Video - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent :body="videoForm.body" :source="videoForm.source" :medias="filepaths">
            <template #form>
                <form @submit.prevent="createVideo">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <TextInput id="title" v-model="videoForm.title"/>
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

                    <div class="mb-3 flex justify-end space-x-3">
                        <PrimaryButton 
                            type="submit" 
                            :disable="videoForm.processing"
                        >
                            Submit
                        </PrimaryButton>
                        <SecondaryButtonLink :href="route('videos.index')">Cancel</SecondaryButtonLink>
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
    },
    // methods: {
    //     filepondInitialized() {
    //         console.log('Filepond is ready');
    //     }        
    // }
}
</script>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButtonLink from '@/Components/SecondaryButtonLink.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
    video: {
        type: Object,
        default: null,
        required: false
    },
    filename: {
        type: String,
        default: '',
    }
});

let filepathInitial = null;
let filepaths = ref([]);

const videoForm = useForm({
    title: props.video.title,
    body: props.video.body,
    source: props.video.source ?? '',
    filename: props.video.filename,
    filepaths: props.video.filepaths ?? [],
})

const updateFilepaths = (init = false) => {
    if (init) {
        filepathInitial = videoForm.filename;
        filepaths.value.push('/storage/medias/' + videoForm.filename);
        return;
    }
    
    filepaths.value = [];
    if (videoForm.filename) {
        filepaths.value.push((filepathInitial.indexOf(videoForm.filename) >=0 ? '/storage/medias/' : '/storage/tmp/') + videoForm.filename);
    }
}

const createVideo = () => {
    if (props.video.id) { //update
        videoForm.patch(route(props.toRoute, props.video.id), {
            preserveScroll: true,
            onSuccess: () => videoForm.reset(),
        })
    } else { //create
        videoForm.post(route(props.toRoute), {
            preserveScroll: true,
            onSuccess: () => videoForm.reset(),
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
</script>