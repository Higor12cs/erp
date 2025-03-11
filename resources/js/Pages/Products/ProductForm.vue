<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import Select2 from "@/Components/Select2.vue";
import { ref } from "vue";

const props = defineProps({
    product: {
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
    _method: props.product.id ? "PUT" : "POST",
    brand_id: props.product.brand_id || null,
    group_id: props.product.group_id || null,
    name: props.product.name || "",
    description: props.product.description || "",
    sku: props.product.sku || "",
    cost: props.product.cost || "",
    price: props.product.price || "",
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

        <div class="form-group mt-3">
            <label for="description">Descrição</label>
            <textarea
                id="description"
                v-model="form.description"
                class="form-control"
                :class="{ 'is-invalid': form.errors.description }"
                rows="2"
            ></textarea>
            <div class="invalid-feedback">
                {{ form.errors.description }}
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <Select2
                    label="Marca"
                    v-model="form.brand_id"
                    :error="form.errors.brand_id"
                    :search-url="route('api.brands.search')"
                    value-key="id"
                    label-key="name"
                    placeholder="Pesquisar"
                />
            </div>

            <div class="col-6">
                <Select2
                    label="Grupo"
                    v-model="form.group_id"
                    :error="form.errors.group_id"
                    :search-url="route('api.groups.search')"
                    value-key="id"
                    label-key="name"
                    placeholder="Pesquisar"
                />
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <InputField
                    id="sku"
                    label="SKU"
                    v-model="form.sku"
                    :error="form.errors.sku"
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="cost"
                    label="Custo"
                    v-model="form.cost"
                    :error="form.errors.cost"
                    maskType="currency"
                    required
                />
            </div>
            <div class="col-md-4">
                <InputField
                    id="price"
                    label="Preço"
                    v-model="form.price"
                    :error="form.errors.price"
                    maskType="currency"
                    required
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
