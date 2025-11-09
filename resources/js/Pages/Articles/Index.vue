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
                <div class="flex flex-col sm:flex-row justify-between gap-4">
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
                                    {{ article.descricao || "Sem descrição" }}
                                </p>
                                <div class="mt-1 space-y-0.5">
                                    <p class="text-sm">
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Preço s/ IVA:</span
                                        >
                                        <span class="font-medium ml-1"
                                            >{{ article.preco }}€</span
                                        >
                                    </p>
                                    <p class="text-sm">
                                        <span
                                            class="text-gray-600 dark:text-gray-400"
                                            >Preço c/ IVA ({{
                                                article.iva_percentagem
                                            }}%):</span
                                        >
                                        <span
                                            class="font-semibold text-green-600 dark:text-green-400 ml-1"
                                            >{{ article.preco_com_iva }}€</span
                                        >
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <Link
                                    v-if="can.edit"
                                    :href="route('articles.edit', article.id)"
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
                    <p>Nenhum artigo encontrado ou erro ao carregar dados.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, inject } from "vue";
import { router, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

// Icons
import { Package, Search, Filter, Plus, Pencil, Trash2 } from "lucide-vue-next";

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
