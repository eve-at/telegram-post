<template>
    <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
        <div class="flex flex-col lg:flex-row p-3 bg-white border-gray-300 rounded-xl m-2 overflow-hidden">
            <div class="lg:w-7/12 lg:border-r lg:border-dotted pr-2 mr-2">
                <slot name="form" />
            </div>
            <div class="lg:w-5/12">
                <div class="w-72 mx-auto mt-5 border-2 border-gray-500 rounded-xl overflow-hidden shadow-lg">
                    <div v-if="medias !== false" 
                        class="min-h-28 bg-gray-200"
                        :class="{
                            'flex flex-wrap items-center justify-center': medias.length > 1
                        }"
                    >
                        <div v-for="filepath in medias" 
                            :class="{
                                'w-1/2': medias.length > 1
                            }"
                        >
                            <div class="flex-1 overflow-hidden">
                                <img v-if="!isVideo(filepath)" 
                                    :src="filepath"
                                    class="min-h-28"
                                />
                                <video v-if="isVideo(filepath)" 
                                    :src="filepath" 
                                    controls muted
                                    class="min-h-28"
                                >
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2 p-2">
                        <div v-if="body.length" v-html="body" class="text-xs"></div>
                        <div v-if="source.length" 
                            v-html="source" 
                            class="text-xs font-mono">
                        </div>
                        <div class="text-sm text-blue-500 italic font-semibold">Channel link</div>
                        <img src="/images/post-footer.jpg" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    body: {
        type: String,
        default: ''
    },
    source: {
        type: String,
        default: ''
    },
    medias: {
        type: Array,
        default: []
    },
})

const isVideo = (filename) => filename.endsWith('.mp4');
</script>