<template>
    <Head :title="'Post - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Post - ' + $props.title"
            ></h2>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="p-3 border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <form @submit.prevent="createPost" class="w-6/12">
                    <div class="mb-3">
                        <InputLabel for="title">Title</InputLabel>
                        <TextInput id="title" v-model="postForm.title"/>
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

                    <div class="mb-3 flex justify-end space-x-3">
                        <PrimaryButton 
                            type="submit" 
                            :disable="postForm.processing"
                        >
                            Submit
                        </PrimaryButton>
                        <SecondaryButtonLink :href="route('post.index')">Cancel</SecondaryButtonLink>
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
    post: {
        type: Object,
        default: null,
        required: false
    }
});

const postForm = useForm({
    title: props.post.title,
    body: props.post.body,
    source: props.post.source,
})

// onMounted(() => {
//     if (props.post) {
//         postForm.title = props.post.title;
//         postForm.body = props.post.body;
//         postForm.source = props.post.source;
//     }
// })

const createPost = () => {
    if (props.post.id) { //update
        postForm.patch(route(props.toRoute, props.post.id), {
            preserveScroll: true,
            onSuccess: () => postForm.reset(),
        })
    } else { //create
        postForm.post(route(props.toRoute), {
            preserveScroll: true,
            onSuccess: () => postForm.reset(),
        })
    }
}
</script>