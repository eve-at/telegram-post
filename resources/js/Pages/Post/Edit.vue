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
            :body="postPreview" 
            :has-medias="false" 
            :show-signature="postForm.show_signature"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:concept="onFormConcept"
        >
            <template #form>
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
                        <InputLabel for="body">Body</InputLabel>
                        <TextArea id="body" v-model="postForm.body" rows="10" />
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
                    </div>

                    <!-- <div class="pt-6 mb-3 flex justify-between">
                        <div class="space-x-3">
                            <PrimaryButton 
                                type="submit" 
                                :disable="postForm.processing"
                            >
                                Submit
                            </PrimaryButton>
                            <SecondaryButtonLink :href="route('posts.index')">Cancel</SecondaryButtonLink>
                        </div>
                        <div class="">
                            <PrimaryButton 
                                type="button" 
                                @click.prevent="saveAndTest"
                                :disable="postForm.processing"
                            >
                                Submit & Test
                            </PrimaryButton>
                        </div>
                    </div> -->
                </form>
            </template>
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButtonLink from '@/Components/SecondaryButtonLink.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

let postPreview = ref('');

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
    }
});

const postForm = useForm({
    title: props.post.title,
    show_title: props.post.show_title,
    body: props.post.body,
    show_signature: props.post.show_signature,
    source: props.post.source,
    concept: false,    
})

const updateShowTitle = (val) => postForm.show_title = val;
const updateShowSignature = (val) => postForm.show_signature = val;

const updatePostPreview = () => {
    const title = postForm.show_title 
        ? `<span class="text-base text-bold leading-4 block mr-8">${postForm.title}</span><br />`
        : '';

    const source = postForm.source.length 
        ? `<span class="block italic mt-2">${postForm.source}</span><br />`
        : '';

    postPreview.value = 
        `<div class="relative">
            ${title}
            ${postForm.body}<br />
            ${source}
        </div>`;
}

onMounted(updatePostPreview);

watch(    
    postForm,
    updatePostPreview,
    { deep: true }
);

const createPost = () => {
    if (props.post.id) { //update
        postForm.patch(route(props.toRoute, props.post.id), {
            preserveScroll: true,
            //onSuccess: () => postForm.reset(),
        })
    } else { //create
        postForm.post(route(props.toRoute), {
            preserveScroll: true,
            //onSuccess: () => postForm.reset(),
        })
    }
}

const onFormSubmit = () => {
    if (postForm.processing) {
        return;
    }
    postForm.concept = false;
    createPost();
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