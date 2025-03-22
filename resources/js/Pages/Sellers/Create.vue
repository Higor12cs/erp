<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import SellerForm from "@/Pages/Sellers/SellerForm.vue";
import { ref } from "vue";

const formRef = ref(null);

const handleSubmit = (form) => {
    form.post(route("sellers.store"));
};
</script>

<template>
    <Head title="Criar Vendedor" />
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h4>Criar Vendedor</h4>
                <Breadcrumb
                    :breadcrumb="[
                        { label: 'Home', routeName: 'home.index' },
                        { label: 'Vendedores', routeName: 'sellers.index' },
                        { label: 'Criar' },
                    ]"
                />
            </div>
            <Link
                :href="route('sellers.index')"
                class="btn btn-secondary mb-auto"
            >
                <i class="fas fa-sm fa-arrow-left"></i>
                &nbsp; Voltar
            </Link>
        </div>
        <div class="card">
            <div class="card-header">Cadastro de Vendedor</div>
            <div class="card-body">
                <SellerForm
                    ref="formRef"
                    :processing="formRef?.form?.processing"
                    @submit="handleSubmit"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
