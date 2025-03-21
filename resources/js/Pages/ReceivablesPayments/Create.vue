<script setup>
import { ref, computed, onMounted } from "vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import Select2 from "@/Components/Select2.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import axios from "axios";

const props = defineProps({
    receivables: {
        type: Array,
        required: true,
    },
});

const loading = ref(false);
const validationError = ref("");

const payments = ref([]);

onMounted(() => {
    if (props.receivables && props.receivables.length > 0) {
        payments.value = props.receivables.map((receivable) => ({
            receivable_id: receivable.id,
            paid_amount: receivable.remaining_amount,
            fees: 0,
            discount: 0,
            effective_amount: receivable.remaining_amount,
        }));
    }
});

const totalPaidAmount = computed(() =>
    payments.value.reduce(
        (total, payment) => total + parseFloat(payment.paid_amount || 0),
        0
    )
);

const totalFees = computed(() =>
    payments.value.reduce(
        (total, payment) => total + parseFloat(payment.fees || 0),
        0
    )
);

const totalDiscounts = computed(() =>
    payments.value.reduce(
        (total, payment) => total + parseFloat(payment.discount || 0),
        0
    )
);

const effectiveTotal = computed(() =>
    payments.value.reduce(
        (total, payment) =>
            total +
            parseFloat(payment.paid_amount || 0) +
            parseFloat(payment.fees || 0) -
            parseFloat(payment.discount || 0),
        0
    )
);

const form = useForm({
    receivable_ids: computed(() => props.receivables.map((r) => r.id)),
    payment_method_id: "",
    account_id: "",
    payment_date: new Date().toISOString().slice(0, 10),
    payments: payments,
    notes: "",
    total_paid_amount: computed(() => totalPaidAmount.value),
});

const validatePaymentAmounts = () => {
    let isValid = true;
    validationError.value = "";

    for (let i = 0; i < props.receivables.length; i++) {
        const receivable = props.receivables[i];
        const payment = payments.value[i];

        if (
            parseFloat(payment.paid_amount) >
            parseFloat(receivable.remaining_amount)
        ) {
            validationError.value = `O valor de pagamento não pode exceder o valor restante para o recebível ${formatSequentialId(
                receivable.sequential_id
            )}.`;
            isValid = false;
            break;
        }
    }

    return isValid;
};

const handleSubmit = () => {
    if (!validatePaymentAmounts()) {
        return;
    }

    loading.value = true;
    validationError.value = "";

    const payload = {
        receivable_ids: props.receivables.map((r) => r.id),
        payment_method_id: form.payment_method_id,
        account_id: form.account_id,
        payment_date: form.payment_date,
        total_paid_amount: totalPaidAmount.value,
        payments: payments.value,
        notes: form.notes,
    };

    axios
        .post(route("receivables.payments.store"), payload)
        .then(() => {
            loading.value = false;
            window.location.href = route("receivables.payments.index");
        })
        .catch((error) => {
            loading.value = false;
            if (error.response && error.response.status === 422) {
                form.clearErrors();
                Object.keys(error.response.data.errors).forEach((key) => {
                    form.setError(key, error.response.data.errors[key][0]);
                });
            } else {
                console.error("Error submitting form:", error);
                alert(
                    "Ocorreu um erro ao processar o pagamento. Por favor, tente novamente."
                );
            }
        });
};

const calculateEffectiveAmount = (payment, index) => {
    const paid = parseFloat(payment.paid_amount || 0);
    const fees = parseFloat(payment.fees || 0);
    const discount = parseFloat(payment.discount || 0);

    payment.effective_amount = paid + fees - discount;
};

const formatCurrency = (value) => {
    if (!value) return "R$ 0,00";

    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(value);
};

const formatSequentialId = (id) => {
    return String(id).padStart(6, "0");
};
</script>

