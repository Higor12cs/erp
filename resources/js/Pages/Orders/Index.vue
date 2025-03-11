<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

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

const deleteOrder = (orderId) => {
    if (confirm("Tem certeza que deseja excluir este pedido?")) {
        router.delete(route("orders.destroy", orderId));
    }
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
                                <th class="col-6">Cliente</th>
                                <th class="col-2">Data</th>
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
                                    <button
                                        @click="deleteOrder(order.id)"
                                        class="btn btn-sm btn-danger"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td colspan="5" class="text-center">
                                    Nenhum pedido encontrado
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="orders.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
