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
                                Validade
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"
                            >
                                Cliente
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
                        <tr v-if="proposals.data.length === 0">
                            <td
                                colspan="7"
                                class="px-6 py-12 text-center text-gray-500 dark:text-gray-400"
                            >
                                <FileX
                                    class="mx-auto h-12 w-12 mb-4 opacity-50"
                                />
                                <p class="text-lg font-medium">
                                    Nenhuma proposta encontrada
                                </p>
                                <p class="text-sm mt-1">
                                    Comece por criar uma nova proposta
                                </p>
                            </td>
                        </tr>
                        <tr
                            v-for="proposal in proposals.data"
                            :key="proposal.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDate(proposal.data_proposta) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                            >
                                {{ proposal.numero }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatDate(proposal.validade) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ proposal.entity?.name || "-" }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"
                            >
                                {{ formatCurrency(proposal.valor_total) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs font-semibold rounded-full',
                                        proposal.estado === 'fechado'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    ]"
                                >
                                    {{
                                        proposal.estado === "fechado"
                                            ? "Fechado"
                                            : "Rascunho"
                                    }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <div class="flex justify-end gap-2">
                                    <a
                                        v-if="
                                            $page.props.auth.permissions.includes(
                                                'proposals.read'
                                            )
                                        "
                                        :href="
                                            route('proposals.pdf', proposal.id)
                                        "
                                        target="_blank"
                                        class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                        title="Download PDF"
                                    >
                                        <FileText class="h-4 w-4" />
                                    </a>
                                    <Link
                                        v-if="
                                            $page.props.auth.permissions.includes(
                                                'proposals.update'
                                            )
                                        "
                                        :href="
                                            route('proposals.edit', proposal.id)
                                        "
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button
                                        v-if="
                                            $page.props.auth.permissions.includes(
                                                'proposals.delete'
                                            )
                                        "
                                        @click="confirmDelete(proposal)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="proposals.data.length > 0"
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Mostrando {{ proposals.from }} a {{ proposals.to }} de
                        {{ proposals.total }} registos
                    </div>
                    <div class="flex gap-2">
                        <template
                            v-for="link in proposals.links"
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { FileText, Search, Plus, Pencil, Trash2, FileX } from "lucide-vue-next";

const props = defineProps({
    proposals: Object,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const estadoFilter = ref(props.filters?.estado || "");

const formatDate = (date) => {
    if (!date) return "-";
    const d = new Date(date);
    return d.toLocaleDateString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value || 0);
};

const confirmDelete = (proposal) => {
    if (
        confirm(
            `Tem a certeza que pretende eliminar a proposta ${proposal.numero}?`
        )
    ) {
        router.delete(route("proposals.destroy", proposal.id), {
            preserveScroll: true,
        });
    }
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
