<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

const appName = import.meta.env.VITE_APP_NAME || "";

const form = useForm({
    tenant_name: "Acme Inc",
    name: "Usuário Teste",
    email: "test@example.com",
    password: "password",
    password_confirmation: "password",
});

const submit = () => {
    form.post(route("register.attempt"));
};
</script>

<template>
    <Head title="Register" />

    <div class="hold-transition register-page">
        <div class="register-box">
            <div class="card card-outline card-primary">
                <div class="card-header text-center">
                    <Link href="/" class="h3">
                        {{ appName }}
                    </Link>
                </div>
                <div class="card-body">
                    <p class="login-box-msg">Registre uma nova conta</p>

                    <form @submit.prevent="submit">
                        <div class="input-group mb-3">
                            <input
                                id="tenant_name"
                                type="text"
                                class="form-control"
                                placeholder="Nome da empresa"
                                v-model="form.tenant_name"
                                required
                                autofocus
                            />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-building"></span>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="form.errors.tenant_name"
                            class="invalid-feedback"
                        >
                            {{ form.errors.tenant_name }}
                        </div>

                        <div class="input-group mb-3">
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                placeholder="Nome"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.name" class="invalid-feedback">
                            {{ form.errors.name }}
                        </div>

                        <div class="input-group mb-3">
                            <input
                                id="email"
                                type="email"
                                class="form-control"
                                placeholder="Email"
                                v-model="form.email"
                                required
                                autocomplete="username"
                            />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <div v-if="form.errors.email" class="invalid-feedback">
                            {{ form.errors.email }}
                        </div>

                        <div class="input-group mb-3">
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                placeholder="Senha"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                            />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="form.errors.password"
                            class="invalid-feedback"
                        >
                            {{ form.errors.password }}
                        </div>

                        <div class="input-group mb-3">
                            <input
                                id="password_confirmation"
                                type="password"
                                class="form-control"
                                placeholder="Confirme a senha"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="form.errors.password_confirmation"
                            class="invalid-feedback"
                        >
                            {{ form.errors.password_confirmation }}
                        </div>

                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <Link href="/login" class="text-center">
                                        Já tenho uma conta
                                    </Link>
                                </div>
                            </div>
                            <div class="col-4">
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-block"
                                    :disabled="form.processing"
                                    style="border-radius: 0"
                                >
                                    <span
                                        v-if="form.processing"
                                        class="spinner-border spinner-border-sm mr-2"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
                                    <span v-else>Registrar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
