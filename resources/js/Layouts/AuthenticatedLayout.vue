<template>
    <div>
        <div class="flex flex-col min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden sm:space-x-2 lg:space-x-8 sm:-my-px sm:ms-10 md:flex sm:items-center whitespace-nowrap">
                                <NavLink
                                    v-for="(link, index) in links"
                                    :key="index"
                                    :href="link.href" 
                                    :active="link.active()"
                                >
                                    {{ link.title }}
                                </NavLink>
                                <DropdownChannels />
                            </div>
                        </div>

                        <div class="hidden md:flex md:items-center">
                            <DropdownSettings />
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 mt-3 items-center md:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="md:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <DropdownChannels class="mx-auto" />
                        <ResponsiveNavLink 
                            v-for="(link, index) in links"
                            :key="index"
                            :href="link.href" 
                            :active="link.active()">
                            {{ link.title }}
                        </ResponsiveNavLink>                        
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow border-b border-gray-200" v-if="$slots.header">
                <div class="flex justify-between max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="">
                <Flash />
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUpdated } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import DropdownSettings from '@/Components/DropdownSettings.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Flash from '@/Components/Flash.vue'
import DropdownChannels from '@/Components/DropdownChannels.vue';

const showingNavigationDropdown = ref(false);

const page = usePage();

onUpdated(() => {
    setTimeout(() => page.props.flash = null, 4000);
});

const links = ref([
    { 
        title: 'Dashboard', 
        href: route('dashboard'), 
        active: () => route().current('dashboard') 
    },
    { 
        title: 'Messages', 
        href: route('messages.index'), 
        active: () => route().current().startsWith('messages') 
    },
    { 
        title: 'Groups', //Media 
        href: route('media.index'), 
        active: () => route().current().startsWith('media') 
    },
    { 
        title: 'Polls', 
        href: route('polls.index'), 
        active: () => route().current().startsWith('polls') 
    },
]);

</script>