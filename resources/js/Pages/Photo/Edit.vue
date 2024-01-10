<template>
    <Head :title="'Photo - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Photo - ' + $props.title"
            ></h2>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="p-3 border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <form @submit.prevent="createPhoto" class="w-6/12">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <TextInput id="title" v-model="photoForm.title"/>
                        <InputError :message="photoForm.errors.title" />
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButtonLink from '@/Components/SecondaryButtonLink.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

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
    }
});

const photoForm = useForm({
    title: props.photo.title,
    body: props.photo.body,
    source: props.photo.source,
})

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
</script>