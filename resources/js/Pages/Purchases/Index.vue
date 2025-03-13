<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import DeleteConfirmation from "@/Components/DeleteConfirmation.vue";

const props = defineProps({
    purchases: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("pt-BR");
};

const showDeleteModal = ref(false);
const deleteId = ref(null);
const loading = ref(false);

const confirmDelete = (purchaseId) => {
    deleteId.value = purchaseId;
    showDeleteModal.value = true;
};

const handleDelete = () => {
    loading.value = true;
    return route("purchases.destroy", deleteId.value);
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};
</script>

<template>
    <Head title="Compras" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>Compras</h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Compras' },
                    ]"
                />
            </div>
            <Link
                :href="route('purchases.create')"
                class="btn btn-primary mb-auto"
            >
                <i class="fas fa-sm fa-plus"></i>
                &nbsp; Nova Compra
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Compras</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="col-1">Código</th>
                                <th class="col-5">Fornecedor</th>
                                <th class="col-2">Data</th>
                                <th class="col-1">Status</th>
                                <th class="col-2">Valor</th>
                                <th class="col-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="purchase in purchases.data"
                                :key="purchase.id"
                            >
                                <td>
                                    {{
                                        String(purchase.sequential_id).padStart(
                                            6,
                                            "0"
                                        )
                                    }}
                                </td>
                                <td>{{ purchase.supplier.first_name }}</td>
                                <td>{{ formatDate(purchase.issue_date) }}</td>
                                <td>
                                    <span
                                        class="badge"
                                        :class="
                                            purchase.payables &&
                                            purchase.payables.length
                                                ? 'bg-success'
                                                : 'bg-warning'
                                        "
                                    >
                                        {{
                                            purchase.payables &&
                                            purchase.payables.length
                                                ? "Finalizado"
                                                : "Pendente"
                                        }}
                                    </span>
                                </td>
                                <td>
                                    {{ formatCurrency(purchase.total_cost) }}
                                </td>
                                <td class="text-nowrap">
                                    <Link
                                        :href="
                                            route(
                                                'purchases.show',
                                                purchase.sequential_id
                                            )
                                        "
                                        class="btn btn-sm btn-secondary mr-1"
                                    >
                                        Visualizar
                                    </Link>
                                    <Link
                                        v-if="
                                            !(
                                                purchase.payables &&
                                                purchase.payables.length
                                            )
                                        "
                                        :href="
                                            route(
                                                'purchases.edit',
                                                purchase.sequential_id
                                            )
                                        "
                                        class="btn btn-sm btn-secondary mr-1"
                                    >
                                        Editar
                                    </Link>
                                    <Link
                                        v-if="
                                            !(
                                                purchase.payables &&
                                                purchase.payables.length
                                            )
                                        "
                                        :href="
                                            route(
                                                'purchases.create-payables',
                                                purchase.sequential_id
                                            )
                                        "
                                        class="btn btn-sm btn-primary mr-1"
                                    >
                                        Finalizar
                                    </Link>
                                    <button
                                        @click="confirmDelete(purchase.id)"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="purchases.data.length === 0">
                                <td colspan="6" class="text-center">
                                    Nenhuma compra encontrada.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="purchases.links" />
            </div>
        </div>

        <DeleteConfirmation
            v-if="showDeleteModal"
            :visible="showDeleteModal"
            :loading="loading"
            title="Confirmar Exclusão"
            message="Você tem certeza que deseja excluir esta compra?"
            warning="Esta ação não pode ser desfeita."
            delete-route-method="delete"
            :delete-route="handleDelete"
            @cancel="cancelDelete"
            success-redirect="purchases.index"
            success-message="Compra excluída com sucesso!"
        />
    </AuthenticatedLayout>
</template>
