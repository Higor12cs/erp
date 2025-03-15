<script setup>
import { useForm } from "@inertiajs/vue3";
import InputField from "@/Components/InputField.vue";
import { ref, computed } from "vue";

const props = defineProps({
    role: {
        type: Object,
        default: () => ({}),
    },
    processing: {
        type: Boolean,
        default: false,
    },
    permissions: {
        type: Array,
        required: true,
    },
    rolePermissions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["submit"]);

const form = useForm({
    _method: props.role.id ? "PUT" : "POST",
    name: props.role.name || "",
    permissions: props.rolePermissions || [],
});

const submit = () => {
    emit("submit", form);
};

const searchTerm = ref("");
const filteredPermissions = computed(() => {
    if (!searchTerm.value) return props.permissions;
    const search = searchTerm.value.toLowerCase();
    return props.permissions.filter((permission) =>
        permission.description.toLowerCase().includes(search)
    );
});

const selectAll = () => {
    const visiblePermissionIds = filteredPermissions.value.map((p) => p.id);
    form.permissions = [
        ...new Set([...form.permissions, ...visiblePermissionIds]),
    ];
};

const deselectAll = () => {
    const visiblePermissionIds = filteredPermissions.value.map((p) => p.id);
    form.permissions = form.permissions.filter(
        (id) => !visiblePermissionIds.includes(id)
    );
};

defineExpose({ form });
</script>

<template>
    <form @submit.prevent="submit">
        <div class="row">
            <div class="col-12">
                <InputField
                    id="name"
                    label="Nome do Papel"
                    v-model="form.name"
                    :error="form.errors.name"
                    required
                    autofocus
                />
            </div>
        </div>

        <div class="form-group">
            <label>Permissões</label>

            <div class="input-group mb-3">
                <input
                    type="text"
                    class="form-control"
                    v-model="searchTerm"
                    placeholder="Filtrar"
                />
                <div class="input-group-append">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        @click="selectAll"
                    >
                        Selecionar Todas
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        @click="deselectAll"
                    >
                        Desmarcar Todas
                    </button>
                </div>
            </div>

            <div class="card" style="max-height: 400px; overflow-y: auto">
                <div class="card-body">
                    <div class="row">
                        <div
                            v-for="permission in filteredPermissions"
                            :key="permission.id"
                            class="col-md-3 permission-item"
                        >
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input
                                        type="checkbox"
                                        :id="'permission' + permission.id"
                                        v-model="form.permissions"
                                        :value="permission.id"
                                    />
                                    <label :for="'permission' + permission.id">
                                        {{ permission.description }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
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
