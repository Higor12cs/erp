<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

const props = defineProps({
    iconClass: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    subItems: {
        type: Array,
        required: true,
    },
    initiallyOpen: {
        type: Boolean,
        default: false,
    },
});

const isOpen = ref(props.initiallyOpen);
const menuElement = ref(null);

onMounted(() => {
    const hasActiveChild = props.subItems.some((item) =>
        route().current(item.routeName)
    );

    if (hasActiveChild || props.initiallyOpen) {
        if (menuElement.value) {
            menuElement.value.classList.add("menu-open");
            const menuLink = menuElement.value.querySelector(".nav-link");
            if (menuLink) {
                menuLink.classList.add("active");
            }
            isOpen.value = true;
        }
    }
});
</script>

<template>
    <li ref="menuElement" class="nav-item">
        <a href="#" class="nav-link">
            <i :class="`nav-icon ${iconClass}`"></i>
            <p class="ml-1">
                {{ label }}
                <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li v-for="(item, index) in subItems" :key="index" class="nav-item">
                <Link
                    :href="route(item.routeName)"
                    :class="
                        route().current(item.routeName)
                            ? 'nav-link active'
                            : 'nav-link'
                    "
                >
                    <i :class="`nav-icon ${item.iconClass}`"></i>
                    <p class="ml-1">
                        {{ item.label }}
                        <span
                            v-if="item.badge"
                            class="badge badge-primary right"
                            >{{ item.badge }}</span
                        >
                    </p>
                </Link>
            </li>
        </ul>
    </li>
</template>
