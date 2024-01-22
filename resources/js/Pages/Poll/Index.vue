<template>
    <Head title="Polls - List" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Polls</h2>
            <PrimaryButtonLink :href="route('polls.create')">Add new</PrimaryButtonLink>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <div v-if="!polls.data.length" class="p-3 italic">There is no polls so far</div>
                <div v-if="polls.data.length">
                    <div class="flex w-full bg-gray-100">
                        <div class="w-1/12 py-3 px-2">Type</div>
                        <div class="w-7/12 py-3 px-2">Title</div>
                        <div class="w-2/12 py-2 px-2">Created at</div>
                        <div class="w-2/12 py-2 px-2">Options</div>
                    </div>
                    <div 
                        v-for="poll in polls.data"
                        :key="poll.id"
                        class="flex w-full hover:bg-gray-100"
                    >
                        <div 
                            class="w-1/12 px-2 py-2"
                            v-text="poll.type"                        
                        ></div>
                        <div class="w-7/12">
                            <Link 
                                :href="route('polls.edit', poll.id)" 
                                v-text="poll.title"
                                class="block hover:underline hover:text-blue-600 px-3 py-2"
                            ></Link>
                        </div>
                        <div 
                            class="w-2/12 px-2 py-2"
                            v-text="formateDate(poll.created_at)"                        
                        ></div>
                        <div class="w-2/12 px-2 py-2 space-x-2">
                            <Link 
                                class="hover:underline hover:text-blue-600"
                                :href="route('polls.edit', poll.id)" 
                                text="Edit"
                            ></Link>
                            <Link 
                                :href="route('polls.destroy', poll.id)" 
                                method="delete"
                                as="button"
                                :onBefore="() => confirm('Are you sure?')"
                                class="hover:underline hover:text-blue-600"
                            >Delete</Link>
                        </div>
                    </div>
                </div>
            </div>
            <Pagination :meta="polls.meta" />
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
    polls: {
        type: Object
    }
})
const formateDate = (datetime) => formatDistance(parseISO(datetime), new Date) + ' ago';
const confirm = () => window.confirm("Are you sure?");
</script>