<template>
<div 
    v-if="$page.props.channel.list"
    class="inline-flex items-center px-1 pt-1 justify-center"
>
    <Dropdown align="right" width="40">
        <template #trigger>
            <span class="inline-flex border rounded-md border-gray-300">
                <button
                    type="button"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                >
                    <!-- <span class="hidden lg:inline-block">Channel:&nbsp;</span> -->
                    <span class="max-w-36 overflow-hidden whitespace-nowrap" 
                        v-text="$page.props.channel.name"
                    ></span>

                    <svg
                        class="ms-2 -me-0.5 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <DropdownLink 
                v-for="channel in $page.props.channel.list"
                method="patch"
                :href="route('session_channel.update', channel.id)"
                :active="$page.props.channel.id === channel.id"
            >{{ channel.name }}</DropdownLink>
        </template>
    </Dropdown>
</div>
</template>

<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();


</script>