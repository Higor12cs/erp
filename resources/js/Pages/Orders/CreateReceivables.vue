<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Select2 from "@/Components/Select2.vue";
import InputField from "@/Components/InputField.vue";
import { ref, computed, onMounted } from "vue";

const props = defineProps({
    order: Object,
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

const loading = ref(false);

const form = useForm({
    receivables: [
        {
            payment_method_id: "",
            due_date: new Date().toISOString().slice(0, 10),
            amount: props.order.total_price,
            description: `RECEBÍVEL PEDIDO #${String(
                props.order.sequential_id
            ).padStart(6, "0")}`,
        },
    ],
});

const addReceivable = () => {
    const lastDueDate = new Date(
        form.receivables[form.receivables.length - 1].due_date
    );
    const newDueDate = new Date(lastDueDate.setDate(lastDueDate.getDate() + 30))
        .toISOString()
        .slice(0, 10);

    form.receivables.push({
        payment_method_id: "",
        due_date: newDueDate,
        amount: 0,
        description: `RECEBÍVEL PEDIDO #${String(
            props.order.sequential_id
        ).padStart(6, "0")}`,
    });
};

const removeReceivable = (index) => {
    if (form.receivables.length > 1) {
        form.receivables.splice(index, 1);
    }
};

const totalReceivables = computed(() => {
    return form.receivables.reduce((sum, item) => {
        const amount = parseFloat(convertToNumber(item.amount) || 0);
        return sum + amount;
    }, 0);
});

const isValid = computed(() => {
    return Math.abs(totalReceivables.value - props.order.total_price) < 0.01;
});

const submit = () => {
    form.post(route("orders.store-receivables", props.order.id));
};

const convertToNumber = (value) => {
    if (value === null || value === undefined || value === "") {
        return 0;
    }

    if (typeof value === "number") {
        return value;
    }

    let valueStr = String(value).trim();

    valueStr = valueStr.replace(/R\$\s*/g, "");

    if (valueStr.includes(",")) {
        valueStr = valueStr.replace(/\./g, "").replace(",", ".");
    }

    const number = parseFloat(valueStr);
    return number;
};
</script>

<template>
    <Head title="Finalizar Pedido" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>
                    Finalizar Pedido #{{
                        String(order.sequential_id).padStart(6, "0")
                    }}
                </h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Pedidos', routeName: 'orders.index' },
                        { label: 'Finalizar' },
                    ]"
                />
            </div>

            <Link
                :href="route('orders.show', order.sequential_id)"
                class="btn btn-secondary mb-auto"
            >
                <i class="fas fa-sm fa-arrow-left"></i>
                &nbsp; Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Criar Recebíveis</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>
                            Cliente:
                            {{ " " }}
                            <strong>{{ order.customer.first_name }}</strong>
                        </h5>
                    </div>
                    <div class="col-md-12">
                        <h5>
                            Valor Total do Pedido:
                            {{ " " }}
                            <strong class="text-success">{{
                                formatCurrency(order.total_price)
                            }}</strong>
                        </h5>
                    </div>
                </div>

                <form id="receivables-form" @submit.prevent="submit">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-1">#</th>
                                    <th class="col-3">Método de Pagamento</th>
                                    <th class="col-2">Data de Vencimento</th>
                                    <th class="col-2">Valor</th>
                                    <th class="col-3">Descrição</th>
                                    <th class="col-1">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        receivable, index
                                    ) in form.receivables"
                                    :key="index"
                                >
                                    <td>
                                        {{
                                            (index + 1)
                                                .toString()
                                                .padStart(3, "0")
                                        }}
                                    </td>
                                    <td>
                                        <Select2
                                            v-model="
                                                receivable.payment_method_id
                                            "
                                            :search-url="
                                                route(
                                                    'api.payment-methods.search'
                                                )
                                            "
                                            :class="{
                                                'is-invalid':
                                                    form.errors[
                                                        `receivables.${index}.payment_method_id`
                                                    ],
                                            }"
                                            placeholder="Selecione"
                                            required
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                form.errors[
                                                    `receivables.${index}.payment_method_id`
                                                ]
                                            }}
                                        </div>
                                    </td>
                                    <td>
                                        <InputField
                                            :id="`due_date_${index}`"
                                            v-model="receivable.due_date"
                                            type="date"
                                            :error="
                                                form.errors[
                                                    `receivables.${index}.due_date`
                                                ]
                                            "
                                            required
                                        />
                                    </td>
                                    <td>
                                        <InputField
                                            :id="`amount_${index}`"
                                            v-model="receivable.amount"
                                            maskType="currency"
                                            :error="
                                                form.errors[
                                                    `receivables.${index}.amount`
                                                ]
                                            "
                                            required
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="receivable.description"
                                            :class="{
                                                'is-invalid':
                                                    form.errors[
                                                        `receivables.${index}.description`
                                                    ],
                                            }"
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                form.errors[
                                                    `receivables.${index}.description`
                                                ]
                                            }}
                                        </div>
                                    </td>
                                    <td>
                                        <button
                                            :disabled="
                                                form.receivables.length <= 1
                                            "
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            @click="removeReceivable(index)"
                                        >
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-info"
                                            @click="addReceivable"
                                        >
                                            <i class="fas fa-plus"></i>
                                            &nbsp; Adicionar Recebível
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Resumo</div>
            <div class="card-body px-0 pb-0">
                <table class="table">
                    <tbody>
                        <tr>
                            <td class="px-3">
                                <strong>Total do Pedido:</strong>
                            </td>
                            <td class="px-3 text-right">
                                {{ formatCurrency(order.total_price) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-3">
                                <strong>Total dos Recebíveis:</strong>
                            </td>
                            <td class="px-3 text-right">
                                {{ formatCurrency(totalReceivables) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-3"><strong>Diferença:</strong></td>
                            <td
                                class="px-3 text-right"
                                :class="{
                                    'text-success': isValid,
                                    'text-danger': !isValid,
                                }"
                            >
                                {{
                                    formatCurrency(
                                        totalReceivables - order.total_price
                                    )
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="!isValid" class="alert alert-danger mt-3">
            O valor total dos recebíveis deve ser igual ao valor do pedido.

            <div class="">
                Valor do Pedido:
                {{ " " }}
                {{ formatCurrency(order.total_price) }}

                Valor Total dos Recebíveis:
                {{ " " }}
                {{ formatCurrency(totalReceivables) }}

                Diferença:
                {{ " " }}
                {{ formatCurrency(totalReceivables - order.total_price) }}
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button
                type="button"
                class="btn btn-primary"
                :disabled="!isValid || form.processing"
                @click="submit"
            >
                <span
                    v-if="form.processing"
                    class="spinner-border spinner-border-sm mr-2"
                    role="status"
                    aria-hidden="true"
                ></span>
                <span v-if="form.processing">Processando...</span>
                <span v-else>
                    <i class="fas fa-save"></i>
                    &nbsp; Finalizar Pedido
                </span>
            </button>
        </div>
    </AuthenticatedLayout>
</template>
