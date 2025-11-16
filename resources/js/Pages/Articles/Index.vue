<template>
    <Head title="Artigos" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <Package class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Artigos
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir produtos e serviços
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
                <li class="text-gray-900 dark:text-white">Artigos</li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col gap-4">
                    <!-- Linha 1: Pesquisa e Botão Criar -->
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
                                    placeholder="Pesquisar artigos..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    @input="search"
                                />
                            </div>
                        </div>

                        <Link
                            v-if="can.create"
                            :href="route('articles.create')"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2"
                        >
                            <Plus class="h-5 w-5" />
                            Novo Artigo
                        </Link>
                    </div>

                    <!-- Linha 2: Filtros -->
                    <div class="flex flex-wrap gap-3">
                        <select
                            v-model="searchForm.tipo"
                            @change="search"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todos os Tipos</option>
                            <option value="produto">Produto</option>
                            <option value="servico">Serviço</option>
                        </select>

                        <select
                            v-model="searchForm.gama"
                            @change="search"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todas as Gamas</option>
                            <option
                                v-for="gama in gamas"
                                :key="gama"
                                :value="gama"
                            >
                                {{ gama }}
                            </option>
                        </select>

                        <select
                            v-model="searchForm.estado"
                            @change="search"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Todos os Estados</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>

                        <select
                            v-model="searchForm.sort"
                            @change="search"
                            class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        >
                            <option value="created_at-desc">
                                Mais Recente
                            </option>
                            <option value="created_at-asc">Mais Antigo</option>
                            <option value="nome-asc">Nome A-Z</option>
                            <option value="nome-desc">Nome Z-A</option>
                            <option value="preco-desc">Preço Maior</option>
                            <option value="preco-asc">Preço Menor</option>
                            <option value="stock_quantidade-desc">
                                Maior Stock
                            </option>
                            <option value="stock_quantidade-asc">
                                Menor Stock
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- DataTable -->
            <DataTable
                :columns="columns"
                :data="articles?.data || []"
                class="p-6"
            >
                <!-- Coluna Referência -->
                <template #cell-referencia="{ item }">
                    <span
                        class="font-mono text-sm font-medium text-gray-900 dark:text-white"
                    >
                        {{ item.referencia }}
                    </span>
                </template>

                <!-- Coluna Foto -->
                <template #cell-foto="{ item }">
                    <div
                        class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center"
                    >
                        <img
                            v-if="item.foto_url"
                            :src="item.foto_url"
                            :alt="item.nome"
                            class="w-full h-full object-cover"
                        />
                        <Package
                            v-else
                            class="h-6 w-6 text-gray-400 dark:text-gray-500"
                        />
                    </div>
                </template>

                <!-- Coluna Nome -->
                <template #cell-nome="{ item }">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ item.nome }}
                        </div>
                        <div class="flex gap-2 mt-1">
                            <span
                                v-if="item.tipo"
                                :class="[
                                    'px-2 py-0.5 text-xs rounded-full',
                                    item.tipo === 'produto'
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'
                                        : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                ]"
                            >
                                {{
                                    item.tipo === "produto"
                                        ? "Produto"
                                        : "Serviço"
                                }}
                            </span>
                            <span
                                :class="[
                                    'px-2 py-0.5 text-xs rounded-full',
                                    item.estado === 'ativo'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
                                ]"
                            >
                                {{
                                    item.estado === "ativo"
                                        ? "Ativo"
                                        : "Inativo"
                                }}
                            </span>
                            <span
                                v-if="item.gama"
                                class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200"
                            >
                                {{ item.gama }}
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Coluna Descrição -->
                <template #cell-descricao="{ item }">
                    <span
                        class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2"
                    >
                        {{ item.descricao || "—" }}
                    </span>
                </template>

                <!-- Coluna Preço -->
                <template #cell-preco="{ item }">
                    <div class="text-sm">
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ formatCurrency(item.preco) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            s/ IVA
                        </div>
                        <div
                            class="text-sm font-semibold text-green-600 dark:text-green-400 mt-1"
                        >
                            {{ formatCurrency(item.preco_com_iva) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            c/ IVA ({{ item.iva_percentagem }}%)
                        </div>
                        <div
                            v-if="item.tipo === 'produto'"
                            class="mt-2 text-xs"
                        >
                            <span class="text-gray-500 dark:text-gray-400"
                                >Stock:</span
                            >
                            <span
                                :class="[
                                    'font-medium ml-1',
                                    item.stock_quantidade > 10
                                        ? 'text-green-600 dark:text-green-400'
                                        : item.stock_quantidade > 0
                                        ? 'text-orange-600 dark:text-orange-400'
                                        : 'text-red-600 dark:text-red-400',
                                ]"
                            >
                                {{ item.stock_quantidade }}
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Coluna Ações -->
                <template #cell-acoes="{ item }">
                    <div class="flex items-center gap-2 justify-end">
                        <Link
                            v-if="can.edit"
                            :href="route('articles.edit', item.id)"
                            class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                            title="Editar"
                        >
                            <Pencil class="h-4 w-4" />
                        </Link>
                        <button
                            v-if="can.delete"
                            @click="deleteArticle(item.id)"
                            class="p-1.5 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                            title="Eliminar"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </template>
            </DataTable>

            <!-- Paginação -->
            <div
                v-if="articles && articles.links && articles.links.length > 3"
                class="p-6 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-400">
                        Mostrando
                        <span class="font-medium">{{
                            articles.from || 0
                        }}</span>
                        a
                        <span class="font-medium">{{ articles.to || 0 }}</span>
                        de
                        <span class="font-medium">{{
                            articles.total || 0
                        }}</span>
                        resultados
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, index) in articles.links"
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
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, computed } from "vue";
import { router, Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/ui/DataTable.vue";
import { Package, Search, Plus, Pencil, Trash2 } from "lucide-vue-next";

const props = defineProps({
    articles: Object,
    filters: Object,
    sort: Object,
    gamas: Array,
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

// Colunas da DataTable
const columns = computed(() => [
    {
        key: "referencia",
        title: "Referência",
        sortable: false,
    },
    {
        key: "foto",
        title: "Foto",
        sortable: false,
    },
    {
        key: "nome",
        title: "Nome",
        sortable: false,
    },
    {
        key: "descricao",
        title: "Descrição",
        sortable: false,
    },
    {
        key: "preco",
        title: "Preço",
        sortable: false,
    },
    {
        key: "acoes",
        title: "Ações",
        sortable: false,
        class: "text-right",
    },
]);

// Formulário de pesquisa
const searchForm = reactive({
    search: props.filters?.search || "",
    estado: props.filters?.estado || "",
    tipo: props.filters?.tipo || "",
    gama: props.filters?.gama || "",
    sort:
        props.sort?.sort && props.sort?.direction
            ? `${props.sort.sort}-${props.sort.direction}`
            : "created_at-desc",
});

// Funções
const search = () => {
    const [sortField, sortDirection] = searchForm.sort.split("-");

    router.get(
        route("articles.index"),
        {
            search: searchForm.search,
            estado: searchForm.estado,
            tipo: searchForm.tipo,
            gama: searchForm.gama,
            sort: sortField,
            direction: sortDirection,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

const deleteArticle = (id) => {
    if (confirm("Tem certeza que deseja eliminar este artigo?")) {
        router.delete(route("articles.destroy", id), {
            preserveState: true,
        });
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};
</script>
