<template>
    <Head title="Posts - List" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Messages</h2>
        </template>

        <div class="mx-auto w-10/12 flex overflow-hidden flex-col">
            <div class="border bg-white border-gray-300 rounded-xl m-2 divide-y divide-solid overflow-hidden">
                <div class="p-6 text-gray-900 italic">There is no messages so far</div>

                <pro-calendar
                    :events="evts"
                    :loading="false"
                    :config="cfg"
                    view="month"
                    date="2022-11-10T00:00:00.000Z"
                    @calendarClosed="void 0"
                    @fetchEvents="void 0"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import "vue-pro-calendar/style";
import { ref, type Ref } from "vue";
import type { Configs, Appointment } from "vue-pro-calendar";

//https://github.com/lbgm/vue-pro-calendar
const cfg = ref<Configs>({
  viewEvent: undefined,
  reportEvent: {
    icon: true,
    text: "",
  },
  searchPlaceholder: "",
  eventName: "",
  closeText: "",
  nativeDatepicker: true,
  todayButton: true,
  firstDayOfWeek: 1,
});

const evts: Ref<Appointment[]> = ref([
  {
    date: "2022-11-10T14:00:00.000Z",
    comment: "Comment goes here",
    id: "cl32rbkjk1700101o53e3e3uhn",
    keywords: "Projet BAMBA",
    name: "MONTCHO Kévin",
  },
  //...
]);

</script>