<template>
    <Head title="Nova Conta Bancária" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <CreditCard
                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Nova Conta Bancária
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Adicionar uma nova conta bancária à empresa
                    </p>
                </div>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <nav class="mb-6">
            <ol
                class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400"
            >
                <li>
                    <Link
                        :href="route('dashboard')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Dashboard
                    </Link>
                </li>
                <li>/</li>
                <li>
                    <Link
                        :href="route('bank-accounts.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Contas Bancárias
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Nova Conta</li>
            </ol>
        </nav>

        <!-- Form Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <form @submit.prevent="submit" class="p-6">
                <!-- Informações Básicas -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Informações Básicas
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome da Conta -->
                        <div>
                            <label
                                for="nome"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Nome da Conta
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="nome"
                                v-model="form.nome"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <p
                                v-if="form.errors.nome"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.nome }}
                            </p>
                        </div>

                        <!-- Banco -->
                        <div>
                            <label
                                for="banco"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Banco
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="banco"
                                v-model="form.banco"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <p
                                v-if="form.errors.banco"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.banco }}
                            </p>
                        </div>

                        <!-- IBAN -->
                        <div>
                            <label
                                for="iban"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                IBAN
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="iban"
                                v-model="form.iban"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="PT50 0000 0000 0000 0000 0000 0"
                                required
                            />
                            <p
                                v-if="form.errors.iban"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.iban }}
                            </p>
                        </div>

                        <!-- SWIFT/BIC -->
                        <div>
                            <label
                                for="swift_bic"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                SWIFT/BIC
                            </label>
                            <input
                                id="swift_bic"
                                v-model="form.swift_bic"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                maxlength="11"
                            />
                            <p
                                v-if="form.errors.swift_bic"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.swift_bic }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Detalhes Financeiros -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Detalhes Financeiros
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Saldo Inicial -->
                        <div>
                            <label
                                for="saldo_inicial"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Saldo Inicial
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="saldo_inicial"
                                v-model="form.saldo_inicial"
                                type="number"
                                step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <p
                                v-if="form.errors.saldo_inicial"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.saldo_inicial }}
                            </p>
                        </div>

                        <!-- Moeda -->
                        <div>
                            <label
                                for="moeda"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Moeda
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="moeda"
                                v-model="form.moeda"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="EUR">EUR (€)</option>
                                <option value="USD">USD ($)</option>
                                <option value="GBP">GBP (£)</option>
                            </select>
                            <p
                                v-if="form.errors.moeda"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.moeda }}
                            </p>
                        </div>

                        <!-- Tipo de Conta -->
                        <div>
                            <label
                                for="tipo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Tipo de Conta
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="tipo"
                                v-model="form.tipo"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="corrente">Corrente</option>
                                <option value="poupanca">Poupança</option>
                                <option value="credito">Crédito</option>
                                <option value="investimento">
                                    Investimento
                                </option>
                            </select>
                            <p
                                v-if="form.errors.tipo"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.tipo }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status e Observações -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Status e Observações
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Estado -->
                        <div>
                            <label
                                for="estado"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Estado
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="estado"
                                v-model="form.estado"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="ativa">Ativa</option>
                                <option value="inativa">Inativa</option>
                                <option value="encerrada">Encerrada</option>
                            </select>
                            <p
                                v-if="form.errors.estado"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.estado }}
                            </p>
                        </div>

                        <!-- Observações -->
                        <div class="md:col-span-2">
                            <label
                                for="observacoes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Observações
                            </label>
                            <textarea
                                id="observacoes"
                                v-model="form.observacoes"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                            <p
                                v-if="form.errors.observacoes"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.observacoes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div
                    class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700"
                >
                    <Link
                        :href="route('bank-accounts.index')"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="form.processing">A guardar...</span>
                        <span v-else>Criar Conta</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { CreditCard } from "lucide-vue-next";

const form = useForm({
    nome: "",
    banco: "",
    iban: "",
    swift_bic: "",
    saldo_inicial: 0,
    moeda: "EUR",
    tipo: "corrente",
    estado: "ativa",
    observacoes: "",
});

const submit = () => {
    form.post(route("bank-accounts.store"), {
        onSuccess: () => {
            // Redireciona para o index após sucesso
        },
    });
};
</script>
