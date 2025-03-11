<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    breadcrumb: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-0 m-0" style="background-color: transparent;">
            <template v-for="(item, index) in breadcrumb" :key="index">
                <li class="breadcrumb-item" v-if="item.routeName">
                    <Link :href="route(item.routeName)">{{ item.label }}</Link>
                </li>
                <li class="breadcrumb-item" v-else>
                    {{ item.label }}
                </li>
            </template>
        </ol>
    </nav>
</template>
