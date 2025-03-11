<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import { ref, watch } from "vue";

const props = defineProps({
    customer: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["submit"]);
const isLoadingCep = ref(false);
const cepError = ref("");

const form = useForm({
    _method: props.customer.id ? "PUT" : "POST",
    first_name: props.customer.first_name || "",
    last_name: props.customer.last_name || "",
    legal_name: props.customer.legal_name || "",
    cpf_cnpj: props.customer.cpf_cnpj || "",
    rg: props.customer.rg || "",
    ie: props.customer.ie || "",
    birth_date: props.customer.birth_date
        ? new Date(props.customer.birth_date).toISOString().split("T")[0]
        : "",
    email: props.customer.email || "",
    phone: props.customer.phone || "",
    whatsapp: props.customer.whatsapp || "",
    zip_code: props.customer.zip_code || "",
    address: props.customer.address || "",
    number: props.customer.number || "",
    complement: props.customer.complement || "",
    neighborhood: props.customer.neighborhood || "",
    city: props.customer.city || "",
    state: props.customer.state || "",
    country: props.customer.country || "Brasil",
});

const fetchAddressFromCep = async (cep) => {
    cepError.value = "";

    const cleanCep = cep.replace(/\D/g, "");

    if (cleanCep.length !== 8) {
        return;
    }

    try {
        isLoadingCep.value = true;
        const response = await fetch(
            `https://viacep.com.br/ws/${cleanCep}/json/`
        );
        const data = await response.json();

        if (data.erro) {
            cepError.value = "CEP não encontrado";
            return;
        }

        form.address = data.logradouro || "";
        form.neighborhood = data.bairro || "";
        form.city = data.localidade || "";
        form.state = data.uf || "";
        form.country = "Brasil";

        if (data.logradouro) {
            document.getElementById("number")?.focus();
        }
    } catch (error) {
        cepError.value = "Erro ao buscar o CEP";
        console.error("ViaCEP error:", error);
    } finally {
        isLoadingCep.value = false;
    }
};

watch(
    () => form.zip_code,
    (newValue) => {
        const cleanCep = newValue.replace(/\D/g, "");
        if (cleanCep.length === 8) {
            fetchAddressFromCep(cleanCep);
        }
    }
);

const submit = () => {
    emit("submit", form);
};

defineExpose({ form });
</script>

<template>
    <form @submit.prevent="submit">
        <div class="row">
            <!-- Dados Pessoais -->
            <div class="col-md-6">
                <InputField
                    id="first_name"
                    label="Nome"
                    v-model="form.first_name"
                    :error="form.errors.first_name"
                    required
                    autofocus
                />
            </div>
            <div class="col-md-6">
                <InputField
                    id="last_name"
                    label="Sobrenome"
                    v-model="form.last_name"
                    :error="form.errors.last_name"
                />
            </div>
            <div class="col-md-6">
                <InputField
                    id="legal_name"
                    label="Razão Social"
                    v-model="form.legal_name"
                    :error="form.errors.legal_name"
                />
            </div>
            <div class="col-md-6">
                <InputField
                    id="cpf_cnpj"
                    label="CPF/CNPJ"
                    v-model="form.cpf_cnpj"
                    :error="form.errors.cpf_cnpj"
                    maskType="cpf-cnpj"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="rg"
                    label="RG"
                    v-model="form.rg"
                    :error="form.errors.rg"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="ie"
                    label="IE"
                    v-model="form.ie"
                    :error="form.errors.ie"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="birth_date"
                    label="Data de Nascimento"
                    type="date"
                    v-model="form.birth_date"
                    :error="form.errors.birth_date"
                />
            </div>

            <!-- Contato -->
            <div class="col-md-4">
                <InputField
                    id="email"
                    label="Email"
                    type="email"
                    v-model="form.email"
                    :error="form.errors.email"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="phone"
                    label="Telefone"
                    v-model="form.phone"
                    :error="form.errors.phone"
                    maskType="phone"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="whatsapp"
                    label="WhatsApp"
                    v-model="form.whatsapp"
                    :error="form.errors.whatsapp"
                    maskType="phone"
                />
            </div>

            <!-- Endereço -->
            <div class="col-md-3">
                <InputField
                    id="zip_code"
                    label="CEP"
                    v-model="form.zip_code"
                    :error="form.errors.zip_code || cepError"
                    maskType="cep"
                    :disabled="isLoadingCep"
                />
            </div>
            <div class="col-md-7">
                <InputField
                    id="address"
                    label="Endereço"
                    v-model="form.address"
                    :error="form.errors.address"
                />
            </div>
            <div class="col-md-2">
                <InputField
                    id="number"
                    label="Número"
                    v-model="form.number"
                    :error="form.errors.number"
                />
            </div>
            <div class="col-md-6">
                <InputField
                    id="complement"
                    label="Complemento"
                    v-model="form.complement"
                    :error="form.errors.complement"
                />
            </div>
            <div class="col-md-6">
                <InputField
                    id="neighborhood"
                    label="Bairro"
                    v-model="form.neighborhood"
                    :error="form.errors.neighborhood"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="city"
                    label="Cidade"
                    v-model="form.city"
                    :error="form.errors.city"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="state"
                    label="Estado"
                    v-model="form.state"
                    :error="form.errors.state"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="country"
                    label="País"
                    v-model="form.country"
                    :error="form.errors.country"
                />
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
