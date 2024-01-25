<template>
    <div class="mx-auto w-10/12 flex flex-col">
        <div class="flex flex-col lg:flex-row p-3 bg-white border-gray-300 rounded-xl m-2">
            <div class="lg:w-7/12 lg:border-r lg:border-dotted pr-2 mr-2">
                <slot name="form" />

                <div class="pt-6 mb-3 flex justify-between">
                    <div>
                        <PrimaryButton 
                            @click.prevent="$emit('form:save')"
                            :disabled="processing"
                        >
                            Save
                        </PrimaryButton>
                    </div>
                    <div>
                        <SecondaryButton 
                            @click.prevent="$emit('form:reset')"
                            :disabled="processing"
                        >
                            Reset
                        </SecondaryButton>
                    </div>
                    <div>
                        <PrimaryButton 
                            v-if="conceptable" 
                            @click.prevent="$emit('form:concept')"
                            :disabled="processing"
                        >
                            Save & Test
                        </PrimaryButton>
                    </div>
                    <div class="space-x-3">
                        <PrimaryButton 
                            :disabled="processing"
                            @click.prevent="$emit('form:submit')"
                        >
                            Save & Leave
                        </PrimaryButton>
                        <SecondaryButton 
                            :disabled="processing"
                            @click.prevent="$emit('form:cancel')"
                        >
                            Cancel & Leave
                        </SecondaryButton>
                        <SecondaryButton 
                            @click.prevent="() => {
                                sidebarView='schedule'
                            }"
                        >
                            Schedule...
                        </SecondaryButton>
                    </div>
                    
                </div>
            </div>
            <div class="lg:w-5/12 relative">
                <div class="w-full">
                    <RadioGroup 
                        name="sidebarView"
                        v-model="sidebarView"
                        :options="sidebarOptions"
                        @change="sidebarViewChange($event)"
                    />
                </div>
                <div 
                    v-if="sidebarView === 'preview'"
                    class="lg:sticky top-1 w-72 mx-auto my-5 border-2 border-gray-500 rounded-xl shadow-lg overflow-hidden"
                >
                    <div v-if="hasMedia" 
                        class="min-h-28 bg-gray-200"
                        :class="{
                            'flex flex-wrap items-center justify-center': media.length > 1
                        }"
                    >
                        <div v-for="filepath in media" 
                            :class="{
                                'w-1/2': media.length > 1
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
                        <div v-if="showSignature" class="text-sm text-blue-500 italic font-semibold" v-html="signature ?? $page.props.channel.name"></div>
                        <img src="/images/post-footer.jpg" />
                    </div>
                </div>
                <div 
                    v-if="sidebarView === 'schedule'"
                    class="lg:sticky top-1 mx-auto my-5"
                >
                    <Schedule 
                        :canSchedule="canSchedule"
                        :processing="processing"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import PrimaryButton from './PrimaryButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import RadioGroup from '@/Components/RadioGroup.vue';
import Schedule from '@/Components/Schedule.vue'
import { ref } from 'vue';

const sidebarView = ref('preview');

const sidebarOptions = ref({
    'preview': 'Preview',
    'schedule': 'Schedule'
});

const sidebarViewChange = (e) => {
    console.log(e.target.value);
    sidebarView.value = e.target.value;
}

defineProps({
    body: {
        type: String,
        default: ''
    },
    hasMedia: {
        type: Boolean,
        default: true
    },
    showSignature: {
        type: Boolean,
        default: true
    },
    signature: {
        type: String,
        default: null,
    },
    media: {
        type: Array,
        default: []
    },
    conceptable: {
        type: Boolean,
        default: true
    },
    processing: {
        type: Boolean,
        default: false
    },
    canSchedule: {
        type: Boolean,
        default: false
    },
})

const isVideo = (filename) => filename.endsWith('.mp4');
</script>