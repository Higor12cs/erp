<script setup>
import { ref, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    visible: Boolean,
    loading: Boolean,
    title: { type: String, default: "Confirmar Exclusão" },
    message: {
        type: String,
        default: "Você tem certeza que deseja excluir este item?",
    },
    warning: { type: String, default: "Esta ação não pode ser desfeita." },
    deleteRoute: { type: [String, Function], required: true },
    deleteRouteMethod: {
        type: String,
        default: "delete",
        validator: (value) =>
            ["delete", "post", "put", "patch"].includes(value),
    },
    successRedirect: String,
    successMessage: { type: String, default: "Item excluído com sucesso!" },
});

const emit = defineEmits(["cancel"]);
const isVisible = ref(props.visible);

const form = useForm({});

const initializeModal = () => {
    if (window.$) {
        $("#deleteConfirmationModal").modal({
            backdrop: "static",
            keyboard: false,
        });
        $("#deleteConfirmationModal").on("hidden.bs.modal", () =>
            emit("cancel")
        );
    }
};

watch(
    () => props.visible,
    (value) => {
        isVisible.value = value;
        if (value && window.$) {
            setTimeout(() => $("#deleteConfirmationModal").modal("show"), 100);
        }
    }
);

const closeModal = () => {
    if (window.$) $("#deleteConfirmationModal").modal("hide");
    emit("cancel");
};

const confirmDelete = (event) => {
    event.preventDefault();

    const routePath =
        typeof props.deleteRoute === "function"
            ? props.deleteRoute()
            : props.deleteRoute;

    form[props.deleteRouteMethod](routePath, {
        onSuccess: () => {
            closeModal();
        },
        preserveScroll: true,
    });
};

onMounted(() => {
    initializeModal();
});
</script>

<template>
    <div
        class="modal fade"
        id="deleteConfirmationModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form @submit="confirmDelete">
                    <div class="modal-header">
                        <h5
                            class="modal-title"
                            id="deleteConfirmationModalLabel"
                        >
                            {{ title }}
                        </h5>
                        <button
                            type="button"
                            class="close"
                            @click="closeModal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p>{{ message }}</p>
                        <p v-if="warning" class="text-danger">{{ warning }}</p>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="closeModal"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="btn btn-danger"
                            :disabled="form.processing"
                        >
                            <span
                                v-if="form.processing"
                                class="spinner-border spinner-border-sm mr-2"
                                role="status"
                                aria-hidden="true"
                            ></span>
                            <span v-if="form.processing">Excluindo...</span>
                            <span v-else>Excluir</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
