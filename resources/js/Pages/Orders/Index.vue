<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import DeleteConfirmation from "@/Components/DeleteConfirmation.vue";

const props = defineProps({
    orders: Object,
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

const confirmDelete = (orderId) => {
    deleteId.value = orderId;
    showDeleteModal.value = true;
};

const handleDelete = () => {
    loading.value = true;
    return route("orders.destroy", deleteId.value);
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};
</script>

<template>
    <Head title="Pedidos" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>Pedidos</h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Pedidos' },
                    ]"
                />
            </div>
            <Link
                :href="route('orders.create')"
                class="btn btn-primary mb-auto"
            >
                <i class="fas fa-sm fa-plus"></i>
                &nbsp; Novo Pedido
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Pedidos</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="col-1">Código</th>
                                <th class="col-5">Cliente</th>
                                <th class="col-2">Data</th>
                                <th class="col-1">Status</th>
                                <th class="col-2">Valor</th>
                                <th class="col-1">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id">
                                <td>
                                    {{
                                        String(order.sequential_id).padStart(
                                            6,
                                            "0"
                                        )
                                    }}
                                </td>
                                <td>{{ order.customer.first_name }}</td>
                                <td>{{ formatDate(order.issue_date) }}</td>
                                <td>
                                    <span
                                        class="badge"
                                        :class="
                                            order.receivables &&
                                            order.receivables.length
                                                ? 'bg-success'
                                                : 'bg-warning'
                                        "
                                    >
                                        {{
                                            order.receivables &&
                                            order.receivables.length
                                                ? "Finalizado"
                                                : "Pendente"
                                        }}
                                    </span>
                                </td>
                                <td>{{ formatCurrency(order.total_price) }}</td>
                                <td class="text-nowrap">
                                    <Link
                                        :href="
                                            route(
                                                'orders.show',
                                                order.sequential_id
                                            )
                                        "
                                        class="btn btn-sm btn-secondary mr-1"
                                    >
                                        Visualizar
                                    </Link>
                                    <Link
                                        v-if="
                                            !(
                                                order.receivables &&
                                                order.receivables.length
                                            )
                                        "
                                        :href="
                                            route(
                                                'orders.edit',
                                                order.sequential_id
                                            )
                                        "
                                        class="btn btn-sm btn-secondary mr-1"
                                    >
                                        Editar
                                    </Link>
                                    <Link
                                        v-if="
                                            !(
                                                order.receivables &&
                                                order.receivables.length
                                            )
                                        "
                                        :href="
                                            route(
                                                'orders.create-receivables',
                                                order.sequential_id
                                            )
                                        "
                                        class="btn btn-sm btn-primary mr-1"
                                    >
                                        Finalizar
                                    </Link>
                                    <button
                                        @click="confirmDelete(order.id)"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="6" class="text-center">
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="orders.links" />
            </div>
        </div>

        <DeleteConfirmation
            v-if="showDeleteModal"
            :visible="showDeleteModal"
            :loading="loading"
            title="Confirmar Exclusão"
            message="Você tem certeza que deseja excluir este pedido?"
            warning="Esta ação não pode ser desfeita."
            delete-route-method="delete"
            :delete-route="handleDelete"
            @cancel="cancelDelete"
            success-redirect="orders.index"
            success-message="Pedido excluído com sucesso!"
        />
    </AuthenticatedLayout>
</template>
