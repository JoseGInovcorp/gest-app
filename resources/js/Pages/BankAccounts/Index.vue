<template>
    <Head title="Contas Bancárias" />

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
                        Contas Bancárias
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gestão de contas bancárias da empresa
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
                <li class="text-gray-900 dark:text-white">Contas Bancárias</li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-4">
                    <!-- Linha 1: Pesquisa e Botões -->
                    <div
                        class="flex flex-col sm:flex-row justify-between gap-4"
                    >
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <Search
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4"
                                />
                                <input
                                    v-model="searchForm.search"
                                    type="text"
                                    placeholder="Pesquisar contas..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    @keyup.enter="search"
                                />
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex gap-2">
                            <Link
                                v-if="can.create"
                                :href="route('bank-accounts.create')"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <Plus class="h-4 w-4" />
                                Nova Conta
                            </Link>
                        </div>
                    </div>

                    <!-- Linha 2: Filtros -->
                    <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                        <!-- Filtro Tipo -->
                        <select
                            v-model="searchForm.tipo"
                            @change="search"
                            class="pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todos os Tipos</option>
                            <option value="corrente">Corrente</option>
                            <option value="poupanca">Poupança</option>
                            <option value="credito">Crédito</option>
                            <option value="investimento">Investimento</option>
                        </select>

                        <!-- Filtro Estado -->
                        <select
                            v-model="searchForm.estado"
                            @change="search"
                            class="pl-3 pr-8 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todos os Estados</option>
                            <option value="ativa">Ativa</option>
                            <option value="inativa">Inativa</option>
                            <option value="encerrada">Encerrada</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Listagem -->
            <div class="overflow-x-auto p-6">
                <div
                    v-if="accounts && accounts.data && accounts.data.length > 0"
                    class="text-gray-700 dark:text-gray-300"
                >
                    <p class="mb-4">
                        {{ accounts.data.length }} contas encontradas
                    </p>
                    <div class="mt-4 space-y-2">
                        <div
                            v-for="account in accounts.data"
                            :key="account.id"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded border flex items-center justify-between"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-medium">
                                        {{ account.nome }}
                                    </h3>
                                    <span
                                        :class="[
                                            'px-2 py-0.5 text-xs rounded-full',
                                            account.estado === 'ativa'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                                : account.estado === 'inativa'
                                                ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        ]"
                                    >
                                        {{
                                            account.estado
                                                .charAt(0)
                                                .toUpperCase() +
                                            account.estado.slice(1)
                                        }}
                                    </span>
                                </div>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 mb-2"
                                >
                                    {{ account.banco }} - {{ account.iban }}
                                </p>
                                <div
                                    class="grid grid-cols-2 sm:grid-cols-4 gap-2"
                                >
                                    <div class="text-sm">
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Tipo:</span
                                        >
                                        <span class="font-medium ml-1">{{
                                            account.tipo
                                        }}</span>
                                    </div>
                                    <div class="text-sm">
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Saldo Atual:</span
                                        >
                                        <span
                                            :class="[
                                                'font-semibold ml-1',
                                                account.saldo_atual >= 0
                                                    ? 'text-green-600 dark:text-green-400'
                                                    : 'text-red-600 dark:text-red-400',
                                            ]"
                                            >{{
                                                Number(
                                                    account.saldo_atual
                                                ).toFixed(2)
                                            }}
                                            {{ account.moeda }}</span
                                        >
                                    </div>
                                    <div class="text-sm">
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Movimentos:</span
                                        >
                                        <span class="font-medium ml-1">{{
                                            account.transactions_count || 0
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1 ml-4 flex-shrink-0"
                            >
                                <Link
                                    v-if="can.view"
                                    :href="
                                        route('bank-accounts.show', account.id)
                                    "
                                >
                                    <button
                                        class="p-1.5 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded transition-colors"
                                        title="Ver Detalhes"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </button>
                                </Link>
                                <Link
                                    v-if="can.edit"
                                    :href="
                                        route('bank-accounts.edit', account.id)
                                    "
                                >
                                    <button
                                        class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                        title="Editar"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </button>
                                </Link>
                                <button
                                    v-if="can.delete"
                                    @click="deleteAccount(account.id)"
                                    class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                    title="Eliminar"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="p-6 text-center text-gray-500">
                    <p>Nenhuma conta bancária encontrada.</p>
                </div>
            </div>

            <!-- Paginação -->
            <div
                v-if="accounts && accounts.links && accounts.links.length > 3"
                class="p-6 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex flex-wrap gap-1 justify-center">
                    <component
                        :is="link.url ? Link : 'span'"
                        v-for="(link, index) in accounts.links"
                        :key="index"
                        :href="link.url || ''"
                        :class="[
                            'px-3 py-2 text-sm border rounded-lg transition-colors',
                            link.active
                                ? 'bg-blue-600 text-white border-blue-600'
                                : link.url
                                ? 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 border-gray-200 dark:border-gray-700 cursor-not-allowed',
                        ]"
                        v-html="link.label"
                        preserve-scroll
                    ></component>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { CreditCard, Search, Plus, Pencil, Trash2, Eye } from "lucide-vue-next";

// Props
const props = defineProps({
    accounts: Object,
    filters: Object,
    sort: Object,
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

// Formulário de pesquisa
const searchForm = reactive({
    search: props.filters?.search || "",
    estado: props.filters?.estado || "",
    tipo: props.filters?.tipo || "",
});

// Funções
const search = () => {
    router.get(
        route("bank-accounts.index"),
        {
            search: searchForm.search,
            estado: searchForm.estado,
            tipo: searchForm.tipo,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const deleteAccount = (id) => {
    if (
        confirm(
            "Tem certeza que deseja eliminar esta conta bancária? Esta ação não pode ser desfeita."
        )
    ) {
        router.delete(route("bank-accounts.destroy", id), {
            preserveState: true,
        });
    }
};
</script>
