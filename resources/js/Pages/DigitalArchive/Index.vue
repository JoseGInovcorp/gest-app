<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UploadModal from "@/Components/UploadModal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import {
    FolderOpen,
    Plus,
    Eye,
    Download,
    Trash2,
    FileText,
    Image,
    FileSpreadsheet,
    FileVideo,
    File,
    Calendar,
    Search,
    Filter,
    X,
} from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";

const props = defineProps({
    documents: Object,
    filters: Object,
    categories: Object,
    modules: Object,
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

// Estados
const showUploadModal = ref(false);
const filterForm = ref({
    search: props.filters.search || "",
    category: props.filters.category || "",
    module: props.filters.module || "",
    date_from: props.filters.date_from || "",
    date_to: props.filters.date_to || "",
    status: props.filters.status || "",
});

// Aplicar filtros
const applyFilters = () => {
    router.get("/digital-archive", filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Limpar filtros
const clearFilters = () => {
    filterForm.value = {
        search: "",
        category: "",
        module: "",
        date_from: "",
        date_to: "",
        status: "",
    };
    applyFilters();
};

// Ícone por tipo de ficheiro
const getFileIcon = (mimeType) => {
    if (mimeType.startsWith("image/")) return Image;
    if (mimeType.includes("pdf")) return FileText;
    if (mimeType.includes("spreadsheet") || mimeType.includes("excel"))
        return FileSpreadsheet;
    if (mimeType.startsWith("video/")) return FileVideo;
    return File;
};

// Cor por categoria
const getCategoryColor = (category) => {
    const colors = {
        contrato: "blue",
        fatura: "red",
        proposta: "green",
        identificacao: "purple",
        certificado: "yellow",
        relatorio: "indigo",
        comprovativo: "pink",
        correspondencia: "cyan",
        outros: "gray",
    };
    return colors[category] || "gray";
};

// Download
const downloadDocument = (documentId) => {
    window.location.href = `/digital-archive/${documentId}/download`;
};

// Eliminar
const deleteDocument = (document) => {
    if (confirm(`Tem a certeza que deseja eliminar "${document.name}"?`)) {
        router.delete(route("digital-archive.destroy", document.id), {
            preserveScroll: true,
        });
    }
};

// Formatar data
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("pt-PT");
};
</script>

<template>
    <Head title="Arquivo Digital" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div
                        class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg"
                    >
                        <FolderOpen
                            class="h-6 w-6 text-purple-600 dark:text-purple-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Arquivo Digital
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            Repositório de documentos da aplicação
                        </p>
                    </div>
                </div>
                <Button
                    v-if="can.create"
                    @click="showUploadModal = true"
                    class="bg-purple-600 hover:bg-purple-700"
                >
                    <Plus class="h-4 w-4 mr-2" />
                    Carregar Documento
                </Button>
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
                <li class="text-gray-900 dark:text-white">Arquivo Digital</li>
            </ol>
        </nav>

        <!-- Filtros -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6 border border-gray-200 dark:border-gray-700"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Pesquisa -->
                <div class="lg:col-span-2">
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400"
                        />
                        <Input
                            v-model="filterForm.search"
                            placeholder="Pesquisar documentos..."
                            class="pl-10"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                </div>

                <!-- Categoria -->
                <div>
                    <Select v-model="filterForm.category">
                        <option value="">Todas</option>
                        <option
                            v-for="(label, key) in categories"
                            :key="key"
                            :value="key"
                        >
                            {{ label }}
                        </option>
                    </Select>
                </div>

                <!-- Módulo -->
                <div>
                    <Select v-model="filterForm.module">
                        <option value="">Todos</option>
                        <option
                            v-for="(label, key) in modules"
                            :key="key"
                            :value="key"
                        >
                            {{ label }}
                        </option>
                    </Select>
                </div>

                <!-- Data Início -->
                <div>
                    <Input
                        v-model="filterForm.date_from"
                        type="date"
                        placeholder="Data início"
                    />
                </div>

                <!-- Data Fim -->
                <div>
                    <Input
                        v-model="filterForm.date_to"
                        type="date"
                        placeholder="Data fim"
                    />
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="flex gap-2 mt-4">
                <Button @click="applyFilters" variant="default" size="sm">
                    <Filter class="h-4 w-4 mr-2" />
                    Aplicar Filtros
                </Button>
                <Button @click="clearFilters" variant="outline" size="sm">
                    <X class="h-4 w-4 mr-2" />
                    Limpar
                </Button>
            </div>
        </div>

        <!-- Grid de Documentos -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="p-6">
                <!-- Lista de Documentos -->
                <div
                    v-if="documents.data.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                >
                    <div
                        v-for="document in documents.data"
                        :key="document.id"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow"
                    >
                        <!-- Ícone e Nome -->
                        <div class="flex items-start space-x-3 mb-3">
                            <div
                                :class="`p-2 rounded-lg bg-${getCategoryColor(
                                    document.category
                                )}-100 dark:bg-${getCategoryColor(
                                    document.category
                                )}-900/20`"
                            >
                                <component
                                    :is="getFileIcon(document.mime_type)"
                                    :class="`h-6 w-6 text-${getCategoryColor(
                                        document.category
                                    )}-600 dark:text-${getCategoryColor(
                                        document.category
                                    )}-400`"
                                />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3
                                    class="font-medium text-gray-900 dark:text-white truncate"
                                    :title="document.name"
                                >
                                    {{ document.name }}
                                </h3>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ document.original_filename }}
                                </p>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="space-y-1 mb-3">
                            <div
                                class="flex items-center justify-between text-xs"
                            >
                                <span class="text-gray-500">Categoria:</span>
                                <span
                                    :class="`px-2 py-0.5 rounded-full bg-${getCategoryColor(
                                        document.category
                                    )}-100 text-${getCategoryColor(
                                        document.category
                                    )}-700 dark:bg-${getCategoryColor(
                                        document.category
                                    )}-900/20 dark:text-${getCategoryColor(
                                        document.category
                                    )}-300`"
                                >
                                    {{ categories[document.category] }}
                                </span>
                            </div>
                            <div
                                v-if="document.module"
                                class="flex items-center justify-between text-xs"
                            >
                                <span class="text-gray-500">Módulo:</span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ modules[document.module] }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between text-xs"
                            >
                                <span class="text-gray-500">Tamanho:</span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ document.formatted_size }}
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between text-xs"
                            >
                                <span class="text-gray-500">Data:</span>
                                <span class="text-gray-900 dark:text-white">
                                    {{ formatDate(document.created_at) }}
                                </span>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex gap-2">
                            <Button
                                @click="
                                    router.visit(
                                        route(
                                            'digital-archive.show',
                                            document.id
                                        )
                                    )
                                "
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                <Eye class="h-3 w-3 mr-1" />
                                Ver
                            </Button>
                            <Button
                                @click="downloadDocument(document.id)"
                                variant="outline"
                                size="sm"
                                class="flex-1"
                            >
                                <Download class="h-3 w-3 mr-1" />
                                Download
                            </Button>
                            <Button
                                v-if="can.delete"
                                @click="deleteDocument(document)"
                                variant="destructive"
                                size="sm"
                            >
                                <Trash2 class="h-3 w-3" />
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Estado vazio -->
                <div v-else class="text-center py-12">
                    <FolderOpen class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-2"
                    >
                        Nenhum documento encontrado
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        Carregue o primeiro documento para começar.
                    </p>
                </div>
            </div>

            <!-- Paginação -->
            <div
                v-if="documents.data.length > 0"
                class="border-t border-gray-200 dark:border-gray-700 px-6 py-4"
            >
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        A mostrar
                        <span class="font-medium">{{
                            documents.from || 0
                        }}</span>
                        a
                        <span class="font-medium">{{ documents.to || 0 }}</span>
                        de
                        <span class="font-medium">{{
                            documents.total || 0
                        }}</span>
                        resultados
                    </div>
                    <div>
                        <nav
                            class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                            aria-label="Pagination"
                        >
                            <template
                                v-for="(link, index) in documents.links"
                                :key="index"
                            >
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        link.active
                                            ? 'z-10 bg-purple-50 border-purple-500 text-purple-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400',
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600',
                                    ]"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Upload -->
        <UploadModal
            v-if="showUploadModal"
            :categories="categories"
            :modules="modules"
            @close="showUploadModal = false"
        />
    </AuthenticatedLayout>
</template>
