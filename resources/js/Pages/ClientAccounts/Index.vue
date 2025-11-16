<template>
    <Head title="Conta Corrente Clientes" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <DollarSign
                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Conta Corrente Clientes
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Acompanhamento de débitos, créditos e saldos
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
                    Conta Corrente Clientes
                </li>
            </ol>
        </nav>

        <!-- Estatísticas do Cliente (se selecionado) -->
        <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700"
            >
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    Cliente
                </div>
                <div
                    class="text-lg font-semibold text-gray-900 dark:text-white"
                >
                    {{ stats.entity_name }}
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700"
            >
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    Total Débitos
                </div>
                <div
                    class="text-lg font-semibold text-red-600 dark:text-red-400"
                >
                    {{ formatCurrency(stats.total_debitos) }}
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700"
            >
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    Total Créditos
                </div>
                <div
                    class="text-lg font-semibold text-green-600 dark:text-green-400"
                >
                    {{ formatCurrency(stats.total_creditos) }}
                </div>
            </div>
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700"
            >
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                    Saldo Atual
                </div>
                <div
                    :class="[
                        'text-lg font-semibold',
                        stats.saldo_atual > 0
                            ? 'text-red-600 dark:text-red-400'
                            : stats.saldo_atual < 0
                            ? 'text-green-600 dark:text-green-400'
                            : 'text-gray-600 dark:text-gray-400',
                    ]"
                >
                    {{ formatCurrency(stats.saldo_atual) }}
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-4">
                    <!-- Primeira linha: Cliente e Pesquisa -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Seleção de Cliente -->
                        <div class="flex-1">
                            <label
                                for="entity_filter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Cliente
                            </label>
                            <select
                                id="entity_filter"
                                v-model="filters.entity_id"
                                @change="applyFilters"
                                class="w-full pl-3 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="null">Todos os Clientes</option>
                                <option
                                    v-for="entity in entities"
                                    :key="entity.id"
                                    :value="entity.id"
                                >
                                    {{ entity.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Pesquisa -->
                        <div class="flex-1">
                            <label
                                for="search"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Pesquisar
                            </label>
                            <div class="relative">
                                <Search
                                    class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                                />
                                <input
                                    id="search"
                                    v-model="filters.search"
                                    @input="debouncedSearch"
                                    type="text"
                                    placeholder="Descrição ou referência..."
                                    class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                />
                            </div>
                        </div>

                        <!-- Botão Novo Movimento -->
                        <div class="flex items-end">
                            <Link
                                :href="route('client-accounts.create')"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <Plus class="h-4 w-4" />
                                Novo Movimento
                            </Link>
                        </div>
                    </div>

                    <!-- Segunda linha: Filtros -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Tipo -->
                        <div class="flex-1">
                            <label
                                for="tipo_filter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Tipo
                            </label>
                            <select
                                id="tipo_filter"
                                v-model="filters.tipo"
                                @change="applyFilters"
                                class="w-full pl-3 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="null">Todos</option>
                                <option value="debito">Débito</option>
                                <option value="credito">Crédito</option>
                            </select>
                        </div>

                        <!-- Categoria -->
                        <div class="flex-1">
                            <label
                                for="categoria_filter"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Categoria
                            </label>
                            <select
                                id="categoria_filter"
                                v-model="filters.categoria"
                                @change="applyFilters"
                                class="w-full pl-3 pr-8 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="null">Todas</option>
                                <option value="fatura">Fatura</option>
                                <option value="pagamento">Pagamento</option>
                                <option value="nota_credito">
                                    Nota Crédito
                                </option>
                                <option value="nota_debito">Nota Débito</option>
                                <option value="juros">Juros</option>
                                <option value="ajuste">Ajuste</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>

                        <!-- Data Início -->
                        <div class="flex-1">
                            <label
                                for="start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Data Início
                            </label>
                            <input
                                id="start_date"
                                v-model="filters.start_date"
                                @change="applyFilters"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <!-- Data Fim -->
                        <div class="flex-1">
                            <label
                                for="end_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Data Fim
                            </label>
                            <input
                                id="end_date"
                                v-model="filters.end_date"
                                @change="applyFilters"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <DataTable
                    :columns="columns"
                    :data="movements?.data || []"
                    :loading="false"
                >
                    <template #cell-data_movimento="{ item }">
                        <span class="whitespace-nowrap">{{ formatDate(item.data_movimento) }}</span>
                    </template>

                    <template #cell-cliente="{ item }">
                        <span>{{ item.entity?.name }}</span>
                    </template>

                    <template #cell-descricao="{ item }">
                        <span>{{ item.descricao }}</span>
                    </template>

                    <template #cell-categoria="{ item }">
                        <span
                            :class="[
                                'px-2 py-1 text-xs rounded-full',
                                getCategoryBadgeClass(item.categoria),
                            ]"
                        >
                            {{ formatCategory(item.categoria) }}
                        </span>
                    </template>

                    <template #cell-referencia="{ item }">
                        <span>{{ item.referencia || "-" }}</span>
                    </template>

                    <template #cell-debito="{ item }">
                        <span
                            v-if="item.tipo === 'debito'"
                            class="text-red-600 dark:text-red-400 font-medium"
                        >
                            {{ formatCurrency(item.valor) }}
                        </span>
                        <span v-else class="text-gray-400">-</span>
                    </template>

                    <template #cell-credito="{ item }">
                        <span
                            v-if="item.tipo === 'credito'"
                            class="text-green-600 dark:text-green-400 font-medium"
                        >
                            {{ formatCurrency(item.valor) }}
                        </span>
                        <span v-else class="text-gray-400">-</span>
                    </template>

                    <template #cell-saldo="{ item }">
                        <span
                            :class="[
                                'font-medium',
                                item.saldo_apos > 0
                                    ? 'text-red-600 dark:text-red-400'
                                    : item.saldo_apos < 0
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-gray-600 dark:text-gray-400',
                            ]"
                        >
                            {{ formatCurrency(item.saldo_apos) }}
                        </span>
                    </template>

                    <template #cell-acoes="{ item }">
                        <div class="flex items-center justify-center gap-1">
                            <Link
                                v-if="item.invoice_id"
                                :href="route('client-accounts.pdf', item.id)"
                                target="_blank"
                                class="p-1.5 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded transition-colors"
                                title="Download PDF Fatura"
                            >
                                <FileText class="h-4 w-4" />
                            </Link>
                            <Link
                                :href="route('client-accounts.show', item.id)"
                                class="p-1.5 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400"
                                title="Ver detalhes"
                            >
                                <Eye class="h-4 w-4" />
                            </Link>
                            <Link
                                :href="route('client-accounts.edit', item.id)"
                                class="p-1.5 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400"
                                title="Editar"
                            >
                                <Pencil class="h-4 w-4" />
                            </Link>
                            <button
                                @click="deleteMovement(item.id)"
                                class="p-1.5 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400"
                                title="Eliminar"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </template>

                    <template #empty>
                        <div class="text-center py-12">
                            <DollarSign class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <p class="text-gray-500 dark:text-gray-400">
                                Nenhum movimento encontrado.
                            </p>
                        </div>
                    </template>
                </DataTable>
            </div>

            <!-- Pagination -->
            <div
                v-if="movements.data.length > 0"
                class="p-6 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Mostrando {{ movements.from }} a {{ movements.to }} de
                        {{ movements.total }} movimentos
                    </div>
                    <div class="flex gap-2">
                        <template
                            v-for="link in movements.links"
                            :key="link.label"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="[
                                    'px-3 py-1 rounded-lg text-sm',
                                    link.active
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600',
                                ]"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                :class="[
                                    'px-3 py-1 rounded-lg text-sm',
                                    'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-600 cursor-not-allowed',
                                ]"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/ui/DataTable.vue";
