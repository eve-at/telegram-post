<template>
    <Head :title="'Poll - ' + (props.id ? 'Edit' : 'Create')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Poll - ' + (props.id ? 'Edit' : 'Create')"
            ></h2>
        </template>

        <LayoutContent body="">
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
                        <InputError :message="pollForm.errors.options" />
                    </div>

                    <div class="mb-3">
                        <InputLabel for="answer">Correct option*</InputLabel>
                        <!--  -->
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
</script>