<template>
    <Head title="Registrar Pagamento" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>Registrar Pagamento</h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Recebíveis', routeName: 'receivables.index' },
                        { label: 'Registrar Pagamento' },
                    ]"
                />
            </div>
            <Link
                :href="route('receivables.index')"
                class="btn btn-secondary mb-auto"
            >
                <i class="fas fa-sm fa-arrow-left"></i>
                &nbsp; Voltar
            </Link>
        </div>

        <div class="card">
            <div class="card-header">Detalhes do Pagamento</div>
            <div class="card-body">
                <div v-if="validationError" class="alert alert-danger">
                    {{ validationError }}
                </div>

                <h5>
                    Cliente:
                    <strong>
                        {{ props.receivables[0].customer.first_name }}
                        {{ " " }}
                        {{ props.receivables[0].customer.last_name }}
                    </strong>
                </h5>

                <form @submit.prevent="handleSubmit">
                    <div class="row class mt-4">
                        <div class="col-md-4">
                            <Select2
                                label="Método de Pagamento"
                                v-model="form.payment_method_id"
                                :error="form.errors.payment_method_id"
                                :search-url="
                                    route('api.payment-methods.search')
                                "
                                value-key="id"
                                label-key="name"
                                placeholder="Pesquisar"
                                required
                            />
                        </div>

                        <div class="col-md-4">
                            <Select2
                                label="Conta"
                                v-model="form.account_id"
                                :error="form.errors.account_id"
                                :search-url="route('api.accounts.search')"
                                value-key="id"
                                label-key="name"
                                placeholder="Pesquisar"
                                required
                            />
                        </div>

                        <div class="col-md-4">
                            <InputField
                                id="payment_date"
                                label="Data de Pagamento"
                                v-model="form.payment_date"
                                type="date"
                                :error="form.errors.payment_date"
                                required
                            />
                        </div>
                    </div>

                    <h5 class="mt-3">Detalhes dos Recebíveis</h5>

                    <div class="table-responsive">
                        <table
                            class="table table-bordered table-striped table-hover"
                        >
                            <thead>
                                <tr class="text-nowrap">
                                    <th>Código</th>
                                    <th>Vencimento</th>
                                    <th>Valor Total</th>
                                    <th>Valor Pago</th>
                                    <th>Valor Saldo</th>
                                    <th>Valor a Pagar</th>
                                    <th>Acréscimos</th>
                                    <th>Descontos</th>
                                    <th>Valor Efetivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(receivable, index) in receivables"
                                    :key="receivable.id"
                                >
                                    <td>
                                        {{
                                            formatSequentialId(
                                                receivable.sequential_id
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            new Date(
                                                receivable.due_date
                                            ).toLocaleDateString("pt-BR")
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            formatCurrency(
                                                receivable.total_amount
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            formatCurrency(
                                                receivable.paid_amount
                                            )
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            formatCurrency(
                                                receivable.remaining_amount
                                            )
                                        }}
                                    </td>
                                    <td v-if="payments[index]">
                                        <InputField
                                            :id="`paid_amount_${index}`"
                                            v-model="
                                                payments[index].paid_amount
                                            "
                                            maskType="currency"
                                            :error="
                                                form.errors[
                                                    `payments.${index}.paid_amount`
                                                ]
                                            "
                                            @update:modelValue="
                                                calculateEffectiveAmount(
                                                    payments[index],
                                                    index
                                                )
                                            "
                                            required
                                        />
                                        <div
                                            v-if="
                                                parseFloat(
                                                    payments[index].paid_amount
                                                ) >
                                                parseFloat(
                                                    receivable.remaining_amount
                                                )
                                            "
                                            class="text-danger small"
                                        >
                                            Valor excede o saldo restante
                                        </div>
                                    </td>
                                    <td v-if="payments[index]">
                                        <InputField
                                            :id="`fees_${index}`"
                                            v-model="payments[index].fees"
                                            maskType="currency"
                                            :error="
                                                form.errors[
                                                    `payments.${index}.fees`
                                                ]
                                            "
                                            @update:modelValue="
                                                calculateEffectiveAmount(
                                                    payments[index],
                                                    index
                                                )
                                            "
                                        />
                                    </td>
                                    <td v-if="payments[index]">
                                        <InputField
                                            :id="`discount_${index}`"
                                            v-model="payments[index].discount"
                                            maskType="currency"
                                            :error="
                                                form.errors[
                                                    `payments.${index}.discount`
                                                ]
                                            "
                                            @update:modelValue="
                                                calculateEffectiveAmount(
                                                    payments[index],
                                                    index
                                                )
                                            "
                                        />
                                    </td>
                                    <td v-if="payments[index]">
                                        {{
                                            formatCurrency(
                                                payments[index].effective_amount
                                            )
                                        }}
                                    </td>
                                    <td v-else colspan="4">Carregando...</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">
                                        Total
                                    </th>
                                    <th>
                                        {{ formatCurrency(totalPaidAmount) }}
                                    </th>
                                    <th>{{ formatCurrency(totalFees) }}</th>
                                    <th>
                                        {{ formatCurrency(totalDiscounts) }}
                                    </th>
                                    <th>
                                        {{ formatCurrency(effectiveTotal) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group mt-3">
                        <label for="notes">Observações</label>
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            class="form-control"
                            rows="2"
                            :class="{ 'is-invalid': form.errors.notes }"
                        ></textarea>
                        <div class="invalid-feedback">
                            {{ form.errors.notes }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="loading"
                        >
                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm mr-2"
                                role="status"
                                aria-hidden="true"
                            ></span>
                            <span v-if="loading">Processando...</span>
                            <span v-else>
                                <i class="fas fa-save"></i>
                                &nbsp; Registrar Pagamento
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
