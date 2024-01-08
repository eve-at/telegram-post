<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue'
defineProps({
    posts: {
        type: Object
    }
})
</script>

<template>
    <Head title="Posts" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Posts</h2>
        </template>

        <div class="w-11/12 flex bg-white overflow-hidden flex-col">
            <div class="border border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <div class="flex w-full bg-gray-100">
                    <div class="w-1/12 py-2 pl-3">Type</div>
                    <div class="w-9/12 py-2 px-2">Title</div>
                    <div class="w-2/12 py-2 px-2">Created at</div>
                    <div class="w-1/12 py-2 px-2">Options</div>
                </div>
                <div v-if="!posts.data" class="p-3">There is no posts so far</div>
                <div 
                    v-if="posts.data"
                    v-for="post in posts.data"
                    :key="post.id"
                    class="flex w-full hover:bg-gray-100"
                >
                    <div class="w-1/12 pl-3 py-2"
                        v-text="post.type"
                    ></div>    
                    <div class="w-9/12 px-2 py-2">
                        <Link 
                            :href="'/posts/' + post.id" 
                            v-text="post.title"
                            class="hover:underline hover:text-blue-600"
                        ></Link>
                    </div>
                    <div 
                        class="w-2/12 px-2 py-2"
                        v-text="post.created_at.slice(0, 10)"
                    ></div>
                    <div class="w-1/12 px-2 py-2 space-x-2">
                        <Link 
                            class="hover:underline hover:text-blue-600"
                            :href="'/posts/' + post.id" 
                            text="Edit"
                        ></Link>
                        <Link 
                            class="hover:underline hover:text-blue-600"
                            :href="'/posts/' + post.id" 
                            text="Delete"
                        ></Link>
                    </div>
                </div>
            </div>

            <Pagination :meta="posts.meta" />
        </div>
    </AuthenticatedLayout>
</template>