<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    UserCheck,
    Plus,
    Search,
    Filter,
    Download,
    Eye,
    Edit,
    Trash2,
    Globe,
    CheckCircle,
    XCircle,
} from "lucide-vue-next";

const props = defineProps({
    entities: Object,
    filters: Object,
    pageTitle: String,
    entityType: String,
    can: Object,
});

// Estado local
const searchQuery = ref(props.filters?.search || "");
const activeFilter = ref(
    props.filters?.active !== undefined ? props.filters.active : null
);

// Computed
const hasFilters = computed(() => {
    return searchQuery.value || activeFilter.value !== null;
});

const clearFilters = () => {
    searchQuery.value = "";
    activeFilter.value = null;
    // Redirecionar para limpar filtros
    router.get(route("suppliers.index"));
};

const applyFilters = () => {
    const params = {};

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (activeFilter.value !== null) {
        params.active = activeFilter.value;
    }

    router.get(route("suppliers.index"), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Debounce timeout
let filterTimeout = null;

// Watchers para aplicar filtros automaticamente
watch([searchQuery, activeFilter], () => {
    if (filterTimeout) {
        clearTimeout(filterTimeout);
    }
    filterTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const getStatusBadge = (entity) => {
    if (!entity.active) {
        return {
            color: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300",
            text: "Inativo",
        };
    }
    return {
        color: "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300",
        text: "Ativo",
    };
};

const getTypeBadge = (type) => {
    const badges = {
        client: {
            color: "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300",
            text: "Cliente",
        },
        supplier: {
            color: "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-300",
            text: "Fornecedor",
        },
        both: {
            color: "bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-300",
            text: "Cliente/Fornecedor",
        },
    };
    return badges[type] || badges.supplier;
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("pt-PT", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-600/10 dark:bg-purple-400/10"
                        >
                            <UserCheck
                                class="h-6 w-6 text-purple-600 dark:text-purple-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ pageTitle }}
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Gestão completa de fornecedores e parceiros
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center space-x-3 sm:mt-0">
                        <button
                            v-if="can.export"
                            type="button"
                            class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors"
                        >
                            <Download class="h-4 w-4 mr-2" />
                            Exportar
                        </button>

                        <Link
                            v-if="can.create"
                            :href="route('suppliers.create')"
                            class="inline-flex items-center rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 transition-colors"
                        >
                            <Plus class="h-4 w-4 mr-2" />
                            Novo Fornecedor
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4"
            >
                <div
                    class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-4"
                >
                    <!-- Search -->
                    <div class="flex-1">
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Pesquisar fornecedores..."
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 pl-10 pr-3 py-2 text-sm placeholder-gray-500 dark:placeholder-gray-400 focus:border-purple-500 focus:ring-purple-500"
                            />
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select
                            v-model="activeFilter"
                            class="block rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 pl-3 pr-8 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 min-w-[140px]"
                        >
                            <option :value="null">Todos os status</option>
                            <option :value="true">Apenas ativos</option>
                            <option :value="false">Apenas inativos</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <button
                        v-if="hasFilters"
                        @click="clearFilters"
                        type="button"
                        class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                    >
                        Limpar Filtros
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div
                class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900"
            >
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-800"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Fornecedor
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Contacto
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Fiscal
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                                >
                                    Criado
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Ações</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-800"
                        >
                            <tr
                                v-for="entity in entities.data"
                                :key="entity.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                            >
                                <!-- Fornecedor -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="h-10 w-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center"
                                            >
                                                <span
                                                    class="text-sm font-medium text-white"
                                                >
                                                    {{
                                                        entity.name
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-medium text-gray-900 dark:text-white"
                                            >
                                                {{ entity.name }}
                                            </div>
                                            <div
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                NIF:
                                                {{ entity.tax_number || "-" }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contacto -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        {{ entity.email || "-" }}
                                    </div>
                                    <div
                                        class="text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{ entity.phone || "-" }}
                                    </div>
                                </td>

                                <!-- Fiscal -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div>
                                            <div
                                                class="text-sm text-gray-900 dark:text-white"
                                            >
                                                {{ entity.tax_number || "-" }}
                                            </div>
                                            <div
                                                v-if="
                                                    entity.country_code &&
                                                    entity.country_code !== 'PT'
                                                "
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{ entity.country_code }}
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                entity.vat_number &&
                                                entity.vies_last_check
                                            "
                                        >
                                            <CheckCircle
                                                v-if="entity.vies_valid"
                                                class="h-4 w-4 text-green-500"
                                                title="VAT válido (VIES)"
                                            />
                                            <XCircle
                                                v-else
                                                class="h-4 w-4 text-red-500"
                                                title="VAT inválido (VIES)"
                                            />
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="getStatusBadge(entity).color"
                                        class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                    >
                                        {{ getStatusBadge(entity).text }}
                                    </span>
                                    <div class="mt-1">
                                        <span
                                            :class="
                                                getTypeBadge(entity.type).color
                                            "
                                            class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                        >
                                            {{ getTypeBadge(entity.type).text }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Criado -->
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ formatDate(entity.created_at) }}
                                </td>

                                <!-- Ações -->
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <div
                                        class="flex items-center justify-end space-x-2"
                                    >
                                        <Link
                                            :href="
                                                route(
                                                    'suppliers.show',
                                                    entity.id
                                                )
                                            "
                                            class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300 transition-colors"
                                            title="Ver detalhes"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>

                                        <Link
                                            v-if="can.edit"
                                            :href="
                                                route(
                                                    'suppliers.edit',
                                                    entity.id
                                                )
                                            "
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 transition-colors"
                                            title="Editar"
                                        >
                                            <Edit class="h-4 w-4" />
                                        </Link>

                                        <button
                                            v-if="can.delete"
                                            type="button"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors"
                                            title="Eliminar"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div
                    v-if="entities.data.length === 0"
                    class="text-center py-12"
                >
                    <UserCheck class="mx-auto h-12 w-12 text-gray-400" />
                    <h3
                        class="mt-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Nenhum fornecedor encontrado
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Comece por criar o seu primeiro fornecedor.
                    </p>
                    <div class="mt-6">
                        <Link
                            v-if="can.create"
                            :href="route('suppliers.create')"
                            class="inline-flex items-center rounded-lg bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600"
                        >
                            <Plus class="h-4 w-4 mr-2" />
                            Novo Fornecedor
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="entities.data.length > 0"
                class="flex items-center justify-between"
            >
                <div class="flex-1 flex justify-between sm:hidden">
                    <Link
                        v-if="entities.prev_page_url"
                        :href="entities.prev_page_url"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        Anterior
                    </Link>
                    <Link
                        v-if="entities.next_page_url"
                        :href="entities.next_page_url"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        Próximo
                    </Link>
                </div>

                <div
                    class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                >
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Mostrando
                            <span class="font-medium">{{ entities.from }}</span>
                            a
                            <span class="font-medium">{{ entities.to }}</span>
                            de
                            <span class="font-medium">{{
                                entities.total
                            }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        <nav
                            class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                            aria-label="Pagination"
                        >
                            <!-- Pagination links would go here -->
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom styles if needed */
</style>
