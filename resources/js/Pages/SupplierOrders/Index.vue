<template>
    <Head title="Encomendas - Fornecedores" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <Package
                        class="h-6 w-6 text-green-600 dark:text-green-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Encomendas - Fornecedores
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir encomendas de fornecedores
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
                    Encomendas - Fornecedores
                </li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-4 flex-1">
                        <!-- Pesquisa -->
                        <div class="relative flex-1 max-w-md">
                            <Search
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400"
                            />
                            <input
                                type="text"
                                v-model="search"
                                @input="filterOrders"
                                placeholder="Pesquisar por número, fornecedor..."
                                class="pl-10 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Filtro de Estado -->
                        <select
                            v-model="statusFilter"
                            @change="filterOrders"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Todos os estados</option>
                            <option value="draft">Rascunho</option>
                            <option value="sent">Enviado</option>
                            <option value="confirmed">Confirmado</option>
                            <option value="received">Recebido</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>

                    <!-- Botão Criar -->
                    <Link
                        v-if="
                            $page.props.auth.permissions.includes(
                                'supplier-orders.create'
                            )
                        "
                        :href="route('supplier-orders.create')"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition duration-150"
                    >
                        <Plus class="h-5 w-5 mr-2" />
                        Nova Encomenda
                    </Link>
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
                                Entrega
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
                            v-for="order in orders.data"
                            :key="order.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                            >
                                {{ formatDate(order.order_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ order.number }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                            >
                                {{ order.supplier?.name || "-" }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                            >
                                {{ formatDate(order.delivery_date) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(order.total_value) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getStatusBadgeClass(order.status)"
                                >
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <div class="flex justify-end gap-2">
                                    <a
                                        :href="
                                            route(
                                                'supplier-orders.pdf',
                                                order.id
                                            )
                                        "
                                        class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300"
                                        title="Descarregar PDF"
                                    >
                                        <FileText class="h-5 w-5" />
                                    </a>
                                    <Link
                                        v-if="
                                            $page.props.auth.permissions.includes(
                                                'supplier-orders.update'
                                            )
                                        "
                                        :href="
                                            route(
                                                'supplier-orders.edit',
                                                order.id
                                            )
                                        "
                                        class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                        title="Editar"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        v-if="
                                            $page.props.auth.permissions.includes(
                                                'supplier-orders.delete'
                                            )
                                        "
                                        @click="confirmDelete(order.id)"
                                        class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                        title="Eliminar"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td
                                colspan="7"
                                class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
                            >
                                <Package
                                    class="h-12 w-12 mx-auto mb-4 opacity-30"
                                />
                                <p class="text-lg font-medium">
                                    Nenhuma encomenda encontrada
                                </p>
                                <p class="text-sm mt-1">
                                    Crie a sua primeira encomenda de fornecedor
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="orders.data.length > 0"
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando {{ orders.from }} a {{ orders.to }} de
                        {{ orders.total }} registos
                    </div>
                    <div class="flex gap-2">
                        <template
                            v-for="link in orders.links"
                            :key="link.label"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-1 text-sm rounded',
                                    link.active
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600',
                                ]"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-1 text-sm rounded bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 opacity-50 cursor-not-allowed"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            type="danger"
            title="Eliminar Encomenda"
            message="Tens a certeza que desejas eliminar esta encomenda?"
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            @confirm="deleteOrder"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import {
    Search,
    Plus,
    Pencil,
    Trash2,
    Package,
    FileText,
} from "lucide-vue-next";

const props = defineProps({
    orders: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const statusFilter = ref(props.filters.status || "");

const filterOrders = () => {
    router.get(
        route("supplier-orders.index"),
        {
            search: search.value,
            status: statusFilter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);

const confirmDelete = (id) => {
    itemToDelete.value = id;
    showDeleteDialog.value = true;
};

const deleteOrder = () => {
    router.delete(route("supplier-orders.destroy", itemToDelete.value), {
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

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value || 0);
};

const getStatusLabel = (status) => {
    const labels = {
        draft: "Rascunho",
        sent: "Enviado",
        confirmed: "Confirmado",
        received: "Recebido",
        cancelled: "Cancelado",
    };
    return labels[status] || status;
};

const getStatusBadgeClass = (status) => {
    const classes = {
        draft: "px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300",
        sent: "px-2 py-1 text-xs font-semibold rounded-full bg-blue-200 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300",
        confirmed:
            "px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300",
        received:
            "px-2 py-1 text-xs font-semibold rounded-full bg-green-200 dark:bg-green-900/40 text-green-700 dark:text-green-300",
        cancelled:
            "px-2 py-1 text-xs font-semibold rounded-full bg-red-200 dark:bg-red-900/40 text-red-700 dark:text-red-300",
    };
    return classes[status] || classes.draft;
};
</script>
