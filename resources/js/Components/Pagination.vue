<template>
  <div class="flex items-center justify-between bg-white px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <a :href="previousUrl()" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
      <a :href="nextUrl()" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          {{ ' ' }}
          <span class="font-medium" v-text="meta.from"></span>
          {{ ' ' }}
          to
          {{ ' ' }}
          <span class="font-medium" v-text="meta.to"></span>
          {{ ' ' }}
          of
          {{ ' ' }}
          <span class="font-medium" v-text="meta.total"></span>
          {{ ' ' }}
          results
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <div
            v-for="(link, key) in meta.links" :key="key"
            class="relative inline-flex items-center first-of-type:rounded-l-md last-of-type:rounded-r-md px-3 py-2"
                :class="{
                    'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600': link.active,
                    'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0': !link.active,
                    'text-gray-400': !link.url
                }"
          >
            <span 
              v-if="!link.url"  
              v-html="link.label"
            ></span>
            <Link 
                v-if="link.url"
                :href="link.url" 
                v-html="link.label"
            ></Link>
          </div>
        </nav>          
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { Link } from '@inertiajs/vue3';

  const props = defineProps({
    meta: {
        type: Object
    }
  })

  const previousUrl = () => props.meta.links[0].url
  const nextUrl = () => [...props.meta.links].reverse()[0].url
</script>