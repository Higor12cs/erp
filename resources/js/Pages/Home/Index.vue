<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Home" />
    
    <AuthenticatedLayout>
        <div>
            <h4>Home</h4>
        </div>
    </AuthenticatedLayout>
</template>
