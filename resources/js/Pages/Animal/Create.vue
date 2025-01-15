<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, usePage} from '@inertiajs/vue3';
import Form from "@/Pages/Animal/Form.vue";
</script>

<template>
    <Head title="Adding an animal"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Adding an animal in {{ usePage().props.auth.user.shelter.name }} shelter
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                        class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800"
                >
                    <Form/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
