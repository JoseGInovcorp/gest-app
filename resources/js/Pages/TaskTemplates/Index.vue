<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { Plus, Pencil, Trash2, Search } from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";

const props = defineProps({
    templates: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const isActive = ref(props.filters.is_active || "");

const applyFilters = () => {
    router.get(
        route("task-templates.index"),
        {
            search: search.value,
            is_active: isActive.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const deleteTemplate = (template) => {
    if (
        confirm(
            `Tem certeza que deseja eliminar o template "${template.label}"?`
        )
    ) {
        router.delete(route("task-templates.destroy", template.id));
    }
};
</script>

<template>
    <Head title="Templates de Tarefas" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-gray-100"
                    >
                        Templates de Tarefas
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Gerir templates pré-definidos para tarefas de ordens de
                        trabalho
                    </p>
                </div>
                <Link :href="route('task-templates.create')">
                    <Button>
                        <Plus class="h-4 w-4 mr-2" />
                        Novo Template
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Pesquisar
                    </label>
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400"
                        />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            placeholder="Código, título ou descrição..."
                        />
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Estado
                    </label>
                    <select
                        v-model="isActive"
                        @change="applyFilters"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    >
                        <option value="">Todos</option>
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <Button
                        @click="applyFilters"
                        variant="outline"
                        class="w-full"
                    >
                        Aplicar Filtros
                    </Button>
                </div>
            </div>
        </div>

        <!-- Templates Table -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
        >
            <table
                class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Ordem
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Código
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Título
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Grupo Padrão
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Estado
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                        >
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <tr
                        v-for="template in templates.data"
                        :key="template.id"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100"
                        >
                            {{ template.default_sequence }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-400"
                        >
                            {{ template.code }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div
                                class="font-medium text-gray-900 dark:text-gray-100"
                            >
                                {{ template.label }}
                            </div>
                            <div
                                v-if="template.description"
                                class="text-gray-500 dark:text-gray-400 text-xs mt-1"
                            >
                                {{ template.description }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400"
                        >
                            {{ template.assigned_group || "-" }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="
                                    template.is_active
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                "
                                class="px-2 py-1 text-xs font-medium rounded-full"
                            >
                                {{ template.is_active ? "Ativo" : "Inativo" }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                        >
                            <Link
                                :href="
                                    route('task-templates.edit', template.id)
                                "
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3"
                            >
                                <Pencil class="h-4 w-4 inline" />
                            </Link>
                            <button
                                @click="deleteTemplate(template)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400"
                            >
                                <Trash2 class="h-4 w-4 inline" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                v-if="templates.data.length === 0"
                class="p-12 text-center text-gray-500 dark:text-gray-400"
            >
                Nenhum template encontrado.
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="templates.links.length > 3" class="mt-6 flex justify-center">
            <nav class="flex gap-2">
                <component
                    v-for="link in templates.links"
                    :key="link.label"
                    :is="link.url ? Link : 'span'"
                    :href="link.url || undefined"
                    :class="[
                        link.active
                            ? 'bg-blue-600 text-white'
                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300',
                        'px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700',
                        !link.url && 'opacity-50 cursor-not-allowed',
                    ]"
                    v-html="link.label"
                >
                </component>
            </nav>
        </div>
    </AuthenticatedLayout>
</template>
