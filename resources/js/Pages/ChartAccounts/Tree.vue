<script setup>
import { ref, watch, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import DeleteConfirmation from "@/Components/DeleteConfirmation.vue";
import AccountTreeItem from "@/Pages/ChartAccounts/AccountTreeItem.vue";

const props = defineProps({
    accountsTree: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || "");
const expandedAccounts = ref({});
const filteredAccounts = ref([]);
const showDeleteModal = ref(false);
const deleteAccountId = ref(null);
const loading = ref(false);

onMounted(() => {
    // Expandir automaticamente o primeiro nível
    if (props.accountsTree && props.accountsTree.length > 0) {
        props.accountsTree.forEach((account) => {
            expandedAccounts.value[account.id] = true;
        });
    }
    filterAccounts();
});

watch(
    () => searchQuery.value,
    () => filterAccounts()
);

const filterAccounts = () => {
    if (!searchQuery.value) {
        filteredAccounts.value = JSON.parse(JSON.stringify(props.accountsTree));
        return;
    }

    const query = searchQuery.value.toLowerCase();

    // Função recursiva para filtrar e manter a estrutura
    const filterTree = (accounts) => {
        if (!accounts) return [];

        return accounts.filter((account) => {
            // Verifica se o item atual corresponde à pesquisa
            const matchesQuery =
                account.code.toLowerCase().includes(query) ||
                account.name.toLowerCase().includes(query);

            // Filtra recursivamente os filhos
            const filteredChildren = account.children
                ? filterTree(account.children)
                : [];

            // Mantém o item se ele corresponde ou se algum filho corresponde
            if (matchesQuery || filteredChildren.length > 0) {
                // Se algum filho corresponde, expanda este nó automaticamente
                if (filteredChildren.length > 0 && !matchesQuery) {
                    expandedAccounts.value[account.id] = true;
                }

                // Retorna uma cópia do item com os filhos filtrados
                return {
                    ...account,
                    children: filteredChildren,
                };
            }

            return false;
        });
    };

    filteredAccounts.value = filterTree(
        JSON.parse(JSON.stringify(props.accountsTree))
    );
};

const toggleExpand = (accountId) => {
    expandedAccounts.value[accountId] = !expandedAccounts.value[accountId];
};

const expandAll = () => {
    const setAllExpanded = (accounts, value) => {
        accounts.forEach((account) => {
            expandedAccounts.value[account.id] = value;
            if (account.children && account.children.length > 0) {
                setAllExpanded(account.children, value);
            }
        });
    };

    setAllExpanded(props.accountsTree, true);
};

const collapseAll = () => {
    const setAllExpanded = (accounts, value) => {
        accounts.forEach((account) => {
            expandedAccounts.value[account.id] = value;
            if (account.children && account.children.length > 0) {
                setAllExpanded(account.children, value);
            }
        });
    };

    // Mantém o primeiro nível expandido
    props.accountsTree.forEach((account) => {
        expandedAccounts.value[account.id] = true;
    });

    // Colapsa todos os outros níveis
    props.accountsTree.forEach((account) => {
        if (account.children && account.children.length > 0) {
            setAllExpanded(account.children, false);
        }
    });
};

const confirmDelete = (account) => {
    deleteAccountId.value = account.id;
    showDeleteModal.value = true;
};

const handleDelete = () => {
    loading.value = true;
    return route("chart-accounts.destroy", deleteAccountId.value);
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteAccountId.value = null;
};

const createNewAccount = (parentId = null) => {
    const params = {};
    if (parentId) {
        params.parent_id = parentId;
    }
    router.get(route("chart-accounts.create", params));
};
</script>

<template>
    <Head title="Planos de Contas - Árvore" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>Planos de Contas - Visualização em Árvore</h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        {
                            label: 'Planos de Contas',
                            routeName: 'chart-accounts.index',
                        },
                        { label: 'Visualização em Árvore' },
                    ]"
                />
            </div>

            <div>
                <Link
                    :href="route('chart-accounts.index')"
                    class="btn btn-secondary mr-2"
                >
                    <i class="fas fa-table"></i>
                    &nbsp; Visualização em Tabela
                </Link>
                <button @click="createNewAccount()" class="btn btn-primary">
                    <i class="fas fa-sm fa-plus"></i>
                    &nbsp; Novo Plano de Conta
                </button>
            </div>
        </div>

        <div class="card">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <div>Planos de Contas - Árvore</div>
                <div>
                    <button
                        @click="expandAll"
                        class="btn btn-sm btn-outline-secondary mr-2"
                    >
                        <i class="fas fa-expand-alt"></i> Expandir Todos
                    </button>
                    <button
                        @click="collapseAll"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        <i class="fas fa-compress-alt"></i> Recolher Todos
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Search Box -->
                <div class="mb-4">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Pesquisar código ou nome"
                            v-model="searchQuery"
                        />
                        <div class="input-group-append">
                            <button class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <small class="text-muted"
                        >A pesquisa expandirá automaticamente os ramos que
                        contêm resultados</small
                    >
                </div>

                <!-- Tree View -->
                <div class="account-tree">
                    <div
                        v-if="filteredAccounts.length === 0"
                        class="text-center py-4"
                    >
                        <p class="text-muted">
                            Nenhum plano de conta encontrado.
                        </p>
                    </div>

                    <AccountTreeItem
                        v-for="account in filteredAccounts"
                        :key="account.id"
                        :account="account"
                        :is-expanded="expandedAccounts[account.id]"
                        :expanded-accounts="expandedAccounts"
                        @toggle-expand="toggleExpand"
                        @create-new="createNewAccount"
                        @delete="confirmDelete"
                    />
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmation
            v-if="showDeleteModal"
            :visible="showDeleteModal"
            :loading="loading"
            title="Confirmar Exclusão"
            message="Você tem certeza que deseja excluir este plano de conta?"
            warning="Esta ação não pode ser desfeita. Planos de conta com subcontas não podem ser excluídos."
            delete-route-method="delete"
            :delete-route="handleDelete"
            @cancel="cancelDelete"
            success-redirect="chart-accounts.tree"
            success-message="Plano de conta excluído com sucesso!"
        />
    </AuthenticatedLayout>
</template>

<style scoped>
.account-tree {
    margin-left: -24px;
}
</style>
