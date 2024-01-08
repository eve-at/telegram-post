<template>
    <Head title="Post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post</h2>
        </template>

        <div class="w-11/12 flex bg-white overflow-hidden flex-col">
            <div class="border border-gray-300 rounded-xl m-2 overflow-hidden">
                Post here
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>

</script>
