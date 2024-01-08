<template>
    <Head title="Post - Create" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post - Create</h2>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <form @submit.prevent="createPost">
                    <InputLabel for="title">Title</InputLabel>
                    <TextInput id="title" v-model="postForm.title"/>
                    <InputError :message="postForm.errors.title" />
                    
                    <InputLabel for="type">Type</InputLabel>
                    
                    <InputLabel for="body">Body</InputLabel>
                    <TextArea id="body" v-model="postForm.body" />
                    <InputError :message="postForm.errors.body" />

                    <PrimaryButton type="submit" :disable="postForm.processing">Submit</PrimaryButton>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const postForm = useForm({
    title: '',
    body: '',
})

const createPost = () => postForm.post(route(post.store), {
    preserveScroll: true,
    onSuccess: () => postForm.reset(),
})
</script>