<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between">
            <h4>Dashboard</h4>
            <button class="btn btn-primary">Create</button>
        </div>
    </AuthenticatedLayout>
</template>
