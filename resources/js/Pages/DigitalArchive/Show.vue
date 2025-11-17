<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    FolderOpen,
    Download,
    Trash2,
    Edit,
    FileText,
    Calendar,
    User,
    Tag,
    Archive,
    AlertCircle,
    ChevronLeft,
} from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";

const props = defineProps({
    document: Object,
    can: {
        type: Object,
        default: () => ({
            edit: false,
            delete: false,
        }),
    },
});

// Download
const downloadDocument = () => {
    window.location.href = `/digital-archive/${props.document.id}/download`;
};

// Eliminar
const showDeleteDialog = ref(false);

const openDeleteDialog = () => {
    showDeleteDialog.value = true;
};

const deleteDocument = () => {
    router.delete(route("digital-archive.destroy", props.document.id), {
        onSuccess: () => {
            router.visit(route("digital-archive.index"));
        },
        onFinish: () => {
            showDeleteDialog.value = false;
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
};

// Formatar data
const formatDate = (date) => {
    return new Date(date).toLocaleString("pt-PT");
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

// Preview do documento (se for PDF ou imagem)
const canPreview = () => {
    return (
        props.document.mime_type.includes("pdf") ||
        props.document.mime_type.startsWith("image/")
    );
};
</script>

<template>
    <Head :title="`Documento: ${document.name}`" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <Button
                        @click="router.visit(route('digital-archive.index'))"
                        variant="ghost"
                        size="sm"
                    >
                        <ChevronLeft class="h-4 w-4" />
                    </Button>
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
                            {{ document.name }}
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ document.original_filename }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button @click="downloadDocument" variant="outline">
                        <Download class="h-4 w-4 mr-2" />
                        Download
                    </Button>
                    <Button
                        v-if="can.delete"
                        @click="openDeleteDialog"
                        variant="destructive"
                    >
                        <Trash2 class="h-4 w-4 mr-2" />
                        Eliminar
                    </Button>
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
                <li>
                    <Link
                        :href="route('digital-archive.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Arquivo Digital
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">
                    {{ document.name }}
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Preview / Info Principal -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
                >
                    <!-- Preview -->
                    <div
                        v-if="canPreview()"
                        class="p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Pré-visualização
                        </h2>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                            <!-- PDF -->
                            <iframe
                                v-if="document.mime_type.includes('pdf')"
                                :src="document.file_url"
                                class="w-full h-96 border-0 rounded"
                            />
                            <!-- Imagem -->
                            <img
                                v-else-if="
                                    document.mime_type.startsWith('image/')
                                "
                                :src="document.file_url"
                                :alt="document.name"
                                class="max-w-full h-auto rounded"
                            />
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div class="p-6">
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Descrição
                        </h2>
                        <p
                            v-if="document.description"
                            class="text-gray-700 dark:text-gray-300 whitespace-pre-line"
                        >
                            {{ document.description }}
                        </p>
                        <p
                            v-else
                            class="text-gray-500 dark:text-gray-400 italic"
                        >
                            Sem descrição
                        </p>
                    </div>

                    <!-- Versões -->
                    <div
                        v-if="document.versions && document.versions.length > 0"
                        class="p-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Histórico de Versões
                        </h2>
                        <div class="space-y-3">
                            <div
                                v-for="version in document.versions"
                                :key="version.id"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                            >
                                <div>
                                    <p
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        Versão {{ version.version }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(version.created_at) }} •
                                        {{ version.uploader.name }}
                                    </p>
                                </div>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="
                                        router.visit(
                                            route(
                                                'digital-archive.show',
                                                version.id
                                            )
                                        )
                                    "
                                >
                                    Ver
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Metadados -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Informações -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Informações
                        </h3>
                        <dl class="space-y-3">
                            <!-- Categoria -->
                            <div>
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Categoria
                                </dt>
                                <dd>
                                    <span
                                        :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getCategoryColor(
                                            document.category
                                        )}-100 text-${getCategoryColor(
                                            document.category
                                        )}-800 dark:bg-${getCategoryColor(
                                            document.category
                                        )}-900/20 dark:text-${getCategoryColor(
                                            document.category
                                        )}-300`"
                                    >
                                        <Tag class="h-3 w-3 mr-1" />
                                        {{ document.category }}
                                    </span>
                                </dd>
                            </div>

                            <!-- Módulo -->
                            <div v-if="document.module">
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Módulo
                                </dt>
                                <dd
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ document.module }}
                                </dd>
                            </div>

                            <!-- Tamanho -->
                            <div>
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Tamanho
                                </dt>
                                <dd
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ document.formatted_size }}
                                </dd>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Tipo de Ficheiro
                                </dt>
                                <dd
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ document.mime_type }}
                                </dd>
                            </div>

                            <!-- Versão -->
                            <div>
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Versão
                                </dt>
                                <dd
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ document.version }}
                                </dd>
                            </div>

                            <!-- Status -->
                            <div>
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Estado
                                </dt>
                                <dd>
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            document.status === 'active'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
                                                : document.status === 'archived'
                                                ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                                : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
                                        ]"
                                    >
                                        {{ document.status }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Datas -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center"
                        >
                            <Calendar class="h-5 w-5 mr-2" />
                            Datas
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Carregado em
                                </dt>
                                <dd
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ formatDate(document.created_at) }}
                                </dd>
                            </div>

                            <div v-if="document.expires_at">
                                <dt
                                    class="text-sm text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Expira em
                                </dt>
                                <dd>
                                    <span
                                        :class="[
                                            'text-sm font-medium',
                                            document.is_expired
                                                ? 'text-red-600 dark:text-red-400'
                                                : 'text-gray-900 dark:text-white',
                                        ]"
                                    >
                                        {{ formatDate(document.expires_at) }}
                                    </span>
                                    <span
                                        v-if="document.is_expired"
                                        class="ml-2 inline-flex items-center text-xs text-red-600 dark:text-red-400"
                                    >
                                        <AlertCircle class="h-3 w-3 mr-1" />
                                        Expirado
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Utilizador -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center"
                        >
                            <User class="h-5 w-5 mr-2" />
                            Carregado por
                        </h3>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div
                                    class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900/20 flex items-center justify-center"
                                >
                                    <span
                                        class="text-purple-600 dark:text-purple-400 font-medium"
                                    >
                                        {{
                                            document.uploader.name
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <p
                                    class="text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ document.uploader.name }}
                                </p>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{ document.uploader.email }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div
                        v-if="document.tags && document.tags.length > 0"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                    >
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Tags
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in document.tags"
                                :key="tag"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                            >
                                <Tag class="h-3 w-3 mr-1" />
                                {{ tag }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            type="danger"
            title="Eliminar Documento"
            :message="`Tem a certeza que deseja eliminar &quot;${document.name}&quot;?`"
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            @confirm="deleteDocument"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>
