<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, ArrowLeft, Pencil } from "lucide-vue-next";

const props = defineProps({
    entity: Object,
});

// Helper para formatar data
const formatDate = (dateString) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("pt-PT");
};
</script>

<template>
    <Head :title="`${entity.name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10 dark:bg-blue-400/10"
                        >
                            <Users
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ entity.name }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                NIF: {{ entity.tax_number }}
                            </p>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <Link
                            :href="
                                route(
                                    entity.type === 'client'
                                        ? 'clients.index'
                                        : entity.type === 'supplier'
                                        ? 'suppliers.index'
                                        : 'entities.index'
                                )
                            "
                        >
                            <Button variant="outline">
                                <ArrowLeft class="h-4 w-4 mr-2" />
                                Voltar
                            </Button>
                        </Link>

                        <Link
                            :href="
                                route(
                                    entity.type === 'client'
                                        ? 'clients.edit'
                                        : entity.type === 'supplier'
                                        ? 'suppliers.edit'
                                        : 'entities.edit',
                                    entity.id
                                )
                            "
                        >
                            <Button>
                                <Pencil class="h-4 w-4 mr-2" />
                                Editar
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Detalhes -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4"
            >
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Informações Gerais
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Nome</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >NIF</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.tax_number }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Morada</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.address }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Cidade</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.city }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >País</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.country }}
                        </p>
                    </div>

                    <div v-if="entity.email">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Email</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.email }}
                        </p>
                    </div>

                    <div v-if="entity.phone">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Telefone</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.phone }}
                        </p>
                    </div>

                    <div v-if="entity.mobile">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Telemóvel</label
                        >
                        <p class="mt-1 text-gray-900 dark:text-white">
                            {{ entity.mobile }}
                        </p>
                    </div>
                </div>

                <div v-if="entity.observations" class="pt-4">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >Observações</label
                    >
                    <p class="mt-1 text-gray-900 dark:text-white">
                        {{ entity.observations }}
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
