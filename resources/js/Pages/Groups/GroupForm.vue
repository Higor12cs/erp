<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import Select2 from "@/Components/Select2.vue";

const props = defineProps({
    group: {
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
    _method: props.group.id ? "PUT" : "POST",
    name: props.group.name || "",
    section_id: props.group.section_id || "",
});

const handleSectionChange = (value) => {
    //
};

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
                <Select2
                    label="Seção"
                    v-model="form.section_id"
                    :error="form.errors.section_id"
                    :search-url="route('api.sections.search')"
                    value-key="id"
                    label-key="name"
                    placeholder="Pesquisar"
                    required
                    @change="handleSectionChange"
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
