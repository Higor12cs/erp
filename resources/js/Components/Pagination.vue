<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="pagination-container d-flex justify-content-end">
            <ul class="pagination">
                <li
                    v-for="(link, key) in links"
                    :key="key"
                    class="page-item"
                    :class="{ active: link.active, disabled: !link.url }"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="page-link"
                        v-html="link.label"
                    ></Link>
                    <span v-else class="page-link" v-html="link.label"></span>
                </li>
            </ul>
        </div>
    </div>
</template>
