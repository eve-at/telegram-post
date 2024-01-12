<template>
    <Head :title="'Media Group - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Media Group - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent :body="groupForm.body" :source="groupForm.source" :medias="filepaths">
            <template #form>
                <form @submit.prevent="createGroup">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <TextInput id="title" v-model="groupForm.title"/>
                        <InputError :message="groupForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="title">Medias (from 2 to 10 JPG or MP4)</InputLabel>
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

                    <div class="mb-3 flex justify-end space-x-3">
                        <PrimaryButton 
                            type="submit" 
                            :disable="groupForm.processing"
                        >
                            Submit
                        </PrimaryButton>
                        <SecondaryButtonLink :href="route('medias.index')">Cancel</SecondaryButtonLink>
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
//import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
//import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import { watch } from 'vue';

let serverMessage = {};

setOptions({
    server: {
        //upload file
        process: {
            url: '/medias/upload',
            onerror: (response) => {
                serverMessage = JSON.parse(response);
            },
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },

        //delete uploaded file
        revert: {
            url: '/medias/upload-undo',

            headers: {
                'X-CSRF-TOKEN': document.head.querySelector("meta[name='csrf-token']").content
            }
        },

        //preload existed files
        load: '/storage/medias/',
    },
    labelFileProcessingError: () => {
        return serverMessage.error;
    }
});

//const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);
const FilePond = vueFilePond(FilePondPluginFileValidateType);
export default {
    components: {
        FilePond
    },
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
import { ref, onMounted } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    toRoute: {
        type: String,
        required: true
    },
    group: {
        type: Object,
        default: null,
        required: false
    },
});

let filepathsInitial = [];
let filepaths = ref([]);

const groupForm = useForm({
    title: props.group.title,
    body: props.group.body,
    source: props.group.source ?? '',
    filenames: props.group.filenames ?? [],
    filepaths: props.group.filepaths ?? [],
})

const updateFilepaths = (init = false) => {
    if (init) {
        groupForm.filenames.forEach(filename => {
            filepathsInitial.push(filename);
            filepaths.value.push('/storage/medias/' + filename);
        });
        return;
    }

    filepaths.value = [];
    groupForm.filenames.forEach(filename => {
        let path = filepathsInitial.indexOf(filename) >=0 ? '/storage/medias/' : '/storage/tmp/';
        filepaths.value.push(path + filename);
    });
}

const filepondInitialized = () => {
    updateFilepaths(true);
}

const createGroup = () => {
    if (props.group.id) { //update
        groupForm.patch(route(props.toRoute, props.group.id), {
            preserveScroll: true,
            onSuccess: () => groupForm.reset(),
        })
    } else { //create
        groupForm.post(route(props.toRoute), {
            preserveScroll: true,
            onSuccess: () => groupForm.reset(),
        })
    }
}

const handleProcessedFile = (error, file) => {
    if (error) {
        console.error('Filepond Processed File', error);
        return;
    }
    
    // prepend the new file
    groupForm.filenames.unshift(file.serverId);
    updateFilepaths();
}

const handleRemoveFile = (error, removedFile) => {
    if (error) {
        console.error('Filepond Remove File', error);
        return;
    }

    groupForm.filenames = groupForm.filenames.filter((file) => file !== removedFile.serverId);
    updateFilepaths();
}

const handleReorderFiles = (files) => {
    groupForm.filenames = files.map((file) => file.serverId);
    updateFilepaths();
}

</script>