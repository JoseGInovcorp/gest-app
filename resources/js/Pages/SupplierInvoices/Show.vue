<template>
    <Head title="Detalhes da Fatura" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <FileText
                            class="h-6 w-6 text-red-600 dark:text-red-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Detalhes da Fatura
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ invoice.numero }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('supplier-invoices.index')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Voltar
                    </Link>
                    <Link
                        v-if="
                            $page.props.auth.permissions.includes(
                                'supplier-invoices.update'
                            )
                        "
                        :href="route('supplier-invoices.edit', invoice.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <Pencil class="h-4 w-4" />
                        Editar
                    </Link>
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
                        :href="route('supplier-invoices.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Faturas de Fornecedores
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Detalhes</li>
            </ol>
        </nav>

        <!-- Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info Card -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Informações da Fatura
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Número -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Número
                                </label>
                                <p
                                    class="text-base font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ invoice.numero }}
                                </p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Estado
                                </label>
                                <p>
                                    <span
                                        :class="[
                                            'px-3 py-1 text-sm rounded-full font-medium',
                                            invoice.estado === 'paga'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                        ]"
                                    >
                                        {{
                                            invoice.estado === "paga"
                                                ? "Paga"
                                                : "Pendente de Pagamento"
                                        }}
                                    </span>
                                </p>
                            </div>

                            <!-- Fornecedor -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Fornecedor
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ invoice.supplier?.nome || "-" }}
                                </p>
                            </div>

                            <!-- Encomenda -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Encomenda Fornecedor
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{
                                        invoice.supplier_order?.number ||
                                        "Nenhuma"
                                    }}
                                </p>
                            </div>

                            <!-- Data da Fatura -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Data da Fatura
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ formatDate(invoice.data_fatura) }}
                                </p>
                            </div>

                            <!-- Data de Vencimento -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Data de Vencimento
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ formatDate(invoice.data_vencimento) }}
                                </p>
                            </div>

                            <!-- Valor Total -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Valor Total
                                </label>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(invoice.valor_total) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Documentos -->
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Documentos
                            </h3>

                            <div class="space-y-3">
                                <!-- Documento da Fatura -->
                                <div
                                    v-if="invoice.documento"
                                    class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                                >
                                    <div class="flex items-center space-x-3">
                                        <FileText
                                            class="h-5 w-5 text-gray-500 dark:text-gray-400"
                                        />
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                Fatura
                                            </p>
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                Documento original
                                            </p>
                                        </div>
                                    </div>
                                    <a
                                        :href="`/storage/${invoice.documento}`"
                                        target="_blank"
                                        class="text-blue-600 hover:text-blue-700 dark:text-blue-400"
                                    >
                                        <Download class="h-5 w-5" />
                                    </a>
                                </div>
                                <div
                                    v-else
                                    class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-500 dark:text-gray-400 text-center"
                                >
                                    Sem documento
                                </div>

                                <!-- Comprovativo de Pagamento -->
                                <div
                                    v-if="invoice.comprovativo_pagamento"
                                    class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg"
                                >
                                    <div class="flex items-center space-x-3">
                                        <FileText
                                            class="h-5 w-5 text-green-600 dark:text-green-400"
                                        />
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                Comprovativo
                                            </p>
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                Pagamento enviado
                                            </p>
                                        </div>
                                    </div>
                                    <a
                                        :href="`/storage/${invoice.comprovativo_pagamento}`"
                                        target="_blank"
                                        class="text-green-600 hover:text-green-700 dark:text-green-400"
                                    >
                                        <Download class="h-5 w-5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadados -->
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                            >
                                Informações do Sistema
                            </h3>

                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                    >
                                        Criado em
                                    </label>
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ formatDateTime(invoice.created_at) }}
                                    </p>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                    >
                                        Última atualização
                                    </label>
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ formatDateTime(invoice.updated_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FileText, Pencil, Download, ArrowLeft } from "lucide-vue-next";

defineProps({
    invoice: Object,
});

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("pt-PT");
};

const formatDateTime = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};
</script>
