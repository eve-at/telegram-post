<template>
    <Head title="Posts - List" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Posts</h2>
            <!-- TODO: filters -->
            <span>Scheduled</span>
            <span>Waiting</span>
            <span>Published</span>
            <span>All</span>
            <PrimaryButtonLink :href="route('posts.create')">Add new</PrimaryButtonLink>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid divide-gray-300 overflow-hidden">
                <div v-if="!posts.data.length" class="p-3 italic">There is no posts so far</div>
                <div v-if="posts.data.length">
                    <div class="flex w-full bg-gray-100">
                        <div class="w-1/12 py-3 px-2">Type</div>
                        <div class="w-7/12 py-3 px-2">Title</div>
                        <div class="w-2/12 py-2 px-2">Created at</div>
                        <div class="w-2/12 py-2 px-2">Options</div>
                    </div>
                    <div 
                        v-for="post in posts.data"
                        :key="post.id"
                        class="flex w-full border-t border-gray-100"
                        :class="{
                            'hover:bg-gray-100': ! post.ad,
                            'bg-orange-100 hover:bg-orange-200': post.ad,
                        }"
                    >
                        <div 
                            class="w-1/12 px-2 py-2"
                            v-text="post.type"                        
                        ></div>
                        <div class="w-7/12 ">
                            <Link 
                                :href="route('posts.edit', post.id)" 
                                v-text="post.title"
                                class="block hover:underline hover:text-blue-600 px-3 py-2"
                            ></Link>
                        </div>
                        <div 
                            class="w-2/12 px-2 py-2"
                            v-text="formateDate(post.created_at)"                        
                        ></div>
                        <div class="w-2/12 px-2 py-2 space-x-2">
                            <Link 
                                class="hover:underline hover:text-blue-600"
                                :href="route('posts.edit', post.id)" 
                                text="Edit"
                            ></Link>
                            <Link 
                                :href="route('posts.destroy', post.id)" 
                                method="delete"
                                as="button"
                                :onBefore="() => confirm('Are you sure?')"
                                class="hover:underline hover:text-blue-600"
                            >Delete</Link>
                        </div>
                    </div>
                </div>
            </div>
            <Pagination :meta="posts.meta" />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { formatDistance, parseISO } from 'date-fns';
import PrimaryButtonLink from '@/Components/PrimaryButtonLink.vue';
defineProps({
    posts: {
        type: Object
    }
})
const formateDate = (datetime) => formatDistance(parseISO(datetime), new Date) + ' ago';
const confirm = () => window.confirm("Are you sure?");
</script>