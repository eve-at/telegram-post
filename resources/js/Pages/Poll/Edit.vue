<template>
    <Head :title="'Poll - ' + $props.title" />

    <AuthenticatedLayout>
        <template #header>
            <h2 
                class="font-semibold text-xl text-gray-800 leading-tight" 
                v-html="'Poll - ' + $props.title"
            ></h2>
        </template>

        <LayoutContent 
            :body="preview" 
            :has-media="false"
            :show-signature="pollForm.show_signature"
            @form:submit="onFormSubmit"
            @form:cancel="onFormCancel"
            @form:concept="onFormConcept"
        >
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

                    <!-- <div class="mb-3 flex space-x-2">
                        <Checkbox id="is_anonymous" :checked="pollForm.is_anonymous" @update:checked="updateIsAnonymous"/>
                        <InputLabel class="ml-2 cursor-pointer" for="is_anonymous">Anonymous</InputLabel>
                    </div> -->

                    <div class="mb-3">
                        <InputLabel for="options">Options* (2-10 options)</InputLabel>
                        <draggable 
                            v-model="pollForm.options" 
                            item-key="id" 
                            @end="onDragEnd"
                        >
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
                                <div 
                                    v-if="!pollForm.options.length" 
                                    class="flex divide-y border-x border-gray-300 hover:bg-gray-100 italic text-gray-400 p-2 justify-center">
                                    There is no options so far
                                </div>
                            </template>
                            <template #item="{ element, index }">
                                <div class="group flex divide-y border-x border-gray-300 hover:bg-gray-100 cursor-grab">
                                    <div class="hidden"></div>
                                    <div v-if="pollForm.type==='quiz'" class="w-2/12 text-center p-2">
                                        <input type="radio" v-model="pollForm.answer" :value="index" /> 
                                    </div>
                                    <div class="p-2"
                                        :class="{
                                            'w-9/12': pollForm.type==='quiz',
                                            'w-10/12': pollForm.type!=='quiz',
                                        }"
                                        @dblclick="editOption(index)"
                                    >
                                        {{ element }}
                                    </div>
                                    <div class="w-2/12 text-center">
                                        <span 
                                            v-if="pollForm.options.length>2"
                                            @click="deleteOption(index)"
                                            class="hidden group-hover:block cursor-pointer hover:underline text-blue-500 pt-2"
                                        >Delete</span>
                                        <span 
                                            v-if="pollForm.options.length<3"
                                            class="hidden group-hover:block cursor-not-allowed text-gray-300 pt-2"
                                        >Delete</span>
                                    </div>
                                </div>
                            </template>
                            <template #footer>
                                <div
                                    class="border-t border-l border-r rounded-b-md bg-gray-300"
                                    role="group"
                                    key="footer"
                                >
                                    <PrimaryButton 
                                        class="ml-4 my-2" 
                                        @click.prevent="openModal()"
                                        :disabled="pollForm.processing || pollForm.options.length >= props.maxOptions"
                                    >
                                        Add option
                                    </PrimaryButton>
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

                    <!-- <div class="mb-3 flex space-x-2">
                        <Checkbox id="show_signature" :checked="pollForm.show_signature" @update:checked="updateShowSignature"/>
                        <InputLabel class="ml-2 cursor-pointer" for="show_signature">Show Channel signature</InputLabel>
                    </div> -->
                </form>
            </template>
        </LayoutContent>
    </AuthenticatedLayout>
    <Modal 
        :show="showModal" 
        @close="closeModal" 
        @onEnterKey="saveOption"
    >
        <div class="flex py-2 px-4 space-x-2">
            <TextInput v-model="modalInputText" autofocus autoselect placeholder="Tap your option..."/>
            <PrimaryButton @click="saveOption">{{ modalEditOption >= 0 ? 'Modify' : 'Add' }}</PrimaryButton>
            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
        </div>
    </Modal>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LayoutContent from '@/Components/LayoutContent.vue';
//https://sortablejs.github.io/vue.draggable.next/#/simple
import draggable from 'vuedraggable';
import Modal from '@/Components/Modal.vue'
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
//import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    poll: {
        type: Object,
        default: null,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    types: {
        type: Object,
        required: true
    },
    toRoute: {
        type: String,
        required: true
    },
    cancelRoute: {
        type: String,
        required: true
    }
});

let preview = ref('');
let showModal = ref(false);
let modalEditOption = ref(-1);
let modalInputText = ref('');

const pollForm = useForm({
    title: props.poll.title,
    type: props.poll.type,
    options: props.poll.options,
    answer: props.poll.answer,
    explanation: props.poll.explanation,
    //show_signature: props.poll.show_signature,
    is_anonymous: props.poll.is_anonymous, // non-anonymous polls can't be sent to channel chats
    concept: false,  
});

//const updateShowSignature = (val) => pollForm.show_signature = val;
//const updateIsAnonymous = (val) => pollForm.is_anonymous = val;

const updatePreview = () => {
    const explantion = pollForm.explanation.length 
        ? `<img title="${pollForm.explanation}" class="absolute top-1 right-1 cursor-help" src="/images/bulb.svg" width="20" height="20" alt="Explanation" />`
        : '';
    const poll = pollForm.options.length > 0
        ? '<ul class="space-y-1 text-sm pl-2"><li><input class="mr-2" type="radio" name="previewOption" />' + pollForm.options.join('</li><li><input class="mr-2" type="radio" name="previewOption" />') + '</li></ul>' 
        : '<span class="italic text-gray-400 p-2">< There is no options so far ><span>';

    const anonymous = pollForm.is_anonymous ? '<span class="text-gray-600">Anonymous Poll</span><br />' : '';

    preview.value = 
        `<div class="relative">
            <span class="text-base text-bold block mr-8">${pollForm.title}</span>
            ${explantion}
            ${anonymous}
            <br />
            ${poll}<br />
        </div>`;
}

onMounted(() => {
    usePage().props.messagable_id = props.post.id;
    
    updatePreview();
});

watch(
    pollForm,
    updatePreview,
    { deep: true }
);

const createPoll = () => {
    if (props.poll.id) { //update
        pollForm.patch(route(props.toRoute, props.poll.id), {
            preserveScroll: true
        })
    } else { //create
        pollForm.post(route(props.toRoute), {
            preserveScroll: true
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

    updatePreview();
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

const openModal = async () => {
    showModal.value = true;
}

const closeModal = () => {
    showModal.value = false;
    modalEditOption.value = -1;
    modalInputText.value = '';
}

const saveOption = () => {
    if (modalEditOption.value < 0) {
        if (pollForm.options.length >= props.maxOptions) {
            return;
        }
        pollForm.options.push(modalInputText.value);
    } else {
        pollForm.options[modalEditOption.value] = modalInputText.value;
    }
    closeModal();
}

const editOption = (index) => {
    modalEditOption.value = index;
    modalInputText.value = pollForm.options[index];
    openModal();
}

const onFormSubmit = () => {
    if (pollForm.processing) {
        return;
    }
    pollForm.concept = false;
    createPoll();
}

const onFormCancel = () => {
    if (pollForm.processing) {
        return;
    }
    router.visit(route(props.cancelRoute));
}

const onFormConcept = () => {
    if (pollForm.processing) {
        return;
    }
    pollForm.concept = true;
    createPoll();
    pollForm.concept = false;
}

</script>

<style>
    .sortable-ghost {
        --tw-border-opacity: .5;
        border-top: 1px solid rgb(209 213 219 / var(--tw-border-opacity));
        /* cursor: grabbing; */
        opacity: 1;
    }
    .sortable-ghost > * {
        opacity: 0.0;
    }
</style>