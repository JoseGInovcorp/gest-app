<script setup>
import { router } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import EntitiesDataTable from "@/Components/EntitiesDataTableNew.vue";
import { Package } from "lucide-vue-next";

const props = defineProps({
    entities: Object,
    filters: Object,
    pageTitle: String,
    entityType: String,
    can: Object,
});

// Event handlers
const handleCreate = () => {
    router.visit(route("suppliers.create"));
};

const handleView = (entity) => {
    router.visit(route("suppliers.show", entity.id));
};

const handleEdit = (entity) => {
    router.visit(route("suppliers.edit", entity.id));
};

const handleDelete = (entity) => {
    if (
        confirm(
            `Tem certeza que deseja eliminar o fornecedor "${entity.name}"?`
        )
    ) {
        router.delete(route("suppliers.destroy", entity.id));
    }
};
</script>

<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <Package
                        class="h-6 w-6 text-purple-600 dark:text-purple-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ pageTitle }}
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir informações dos fornecedores
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
                <li class="text-gray-900 dark:text-white">Fornecedores</li>
            </ol>
        </nav>

        <!-- Data Table -->
        <EntitiesDataTable
            :entities="entities.data || []"
            :can-view="can.view"
            :can-edit="can.edit"
            :can-delete="can.delete"
            entity-type="supplier"
            @create="handleCreate"
            @view="handleView"
            @edit="handleEdit"
            @delete="handleDelete"
        />
    </AuthenticatedLayout>
</template>
