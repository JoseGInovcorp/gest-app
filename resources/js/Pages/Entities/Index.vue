<script setup>
import { router } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import EntitiesDataTable from "@/Components/EntitiesDataTableNew.vue";
import { Users } from "lucide-vue-next";

const props = defineProps({
    entities: Object,
    filters: Object,
    pageTitle: String,
    entityType: String,
    can: Object,
});

// Event handlers
const handleCreate = () => {
    router.visit(route("entities.create"));
};

const handleView = (entity) => {
    router.visit(route("entities.show", entity.id));
};

const handleEdit = (entity) => {
    router.visit(route("entities.edit", entity.id));
};

const handleDelete = (entity) => {
    if (
        confirm(`Tem certeza que deseja eliminar a entidade "${entity.name}"?`)
    ) {
        router.delete(route("entities.destroy", entity.id));
    }
};
</script>

<template>
    <Head :title="pageTitle || 'Entidades'" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <Users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ pageTitle || "Entidades" }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Gestão de todas as entidades do sistema
                    </p>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <EntitiesDataTable
            :entities="entities"
            :filters="filters"
            :entity-type="entityType || 'all'"
            @create="handleCreate"
            @view="handleView"
            @edit="handleEdit"
            @delete="handleDelete"
        />
    </AuthenticatedLayout>
</template>
