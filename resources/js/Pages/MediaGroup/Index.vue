<template>
    <Head title="Media Group - List" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Media Group</h2>
            <PrimaryButtonLink :href="route('medias.create')">Add new</PrimaryButtonLink>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <div class="flex w-full bg-gray-100">
                    <div class="w-8/12 py-3 px-2">Title</div>
                    <div class="w-2/12 py-2 px-2">Created at</div>
                    <div class="w-2/12 py-2 px-2">Options</div>
                </div>
                <div v-if="!medias.data" class="p-3">There is no media so far</div>
                <div 
                    v-for="media in medias.data"
                    :key="media.id"
                    class="flex w-full hover:bg-gray-100"
                >
                    <div class="w-8/12 ">
                        <Link 
                            :href="`/medias/${media.id}/edit`" 
                            v-text="media.title"
                            class="block hover:underline hover:text-blue-600 px-3 py-2"
                        ></Link>
                    </div>
                    <div 
                        class="w-2/12 px-2 py-2"
                        v-text="formateDate(media.created_at)"                        
                    ></div>
                    <div class="w-2/12 px-2 py-2 space-x-2">
                        <Link 
                            class="hover:underline hover:text-blue-600"
                            :href="`/medias/${media.id}/edit`" 
                            text="Edit"
                        ></Link>
                        <Link 
                            :href="route('medias.destroy', media.id)" 
                            method="delete"
                            as="button"
                            :onBefore="() => confirm('Are you sure?')"
                            class="hover:underline hover:text-blue-600"
                        >Delete</Link>
                    </div>
                </div>
            </div>

            <Pagination :meta="medias.meta" />
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
    medias: {
        type: Object
    }
})
const formateDate = (datetime) => formatDistance(parseISO(datetime), new Date) + ' ago';
const confirm = () => window.confirm("Are you sure?");
</script>