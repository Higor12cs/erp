<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { onMounted } from "vue";
import NavItem from "@/Components/NavItem.vue";
import NavItemCollapsible from "@/Components/NavItemCollapsible.vue";

const appName = import.meta.env.VITE_APP_NAME || "";
const currentTime = ref("");
const page = usePage();

const sidebarItems = [
    // Início
    {
        type: "header",
        label: "Início",
        items: [
            {
                type: "link",
                routeName: "home.index",
                iconClass: "fas fa-home",
                label: "Home",
            },
            {
                type: "link",
                routeName: "dashboard.index",
                iconClass: "fas fa-tachometer-alt",
                label: "Dashboard",
                permission: "dashboard.index",
            },
        ],
    },

    // Cadastros
    {
        type: "header",
        label: "Cadastros",
        items: [
            {
                type: "link",
                routeName: "customers.index",
                iconClass: "fas fa-users",
                label: "Clientes",
                permission: "customers.index",
            },
            {
                type: "link",
                routeName: "suppliers.index",
                iconClass: "fas fa-users",
                label: "Fornecedores",
                permission: "suppliers.index",
            },
        ],
    },

    // Vendas
    {
        type: "header",
        label: "Vendas",
        items: [
            {
                type: "collapsible",
                iconClass: "fas fa-shopping-basket",
                label: "Pedidos",
                subItems: [
                    {
                        type: "link",
                        routeName: "orders.index",
                        iconClass: "fas fa-shopping-basket",
                        label: "Pedidos",
                        permission: "orders.index",
                    },
                    {
                        type: "link",
                        routeName: "orders.create",
                        iconClass: "fas fa-shopping-basket",
                        label: "Novo Pedido",
                        permission: "orders.create",
                    },
                ],
            },
        ],
    },

    // Compras
    {
        type: "header",
        label: "Compras",
        items: [
            {
                type: "collapsible",
                iconClass: "fas fa-truck-loading",
                label: "Compras",
                subItems: [
                    {
                        routeName: "purchases.index",
                        iconClass: "fas fa-truck-loading",
                        label: "Compras",
                        permission: "purchases.index",
                    },
                    {
                        routeName: "purchases.create",
                        iconClass: "fas fa-truck-loading",
                        label: "Nova Compra",
                        permission: "purchases.create",
                    },
                ],
            },
        ],
    },

    // Financeiro
    {
        type: "header",
        label: "Financeiro",
        items: [
            {
                type: "collapsible",
                iconClass: "fas fa-university",
                label: "Financeiro",
                subItems: [
                    {
                        routeName: "receivables.index",
                        iconClass: "fas fa-arrow-down",
                        label: "Recebíveis",
                        permission: "receivables.index",
                    },
                    {
                        routeName: "payables.index",
                        iconClass: "fas fa-arrow-up",
                        label: "Pagáveis",
                        permission: "payables.index",
                    },
                    {
                        routeName: "accounts.index",
                        iconClass: "fas fa-university",
                        label: "Contas",
                        permission: "accounts.index",
                    },
                    {
                        routeName: "payment-methods.index",
                        iconClass: "fas fa-wallet",
                        label: "Métodos de Pagamento",
                        permission: "payment-methods.index",
                    },
                ],
            },
        ],
    },

    // Estoque
    {
        type: "header",
        label: "Estoque",
        items: [
            {
                type: "link",
                routeName: "products.index",
                iconClass: "fas fa-box",
                label: "Produtos",
                permission: "products.index",
            },
            {
                type: "link",
                routeName: "stock.index",
                iconClass: "fas fa-boxes",
                label: "Estoque",
                permission: "stock.index",
            },
            {
                type: "link",
                routeName: "kardex.index",
                iconClass: "fas fa-dolly",
                label: "Kardex",
                permission: "kardex.index",
            },
            {
                type: "collapsible",
                iconClass: "fas fa-tags",
                label: "Atributos",
                permission: "atributos.index",
                subItems: [
                    {
                        routeName: "sections.index",
                        iconClass: "fas fa-tag",
                        label: "Seções",
                        permission: "sections.index",
                    },
                    {
                        routeName: "groups.index",
                        iconClass: "fas fa-tag",
                        label: "Grupos",
                        permission: "groups.index",
                    },
                    {
                        routeName: "brands.index",
                        iconClass: "fas fa-tag",
                        label: "Marcas",
                        permission: "brands.index",
                    },
                ],
            },
        ],
    },

    // Configurações
    {
        type: "header",
        label: "Configurações",
        items: [
            {
                type: "link",
                routeName: "users.index",
                iconClass: "fas fa-users",
                label: "Usuários",
                permission: "users.index",
            },
            {
                type: "link",
                routeName: "roles.index",
                iconClass: "fas fa-lock",
                label: "Papéis",
                permission: "roles.index",
            },
        ],
    },
];

const hasPermission = (permission) => {
    if (!permission) return true;

    const userRoles = page.props.auth.roles || [];
    const userPermissions = page.props.auth.permissions || [];

    if (userRoles.includes("Administrador")) return true;

    return userPermissions.includes(permission);
};

const visibleMenuItems = computed(() => {
    return sidebarItems.filter((section) => {
        const visibleItems = section.items.filter((item) => {
            if (item.type === "link") {
                return !item.permission || hasPermission(item.permission);
            } else if (item.type === "collapsible") {
                const filteredSubItems = item.subItems.filter(
                    (subItem) =>
                        !subItem.permission || hasPermission(subItem.permission)
                );

                if (filteredSubItems.length === 0) return false;

                item.filteredSubItems = filteredSubItems;
                return !item.permission || hasPermission(item.permission);
            }
            return false;
        });

        section.visibleItems = visibleItems;
        return visibleItems.length > 0;
    });
});

const updateTime = () => {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    const seconds = String(now.getSeconds()).padStart(2, "0");
    currentTime.value = `${hours}:${minutes}:${seconds}`;
};

onMounted(() => {
    updateTime();
    setInterval(updateTime, 1000);
});
</script>

<template>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        data-widget="pushmenu"
                        href="#"
                        role="button"
                    >
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    {{ currentTime }}
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        {{ $page.props.auth.user.name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="dropdown-item"
                        >
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </Link>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-2">
            <Link href="/" class="brand-link d-flex justify-content-center">
                <span class="brand-text font-weight-semibold">
                    {{ appName }}
                </span>
            </Link>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul
                        class="nav nav-pills nav-sidebar flex-column"
                        data-widget="treeindex"
                        role="menu"
                        data-accordion="false"
                    >
                        <template
                            v-for="(section, sectionIndex) in visibleMenuItems"
                            :key="sectionIndex"
                        >
                            <!-- Header -->
                            <li class="nav-header">{{ section.label }}</li>

                            <!-- Menu Items -->
                            <template
                                v-for="(
                                    item, itemIndex
                                ) in section.visibleItems"
                                :key="`${sectionIndex}-${itemIndex}`"
                            >
                                <!-- Regular Link -->
                                <NavItem
                                    v-if="item.type === 'link'"
                                    :route-name="item.routeName"
                                    :icon-class="item.iconClass"
                                    :label="item.label"
                                />

                                <!-- Collapsible Menu -->
                                <NavItemCollapsible
                                    v-else-if="item.type === 'collapsible'"
                                    :icon-class="item.iconClass"
                                    :label="item.label"
                                    :sub-items="item.filteredSubItems"
                                />
                            </template>
                        </template>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid py-3">
                    <slot />
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">
                <strong>{{ appName }}</strong> 0.0.1
            </div>
        </footer>

        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>
</template>
