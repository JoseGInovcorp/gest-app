<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <Package class="h-8 w-8 text-blue-600" />
                    <div>
                        <h2
                            class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Artigos
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Gestão de produtos e serviços
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        {{ articles.total }}
                        {{ articles.total === 1 ? "artigo" : "artigos" }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                            >
                                <Home class="w-4 h-4 mr-2" />
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    class="w-4 h-4 text-gray-400 mx-1"
                                />
                                <span
                                    class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Configurações
                                </span>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    class="w-4 h-4 text-gray-400 mx-1"
                                />
                                <span
                                    class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Artigos
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Main Card -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <!-- Toolbar -->
                    <div
                        class="p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <div
                            class="flex flex-col sm:flex-row justify-between gap-4"
                        >
                            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                                <!-- Pesquisa -->
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
                                            @keyup.enter="search"
                                        />
                                    </div>
                                </div>

                                <!-- Filtro Estado -->
                                <select
                                    v-model="searchForm.estado"
                                    @change="search"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="">Todos os Estados</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="flex gap-2">
                                <button
                                    @click="search"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg transition-colors"
                                >
                                    <Filter class="h-4 w-4" />
                                </button>
                                <Link
                                    v-if="can.create"
                                    :href="route('articles.create')"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center gap-2"
                                >
                                    <Plus class="h-4 w-4" />
                                    Novo Artigo
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="overflow-x-auto p-6">
                        <div
                            v-if="articles && articles.data"
                            class="text-gray-700 dark:text-gray-300"
                        >
                            <p class="mb-4">
                                {{ articles.data.length }} artigos encontrados
                            </p>
                            <div class="mt-4 space-y-2">
                                <div
                                    v-for="article in articles.data"
                                    :key="article.id"
                                    class="p-4 bg-gray-50 dark:bg-gray-700 rounded border flex items-center justify-between"
                                >
                                    <div class="flex-1">
                                        <h3 class="font-medium">
                                            {{ article.referencia }} -
                                            {{ article.nome }}
                                        </h3>
                                        <p
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            {{
                                                article.descricao ||
                                                "Sem descrição"
                                            }}
                                        </p>
                                        <p class="text-sm font-medium mt-1">
                                            {{ article.preco }}€ (IVA:
                                            {{ article.iva_percentagem }}%)
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        <Link
                                            v-if="can.edit"
                                            :href="
                                                route(
                                                    'articles.edit',
                                                    article.id
                                                )
                                            "
                                        >
                                            <button
                                                class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                                title="Editar"
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </button>
                                        </Link>
                                        <button
                                            v-if="can.delete"
                                            @click="deleteArticle(article.id)"
                                            class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors"
                                            title="Eliminar"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-6 text-center text-gray-500">
                            <p>
                                Nenhum artigo encontrado ou erro ao carregar
                                dados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, inject } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";
// import ArticlesDataTable from "@/Components/ArticlesDataTable.vue";

// Icons
import {
    Package,
    Home,
    ChevronRight,
    Search,
    Filter,
    Plus,
    Pencil,
    Trash2,
} from "lucide-vue-next";

// Inject permission checker with fallback
const hasPermission = inject("hasPermission", () => () => false);

// Props
const props = defineProps({
    articles: Object,
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
    search: props.filters.search || "",
    estado: props.filters.estado || "",
});

// Funções
const search = () => {
    router.get(
        route("articles.index"),
        {
            ...searchForm,
            sort: props.sort?.sort,
            direction: props.sort?.direction,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const handleSort = (field) => {
    const direction =
        props.sort?.sort === field && props.sort?.direction === "asc"
            ? "desc"
            : "asc";

    router.get(
        route("articles.index"),
        {
            ...searchForm,
            sort: field,
            direction: direction,
        },
        {
            preserveState: true,
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
</script>
