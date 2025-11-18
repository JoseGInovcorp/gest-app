<template>
    <Head title="Propostas" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <FileText
                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Propostas
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir propostas de clientes
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
                <li class="text-gray-900 dark:text-white">Propostas</li>
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
                                @input="filterProposals"
                                placeholder="Pesquisar por número, cliente..."
                                class="pl-10 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Filtro de Estado -->
                        <select
                            v-model="estadoFilter"
                            @change="filterProposals"
                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Todos os estados</option>
                            <option value="rascunho">Rascunho</option>
                            <option value="fechado">Fechado</option>
                        </select>
                    </div>

                    <!-- Botão Criar -->
                    <Link
                        v-if="
                            $page.props.auth.permissions.includes(
                                'proposals.create'
                            )
                        "
                        :href="route('proposals.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition duration-150"
                    >
                        <Plus class="h-5 w-5 mr-2" />
                        Nova Proposta
                    </Link>
                </div>
            </div>

            <!-- DataTable -->
            <DataTable
                :columns="columns"
                :data="proposals?.data || []"
                class="p-6"
            >
                <!-- Coluna Data -->
                <template #cell-data="{ item }">
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ formatDate(item.data_proposta) }}
                    </span>
                </template>

                <!-- Coluna Número -->
                <template #cell-numero="{ item }">
                    <span
                        class="text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ item.numero }}
                    </span>
                </template>

                <!-- Coluna Validade -->
                <template #cell-validade="{ item }">
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ formatDate(item.validade) }}
                    </span>
                </template>

                <!-- Coluna Cliente -->
                <template #cell-cliente="{ item }">
                    <span class="text-sm text-gray-900 dark:text-white">
                        {{ item.entity?.name || "—" }}
                    </span>
                </template>

                <!-- Coluna Valor Total -->
                <template #cell-valor_total="{ item }">
                    <span
                        class="text-sm font-semibold text-gray-900 dark:text-white"
                    >
                        {{ formatCurrency(item.valor_total) }}
                    </span>
                </template>

                <!-- Coluna Estado -->
                <template #cell-estado="{ item }">
                    <span
                        :class="[
                            'px-2 py-1 text-xs font-semibold rounded-full',
                            item.estado === 'fechado'
                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                        ]"
                    >
                        {{ item.estado === "fechado" ? "Fechado" : "Rascunho" }}
                    </span>
                </template>

                <!-- Coluna Ações -->
                <template #cell-acoes="{ item }">
                    <div class="flex items-center gap-2 justify-end">
                        <!-- Download PDF -->
                        <a
                            :href="route('proposals.pdf', item.id)"
                            target="_blank"
                            class="p-1.5 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded transition-colors"
                            title="Download PDF"
                        >
                            <FileText class="h-4 w-4" />
                        </a>

                        <!-- Editar -->
                        <Link
                            v-if="
                                $page.props.auth.permissions.includes(
                                    'proposals.update'
                                )
                            "
                            :href="route('proposals.edit', item.id)"
                            class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                            title="Editar"
                        >
                            <Pencil class="h-4 w-4" />
                        </Link>

                        <!-- Eliminar -->
                        <button
                            v-if="
                                $page.props.auth.permissions.includes(
                                    'proposals.delete'
                                )
                            "
                            @click="openDeleteDialog(item)"
                            class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                            title="Eliminar"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </template>

                <!-- Empty State -->
                <template #empty>
                    <div class="text-center py-12">
                        <FileX class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                        <p
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            Nenhuma proposta encontrada
                        </p>
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                        >
                            Comece por criar uma nova proposta
                        </p>
                    </div>
                </template>
            </DataTable>

            <!-- Pagination -->
            <div
                v-if="proposals?.data && proposals.data.length > 0"
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Mostrando
                        <span class="font-medium">{{
                            proposals.from || 0
                        }}</span>
                        a
                        <span class="font-medium">{{ proposals.to || 0 }}</span>
                        de
                        <span class="font-medium">{{
                            proposals.total || 0
                        }}</span>
                        resultados
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <template
                            v-for="link in proposals.links"
                            :key="link.label"
                        >
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                :class="[
                                    'px-3 py-2 text-sm border rounded-lg transition-colors',
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600',
                                ]"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-2 text-sm border rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 border-gray-200 dark:border-gray-700 cursor-not-allowed"
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
            title="Eliminar Proposta"
            :message="
                proposalToDelete
                    ? `Tem a certeza que pretende eliminar a proposta ${proposalToDelete.numero}?`
                    : ''
            "
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/ui/DataTable.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import { FileText, Search, Plus, Pencil, Trash2, FileX } from "lucide-vue-next";

const props = defineProps({
    proposals: Object,
    filters: Object,
});

// Colunas da DataTable
const columns = computed(() => [
    {
        key: "data",
        title: "Data",
        sortable: false,
    },
    {
        key: "numero",
        title: "Número",
        sortable: false,
    },
    {
        key: "validade",
        title: "Validade",
        sortable: false,
    },
    {
        key: "cliente",
        title: "Cliente",
        sortable: false,
    },
    {
        key: "valor_total",
        title: "Valor Total",
        sortable: false,
    },
    {
        key: "estado",
        title: "Estado",
        sortable: false,
    },
    {
        key: "acoes",
        title: "Ações",
        sortable: false,
        class: "text-right",
    },
]);

const search = ref(props.filters?.search || "");
const estadoFilter = ref(props.filters?.estado || "");

const formatDate = (date) => {
    if (!date) return "—";
    const d = new Date(date);
    return d.toLocaleDateString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value || 0);
};

const showDeleteDialog = ref(false);
const proposalToDelete = ref(null);

const openDeleteDialog = (proposal) => {
    console.log("openDeleteDialog called with:", proposal);
    proposalToDelete.value = proposal;
    showDeleteDialog.value = true;
};

const confirmDelete = () => {
    console.log("confirmDelete called for proposal:", proposalToDelete.value);
    if (!proposalToDelete.value) {
        console.error("No proposal to delete");
        return;
    }
    router.delete(route("proposals.destroy", proposalToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log("Delete successful");
        },
        onError: (errors) => {
            console.error("Delete failed:", errors);
        },
        onFinish: () => {
            showDeleteDialog.value = false;
            proposalToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    proposalToDelete.value = null;
};

const filterProposals = () => {
    router.get(
        route("proposals.index"),
        {
            search: search.value,
            estado: estadoFilter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};
</script>
