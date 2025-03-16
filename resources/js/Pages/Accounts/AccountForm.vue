<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";

const props = defineProps({
    account: {
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
    _method: props.account.id ? "PUT" : "POST",
    name: props.account.name || "",
    type: props.account.type || "",
    bank_name: props.account.bank_name || "",
    agency: props.account.agency || "",
    account_number: props.account.account_number || "",
    account_number: props.account.account_number || "",
    current_balance: props.account.current_balance || "",
    active: props.account.active !== undefined ? props.account.active : true,
});

const submit = () => {
    emit("submit", form);
};

defineExpose({ form });
</script>

<template>
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-12">
                <InputField
                    id="name"
                    label="Nome"
                    v-model="form.name"
                    :error="form.errors.name"
                    required
                    autofocus
                />
            </div>
        </div>

        <div class="row">
            <div class="col-6">
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
                        <option value="checking">Conta Corrente</option>
                        <option value="cash">Caixa</option>
                    </select>
                </div>
            </div>
            <div class="col-6">
                <InputField
                    id="bank_name"
                    label="Banco"
                    v-model="form.bank_name"
                    :error="form.errors.bank_name"
                />
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <InputField
                    id="bank_name"
                    label="Banco"
                    v-model="form.bank_name"
                    :error="form.errors.bank_name"
                />
            </div>
            <div class="col-4">
                <InputField
                    id="agency"
                    label="Agência"
                    v-model="form.agency"
                    :error="form.errors.agency"
                />
            </div>
            <div class="col-4">
                <InputField
                    id="account_number"
                    label="Número da Conta"
                    v-model="form.account_number"
                    :error="form.errors.account_number"
                />
            </div>
        </div>

        <div class="row">
            <div class="col-12">
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
