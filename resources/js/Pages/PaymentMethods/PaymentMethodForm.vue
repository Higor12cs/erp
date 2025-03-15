<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";

const props = defineProps({
    paymentMethod: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["submit"]);

const form = useForm({
    _method: props.paymentMethod.id ? "PUT" : "POST",
    name: props.paymentMethod.name || "",
    type: props.paymentMethod.type || "",
    bank_name: props.paymentMethod.bank_name || "",
    agency: props.paymentMethod.agency || "",
    paymentMethod_number: props.paymentMethod.paymentMethod_number || "",
    paymentMethod_number: props.paymentMethod.paymentMethod_number || "",
    current_balance: props.paymentMethod.current_balance || "",
    active:
        props.paymentMethod.active !== undefined
            ? props.paymentMethod.active
            : true,
});

const submit = () => {
    emit("submit", form);
};

defineExpose({ form });
</script>

<template>
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-md-6">
                <InputField
                    id="name"
                    label="Nome"
                    v-model="form.name"
                    :error="form.errors.name"
                    required
                    autofocus
                />
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="type">
                        Tipo
                        <span class="text-danger">*</span>
                    </label>
                    <select
                        id="type"
                        class="form-control"
                        v-model="form.type"
                        :error="form.errors.type"
                        required
                    >
                        <option value="">-</option>
                        <option value="bank_slip">Boleto Bancário</option>
                        <option value="bank_transfer">
                            Transferência Bancária
                        </option>
                        <option value="cash">Dinheiro</option>
                        <option value="credit_card">Cartão de Crédito</option>
                        <option value="debit_card">Cartão de Débito</option>
                        <option value="pix">PIX</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="active" v-model="form.active" />
                    <label for="active">Ativo</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button
                type="submit"
                class="btn btn-primary"
                :disabled="processing"
            >
                <span
                    v-if="processing"
                    class="spinner-border spinner-border-sm mr-2"
                    role="status"
                    aria-hidden="true"
                ></span>
                <span v-if="processing">Salvando...</span>
                <span v-else>
                    <i class="fas fa-save"></i>
                    &nbsp; Salvar
                </span>
            </button>
        </div>
    </form>
</template>
