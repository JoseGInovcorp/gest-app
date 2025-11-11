<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { FileText } from "lucide-vue-next";

const props = defineProps({
    suppliers: Array,
    supplierOrders: Array,
});

const form = useForm({
    data_fatura: new Date().toISOString().split("T")[0],
    data_vencimento: "",
    supplier_id: "",
    supplier_order_id: "",
    valor_total: "",
    documento: null,
    estado: "pendente",
});

// Filtrar encomendas por fornecedor selecionado
const filteredOrders = ref([]);

watch(
    () => form.supplier_id,
    (newSupplierId) => {
        if (newSupplierId) {
            filteredOrders.value = props.supplierOrders.filter(
                (order) => order.supplier_id == newSupplierId
            );
        } else {
            filteredOrders.value = [];
        }
        // Limpar encomenda selecionada se fornecedor mudar
        form.supplier_order_id = "";
    }
);

const handleFileChange = (event) => {
    form.documento = event.target.files[0];
};

const submit = () => {
    form.post(route("supplier-invoices.store"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nova Fatura de Fornecedor" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center">
                    <div class="flex items-center space-x-4 flex-1">
                        <div
                            class="p-3 bg-red-100 rounded-full dark:bg-red-900"
                        >
                            <FileText
                                class="h-8 w-8 text-red-600 dark:text-red-300"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-3xl font-bold text-gray-900 dark:text-white"
                            >
                                Nova Fatura de Fornecedor
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                Registar nova fatura recebida de fornecedor
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Breadcrumbs -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                            >
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <span class="mx-2 text-gray-400">/</span>
                                <Link
                                    :href="route('supplier-invoices.index')"
                                    class="text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                                >
                                    Faturas de Fornecedores
                                </Link>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <span class="mx-2 text-gray-400">/</span>
                                <span
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Nova Fatura
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Formulário -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Linha 1: Datas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Data da Fatura -->
                            <div>
                                <label
                                    for="data_fatura"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Data da Fatura
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="data_fatura"
                                    v-model="form.data_fatura"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                />
                                <p
                                    v-if="form.errors.data_fatura"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.data_fatura }}
                                </p>
                            </div>

                            <!-- Data de Vencimento -->
                            <div>
                                <label
                                    for="data_vencimento"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Data de Vencimento
                                    <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="data_vencimento"
                                    v-model="form.data_vencimento"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                />
                                <p
                                    v-if="form.errors.data_vencimento"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.data_vencimento }}
                                </p>
                            </div>
                        </div>

                        <!-- Linha 2: Fornecedor e Encomenda -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fornecedor -->
                            <div>
                                <label
                                    for="supplier_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Fornecedor
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="supplier_id"
                                    v-model="form.supplier_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    <option
                                        v-for="supplier in suppliers"
                                        :key="supplier.id"
                                        :value="supplier.id"
                                    >
                                        {{ supplier.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.supplier_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.supplier_id }}
                                </p>
                            </div>

                            <!-- Encomenda Fornecedor (opcional) -->
                            <div>
                                <label
                                    for="supplier_order_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Encomenda Fornecedor (opcional)
                                </label>
                                <select
                                    id="supplier_order_id"
                                    v-model="form.supplier_order_id"
                                    :disabled="!form.supplier_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:bg-gray-100 dark:disabled:bg-gray-900"
                                >
                                    <option value="">Nenhuma</option>
                                    <option
                                        v-for="order in filteredOrders"
                                        :key="order.id"
                                        :value="order.id"
                                    >
                                        {{ order.number }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.supplier_order_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.supplier_order_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Linha 3: Valor e Estado -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Valor Total -->
                            <div>
                                <label
                                    for="valor_total"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Valor Total
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        id="valor_total"
                                        v-model="form.valor_total"
                                        step="0.01"
                                        min="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white pl-8"
                                        required
                                    />
                                    <span
                                        class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400"
                                        >€</span
                                    >
                                </div>
                                <p
                                    v-if="form.errors.valor_total"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.valor_total }}
                                </p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label
                                    for="estado"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="estado"
                                    v-model="form.estado"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                >
                                    <option value="pendente">
                                        Pendente de Pagamento
                                    </option>
                                    <option value="paga">Paga</option>
                                </select>
                                <p
                                    v-if="form.errors.estado"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.estado }}
                                </p>
                            </div>
                        </div>

                        <!-- Upload Documento -->
                        <div>
                            <label
                                for="documento"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Documento da Fatura (PDF, JPG, PNG - máx 5MB)
                            </label>
                            <input
                                type="file"
                                id="documento"
                                @change="handleFileChange"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                            />
                            <p
                                v-if="form.errors.documento"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.documento }}
                            </p>
                        </div>

                        <!-- Botões -->
                        <div
                            class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700"
                        >
                            <Link
                                :href="route('supplier-invoices.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">A guardar...</span>
                                <span v-else>Criar Fatura</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
