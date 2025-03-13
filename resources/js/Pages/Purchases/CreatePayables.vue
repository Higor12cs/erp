<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Select2 from "@/Components/Select2.vue";
import InputField from "@/Components/InputField.vue";
import { ref, computed, onMounted } from "vue";

const props = defineProps({
    purchase: Object,
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
    payables: [
        {
            payment_method_id: "",
            due_date: new Date().toISOString().slice(0, 10),
            amount: props.purchase.total_cost,
            description: `PAGÁVEL COMPRA #${String(
                props.purchase.sequential_id
            ).padStart(6, "0")}`,
        },
    ],
});

const addPayable = () => {
    const lastDueDate = new Date(
        form.payables[form.payables.length - 1].due_date
    );
    const newDueDate = new Date(lastDueDate.setDate(lastDueDate.getDate() + 30))
        .toISOString()
        .slice(0, 10);

    form.payables.push({
        payment_method_id: "",
        due_date: newDueDate,
        amount: 0,
        description: `PAGÁVEL COMPRA #${String(
            props.purchase.sequential_id
        ).padStart(6, "0")}`,
    });
};

const removePayable = (index) => {
    if (form.payables.length > 1) {
        form.payables.splice(index, 1);
    }
};

const totalPayables = computed(() => {
    return form.payables.reduce((sum, item) => {
        const amount = parseFloat(convertToNumber(item.amount) || 0);
        return sum + amount;
    }, 0);
});

const isValid = computed(() => {
    return Math.abs(totalPayables.value - props.purchase.total_cost) < 0.01;
});

const submit = () => {
    form.post(route("purchases.store-payables", props.purchase.id));
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
    <Head title="Finalizar Compra" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>
                    Finalizar Compra #{{
                        String(purchase.sequential_id).padStart(6, "0")
                    }}
                </h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Compras', routeName: 'purchases.index' },
                        { label: 'Finalizar' },
                    ]"
                />
            </div>

            <Link
                :href="route('purchases.show', purchase.sequential_id)"
                class="btn btn-secondary mb-auto"
            >
                <i class="fas fa-sm fa-arrow-left"></i>
                &nbsp; Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Criar Pagáveis</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>
                            Fornecedor:
                            {{ " " }}
                            <strong>{{ purchase.supplier.first_name }}</strong>
                        </h5>
                    </div>
                    <div class="col-md-12">
                        <h5>
                            Valor Total da Compra:
                            {{ " " }}
                            <strong class="text-success">{{
                                formatCurrency(purchase.total_cost)
                            }}</strong>
                        </h5>
                    </div>
                </div>

                <form id="payables-form" @submit.prevent="submit">
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
                                    v-for="(payable, index) in form.payables"
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
                                            v-model="payable.payment_method_id"
                                            :search-url="
                                                route(
                                                    'api.payment-methods.search'
                                                )
                                            "
                                            :class="{
                                                'is-invalid':
                                                    form.errors[
                                                        `payables.${index}.payment_method_id`
                                                    ],
                                            }"
                                            placeholder="Selecione"
                                            required
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                form.errors[
                                                    `payables.${index}.payment_method_id`
                                                ]
                                            }}
                                        </div>
                                    </td>
                                    <td>
                                        <InputField
                                            :id="`due_date_${index}`"
                                            v-model="payable.due_date"
                                            type="date"
                                            :error="
                                                form.errors[
                                                    `payables.${index}.due_date`
                                                ]
                                            "
                                            required
                                        />
                                    </td>
                                    <td>
                                        <InputField
                                            :id="`amount_${index}`"
                                            v-model="payable.amount"
                                            maskType="currency"
                                            :error="
                                                form.errors[
                                                    `payables.${index}.amount`
                                                ]
                                            "
                                            required
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control"
                                            v-model="payable.description"
                                            :class="{
                                                'is-invalid':
                                                    form.errors[
                                                        `payables.${index}.description`
                                                    ],
                                            }"
                                        />
                                        <div class="invalid-feedback">
                                            {{
                                                form.errors[
                                                    `payables.${index}.description`
                                                ]
                                            }}
                                        </div>
                                    </td>
                                    <td>
                                        <button
                                            :disabled="
                                                form.payables.length <= 1
                                            "
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            @click="removePayable(index)"
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
                                            @click="addPayable"
                                        >
                                            <i class="fas fa-plus"></i>
                                            &nbsp; Adicionar Pagável
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
                                <strong>Total da Compra:</strong>
                            </td>
                            <td class="px-3 text-right">
                                {{ formatCurrency(purchase.total_cost) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="px-3">
                                <strong>Total dos Pagáveis:</strong>
                            </td>
                            <td class="px-3 text-right">
                                {{ formatCurrency(totalPayables) }}
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
                                        totalPayables - purchase.total_cost
                                    )
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="!isValid" class="alert alert-danger mt-3">
            O valor total dos pagáveis deve ser igual ao valor da compra.

            <div class="">
                Valor da Compra:
                {{ " " }}
                {{ formatCurrency(purchase.total_cost) }}

                Valor Total dos Pagáveis:
                {{ " " }}
                {{ formatCurrency(totalPayables) }}

                Diferença:
                {{ " " }}
                {{ formatCurrency(totalPayables - purchase.total_cost) }}
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
                    &nbsp; Finalizar Compra
                </span>
            </button>
        </div>
    </AuthenticatedLayout>
</template>
