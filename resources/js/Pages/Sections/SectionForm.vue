<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";

const props = defineProps({
    section: {
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
    _method: props.section.id ? "PUT" : "POST",
    name: props.section.name || "",
    active: props.section.active !== undefined ? props.section.active : true,
});

const submit = () => {
    emit("submit", form);
};

defineExpose({ form });
</script>

<template>
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-md-12">
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
