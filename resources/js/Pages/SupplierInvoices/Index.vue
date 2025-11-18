<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import {
    FileText,
    Plus,
    Eye,
    Pencil,
    Trash2,
    Download,
    Send,
} from "lucide-vue-next";

const props = defineProps({
    invoices: Object,
    suppliers: Array,
    filters: Object,
    can: {
        type: Object,
        default: () => ({
            create: false,
            view: true,
            edit: false,
            delete: false,
        }),
    },
});

// Filtros
const filterForm = ref({
    supplier_id: props.filters.supplier_id || "",
    estado: props.filters.estado || "",
    data_inicio: props.filters.data_inicio || "",
    data_fim: props.filters.data_fim || "",
    search: props.filters.search || "",
});

const applyFilters = () => {
    router.get("/supplier-invoices", filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    filterForm.value = {
        supplier_id: "",
        estado: "",
        data_inicio: "",
        data_fim: "",
        search: "",
    };
    applyFilters();
};

// Formatadores
const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};

const getEstadoBadgeClass = (estado) => {
    switch (estado) {
        case "paga":
            return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300";
        case "pendente":
            return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300";
    }
};

const getEstadoLabel = (estado) => {
    switch (estado) {
        case "paga":
            return "Paga";
        case "pendente":
            return "Pendente de Pagamento";
        default:
            return estado;
    }
};

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);

const confirmDelete = (invoice) => {
    itemToDelete.value = invoice;
    showDeleteDialog.value = true;
};

// Actions
const deleteInvoice = () => {
    router.delete(route("supplier-invoices.destroy", itemToDelete.value.id), {
        preserveScroll: true,
        onFinish: () => {
            showDeleteDialog.value = false;
            itemToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    itemToDelete.value = null;
};

const downloadDocument = (invoice) => {
    window.open(
        route("supplier-invoices.download-document", invoice.id),
        "_blank"
    );
};
</script>

<template>
    <Head title="Faturas de Fornecedores" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <FileText class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Faturas de Fornecedores
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gestão de faturas e pagamentos a fornecedores
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
                <li class="text-gray-900 dark:text-white">
                    Faturas de Fornecedores
                </li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-4">
                    <div
                        class="flex flex-col sm:flex-row justify-between gap-4"
                    >
                        <div class="flex flex-col sm:flex-row gap-4 flex-1">
                            <!-- Pesquisa -->
                            <div class="flex-1 max-w-md">
                                <input
                                    type="text"
                                    v-model="filterForm.search"
                                    placeholder="Pesquisar por número ou fornecedor..."
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @input="applyFilters"
                                />
                            </div>

                            <!-- Filtro Fornecedor -->
                            <select
                                v-model="filterForm.supplier_id"
                                @change="applyFilters"
                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Todos fornecedores</option>
                                <option
                                    v-for="supplier in suppliers"
                                    :key="supplier.id"
                                    :value="supplier.id"
                                >
                                    {{ supplier.name }}
                                </option>
                            </select>

                            <!-- Filtro Estado -->
                            <select
                                v-model="filterForm.estado"
                                @change="applyFilters"
                                class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Todos os estados</option>
                                <option value="pendente">Pendente</option>
                                <option value="paga">Paga</option>
                            </select>
                        </div>

                        <!-- Botão Criar -->
                        <Link
                            v-if="can.create"
                            :href="route('supplier-invoices.create')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition duration-150"
                        >
                            <Plus class="h-5 w-5 mr-2" />
                            Nova Fatura
                        </Link>
                    </div>

                    <!-- Filtros de Data (segunda linha) -->
                    <div class="flex gap-4">
                        <input
                            type="date"
                            v-model="filterForm.data_inicio"
                            @change="applyFilters"
                            placeholder="Data início"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <input
                            type="date"
                            v-model="filterForm.data_fim"
                            @change="applyFilters"
                            placeholder="Data fim"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                        <button
                            @click="clearFilters"
                            class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                        >
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Data
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Número
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Fornecedor
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Encomenda
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Documento
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Valor Total
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Estado
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="invoice in invoices.data"
                            :key="invoice.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                            >
                                {{ formatDate(invoice.data_fatura) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ invoice.numero }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                            >
                                {{ invoice.supplier?.name || "-" }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                            >
                                {{ invoice.supplier_order?.number || "-" }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button
                                    v-if="invoice.documento"
                                    @click="downloadDocument(invoice)"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <Download class="h-5 w-5" />
                                </button>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ formatCurrency(invoice.valor_total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getEstadoBadgeClass(invoice.estado)"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                >
                                    {{ getEstadoLabel(invoice.estado) }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <div
                                    class="flex items-center justify-end space-x-2"
                                >
                                    <Link
                                        v-if="can.view"
                                        :href="
                                            route(
                                                'supplier-invoices.show',
                                                invoice.id
                                            )
                                        "
                                        class="p-1.5 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors"
                                        title="Ver Detalhes"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link
                                        v-if="can.edit"
                                        :href="
                                            route(
                                                'supplier-invoices.edit',
                                                invoice.id
                                            )
                                        "
                                        class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                        title="Editar"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        v-if="can.delete"
                                        @click="confirmDelete(invoice)"
                                        class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                        title="Eliminar"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!invoices.data || invoices.data.length === 0">
                            <td
                                colspan="8"
                                class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                Nenhuma fatura encontrada.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div
                v-if="invoices.links && invoices.links.length > 3"
                class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="invoices.links[0].url"
                            :href="invoices.links[0].url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Anterior
                        </Link>
                        <Link
                            v-if="invoices.links[invoices.links.length - 1].url"
                            :href="
                                invoices.links[invoices.links.length - 1].url
                            "
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Próxima
                        </Link>
                    </div>
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                Mostrando
                                <span class="font-medium">{{
                                    invoices.from || 0
                                }}</span>
                                a
                                <span class="font-medium">{{
                                    invoices.to || 0
                                }}</span>
                                de
                                <span class="font-medium">{{
                                    invoices.total || 0
                                }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <template
                                    v-for="(link, index) in invoices.links"
                                    :key="index"
                                >
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400',
                                        ]"
                                        v-html="link.label"
                                    />
                                    <span
                                        v-else
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 dark:bg-gray-900 dark:border-gray-700',
                                        ]"
                                        v-html="link.label"
                                    />
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            type="danger"
            title="Eliminar Fatura"
            :message="
                itemToDelete
                    ? `Tens a certeza que desejas eliminar a fatura &quot;${itemToDelete.numero}&quot;?`
                    : ''
            "
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            @confirm="deleteInvoice"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>
