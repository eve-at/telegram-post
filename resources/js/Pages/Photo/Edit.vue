<template>
    <Head :title="'Photo - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Photo - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent :body="photoForm.body" :source="photoForm.source">
            <template #form>
                <form @submit.prevent="createPhoto">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <TextInput id="title" v-model="photoForm.title"/>
                        <InputError :message="photoForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="title">Photo</InputLabel>
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

                    <div class="mb-3 flex justify-end space-x-3">
                        <PrimaryButton 
                            type="submit" 
                            :disable="photoForm.processing"
                        >
                            Submit
                        </PrimaryButton>
                        <SecondaryButtonLink :href="route('photos.index')">Cancel</SecondaryButtonLink>
                    </div>
                </form>
            </template>
            <template #media>
                <img v-if="photoForm.filename" 
                    :src="imagePath()" 
                    class="w-full"
                />
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
    },
    methods: {
        filepondInitialized() {
            console.log('Filepond is ready');
        }
        
    }
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
import { Head, useForm } from '@inertiajs/vue3';
import LayoutContent from '@/Components/LayoutContent.vue';

let photoUpdated = false;

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    toRoute: {
        type: String,
        required: true
    },
    photo: {
        type: Object,
        default: null,
        required: false
    },
    filename: {
        type: String,
        default: '',
    }
});

const photoForm = useForm({
    title: props.photo.title,
    body: props.photo.body,
    source: props.photo.source ?? '',
    filename: props.photo.filename,
    filepaths: props.photo.filepaths ?? [],
})

const imagePath = () => {
    if (!photoUpdated && props.photo.id) {
        return '/storage/medias/' + photoForm.filename;
    }
    return '/storage/tmp/' + photoForm.filename;
}

const createPhoto = () => {
    if (props.photo.id) { //update
        photoForm.patch(route(props.toRoute, props.photo.id), {
            preserveScroll: true,
            onSuccess: () => photoForm.reset(),
        })
    } else { //create
        photoForm.post(route(props.toRoute), {
            preserveScroll: true,
            onSuccess: () => photoForm.reset(),
        })
    }
}

const handleProcessedFile = (error, file) => {
    photoForm.filename = '';

    if (error) {
        console.error('Filepond Processed File', error);
        return;
    }
    
    photoUpdated = true;
    photoForm.filename = file.serverId;
}

const handleRemoveFile = (error, file) => {
    if (error) {
        console.error('Filepond Remove File', error);
        return;
    }
    photoUpdated = false;
    photoForm.filename = null;
}
</script>