import { DollarSign, Search, Plus, Eye, Pencil, Trash2, FileText } from "lucide-vue-next";

const props = defineProps({
    movements: Object,
    entities: Array,
    stats: Object,
    filters: Object,
});

const filters = ref({
    entity_id: props.filters.entity_id,
    tipo: props.filters.tipo,
    categoria: props.filters.categoria,
    start_date: props.filters.start_date,
    end_date: props.filters.end_date,
    search: props.filters.search,
});

// Colunas da tabela
const columns = computed(() => [
    { key: "data_movimento", title: "Data", sortable: true },
    { key: "cliente", title: "Cliente", sortable: true },
    { key: "descricao", title: "Descrição", sortable: false },
    { key: "categoria", title: "Categoria", sortable: true },
    { key: "referencia", title: "Referência", sortable: false },
    { key: "debito", title: "Débito", sortable: false, class: "text-right" },
    { key: "credito", title: "Crédito", sortable: false, class: "text-right" },
    { key: "saldo", title: "Saldo", sortable: false, class: "text-right" },
    { key: "acoes", title: "Ações", sortable: false, class: "text-center" },
]);

let searchTimeout;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

const applyFilters = () => {
    router.get(
        route("client-accounts.index"),
        {
            entity_id: filters.value.entity_id,
            tipo: filters.value.tipo,
            categoria: filters.value.categoria,
            start_date: filters.value.start_date,
            end_date: filters.value.end_date,
            search: filters.value.search,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const deleteMovement = (id) => {
    if (confirm("Tem a certeza que deseja eliminar este movimento?")) {
        router.delete(route("client-accounts.destroy", id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};

const formatCategory = (category) => {
    const categories = {
        fatura: "Fatura",
        pagamento: "Pagamento",
        nota_credito: "Nota Crédito",
        nota_debito: "Nota Débito",
        juros: "Juros",
        ajuste: "Ajuste",
        outros: "Outros",
    };
    return categories[category] || category;
};

const getCategoryBadgeClass = (category) => {
    const classes = {
        fatura: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        pagamento:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        nota_credito:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        nota_debito:
            "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        juros: "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
        ajuste: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        outros: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400",
    };
    return (
        classes[category] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400"
    );
};
</script>
