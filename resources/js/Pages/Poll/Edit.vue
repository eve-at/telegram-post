<template>
    <Head :title="'Poll - ' + (props.id ? 'Edit' : 'Create')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Poll - ' + (props.id ? 'Edit' : 'Create')"
            ></h2>
        </template>

        <LayoutContent :body="postBody" :medias="false">
            <template #form>
                <form @submit.prevent="createPoll">
                    <div class="mb-3">
                        <InputLabel for="title">Title*</InputLabel>
                        <TextInput id="title" v-model="pollForm.title"/>
                        <InputError :message="pollForm.errors.title" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="type">Type*</InputLabel>
                        <Select :options="props.types" v-model="pollForm.type"/>
                        <InputError :message="pollForm.errors.type" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="options">Options* (2-10 options)</InputLabel>
                        <!--  -->
                        <draggable v-model="pollForm.options" item-key="id" @end="onDragEnd">
                            <template #header>
                                <div class="flex border-t border-l border-r rounded-t-md bg-gray-300">
                                    <div class="hidden"></div>
                                    <div v-if="pollForm.type==='quiz'" class="w-2/12 text-center p-2">
                                        Answer
                                    </div>
                                    <div class="p-2"
                                        :class="{
                                            'w-9/12': pollForm.type==='quiz',
                                            'w-10/12': pollForm.type!=='quiz',
                                        }"
                                    >
                                        Option
                                    </div>
                                    <div class="w-2/12 text-center pt-2">
                                        <span v-if="pollForm.options.length>2">Options</span>
                                    </div>
                                </div>
                            </template>
                            <template #item="{ element, index }">
                                <div class="flex divide-y border-x last-of-type:border-b border-gray-300 hover:bg-gray-100 last-of-type:rounded-b-md cursor-move">
                                    <div class="hidden"></div>
                                    <div v-if="pollForm.type==='quiz'" class="w-2/12 text-center p-2">
                                        <input type="radio" v-model="pollForm.answer" :value="index" /> 
                                    </div>
                                    <div class="p-2"
                                        :class="{
                                            'w-9/12': pollForm.type==='quiz',
                                            'w-10/12': pollForm.type!=='quiz',
                                        }"
                                    >
                                        {{ element }}
                                    </div>
                                    <div class="w-2/12 text-center">
                                        <span 
                                            v-if="pollForm.options.length>2"
                                            @click="deleteOption(index)"
                                            class="cursor-pointer hover:underline text-blue-500 block pt-2"
                                        >Delete</span>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                        <InputError :message="pollForm.errors.options" />
                        <InputError :message="pollForm.errors.answer" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="explanation">Explanation</InputLabel>
                        <TextInput id="explanation" v-model="pollForm.explanation"/>
                        <InputError :message="pollForm.errors.explanation" />
                    </div>

                    <div class="mb-3 flex justify-end space-x-3">
                        <PrimaryButton 
                            type="submit" 
                            :disable="pollForm.processing"
                        >
                            Submit
                        </PrimaryButton>
                        <SecondaryButtonLink :href="route('polls.index')">Cancel</SecondaryButtonLink>
                    </div>
                </form>
            </template>
        </LayoutContent>
    </AuthenticatedLayout>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButtonLink from '@/Components/SecondaryButtonLink.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import LayoutContent from '@/Components/LayoutContent.vue';
//https://sortablejs.github.io/vue.draggable.next/#/simple
import draggable from 'vuedraggable';
import { ref, onMounted } from 'vue';

const props = defineProps({
    id: {
        type: Number,
        default: null
    },
    title: {
        type: String,
        required: true
    },
    types: {
        type: Object,
        required: true
    },
    type: {
        type: String,
        required: true
    },
    options: {
        type: Array,
        required: true
    },
    answer: {
        type: Number,
        default: 0
    },
    explanation: {
        type: String,
        required: false
    },
    toRoute: {
        type: String,
        required: true
    }
});

//TODO
let postBody = ref('POST BODY HERE');

const pollForm = useForm({
    title: props.title,
    type: props.type,
    options: props.options,
    answer: props.answer,
    explanation: props.explanation,
})

const createPoll = () => {
    if (props.id) { //update
        pollForm.patch(route(props.toRoute, props.id), {
            preserveScroll: true,
            onSuccess: () => pollForm.reset(),
        })
    } else { //create
        pollForm.post(route(props.toRoute), {
            preserveScroll: true,
            onSuccess: () => pollForm.reset(),
        })
    }
}

const deleteOption = (index) => {
    if(!confirm('Are you sure?')) {
        return;
    }

    pollForm.options.splice(index, 1);

    if (pollForm.options.length < 2 || pollForm.answer === index) {
        pollForm.answer = 0;
    } else if (index < pollForm.answer) {
        pollForm.answer -= 1;
    }
}

const onDragEnd = (e) => {
    const oldIndex = e.oldDraggableIndex;
    const newIndex = e.newDraggableIndex;

    if (oldIndex === pollForm.answer) {
        pollForm.answer = newIndex;
    } else if (oldIndex < pollForm.answer && newIndex >= pollForm.answer) {
        pollForm.answer -= 1;
    } else if (oldIndex > pollForm.answer && newIndex <= pollForm.answer) {
        pollForm.answer += 1;
    }
}
</script>