<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Badge from "@/Components/ui/Badge.vue";
import {
    Users,
    ArrowLeft,
    Pencil,
    CheckCircle2,
    XCircle,
} from "lucide-vue-next";

const props = defineProps({
    contact: Object,
});

// Helper para formatar data
const formatDate = (dateString) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("pt-PT", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Helper para obter variante do badge de status
const getStatusVariant = (status) => {
    return status === "active" ? "success" : "default";
};

const getStatusLabel = (status) => {
    return status === "active" ? "Ativo" : "Inativo";
};
</script>

<template>
    <Head :title="`${contact.first_name} ${contact.last_name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-600/10 dark:bg-green-400/10"
                        >
                            <Users
                                class="h-6 w-6 text-green-600 dark:text-green-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ contact.first_name }} {{ contact.last_name }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Contacto #{{ contact.number }}
                                <span v-if="contact.entity">
                                    - {{ contact.entity.name }}</span
                                >
                            </p>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <Link :href="route('contacts.index')">
                            <Button variant="outline">
                                <ArrowLeft class="h-4 w-4 mr-2" />
                                Voltar
                            </Button>
                        </Link>

                        <Link :href="route('contacts.edit', contact.id)">
                            <Button class="bg-green-600 hover:bg-green-700">
                                <Pencil class="h-4 w-4 mr-2" />
                                Editar
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Detalhes do Contacto -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6"
            >
                <div class="flex items-center justify-between">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Informações do Contacto
                    </h2>
                    <Badge :variant="getStatusVariant(contact.status)">
                        {{ getStatusLabel(contact.status) }}
                    </Badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Entidade -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Entidade
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ contact.entity?.name || "-" }}
                            <span
                                v-if="contact.entity?.type"
                                class="text-sm text-gray-500"
                            >
                                ({{ contact.entity.type }})
                            </span>
                        </p>
                    </div>

                    <!-- Número -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Número
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            #{{ contact.number }}
                        </p>
                    </div>

                    <!-- Nome -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Primeiro Nome
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ contact.first_name }}
                        </p>
                    </div>

                    <!-- Apelido -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Apelido
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ contact.last_name }}
                        </p>
                    </div>

                    <!-- Função -->
                    <div v-if="contact.function">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Função
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ contact.function }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div v-if="contact.email">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Email
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            <a
                                :href="`mailto:${contact.email}`"
                                class="text-green-600 hover:text-green-700 dark:text-green-400"
                            >
                                {{ contact.email }}
                            </a>
                        </p>
                    </div>

                    <!-- Telefone -->
                    <div v-if="contact.phone">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Telefone
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            <a
                                :href="`tel:${contact.phone}`"
                                class="text-green-600 hover:text-green-700 dark:text-green-400"
                            >
                                {{ contact.phone }}
                            </a>
                        </p>
                    </div>

                    <!-- Telemóvel -->
                    <div v-if="contact.mobile">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Telemóvel
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            <a
                                :href="`tel:${contact.mobile}`"
                                class="text-green-600 hover:text-green-700 dark:text-green-400"
                            >
                                {{ contact.mobile }}
                            </a>
                        </p>
                    </div>

                    <!-- Consentimento RGPD -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Consentimento RGPD
                        </label>
                        <div class="flex items-center space-x-2">
                            <CheckCircle2
                                v-if="contact.rgpd_consent"
                                class="h-5 w-5 text-green-600"
                            />
                            <XCircle v-else class="h-5 w-5 text-red-600" />
                            <span class="text-gray-900 dark:text-white">
                                {{ contact.rgpd_consent ? "Sim" : "Não" }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div
                    v-if="contact.observations"
                    class="pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Observações
                    </label>
                    <p
                        class="text-gray-900 dark:text-white whitespace-pre-wrap"
                    >
                        {{ contact.observations }}
                    </p>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2
                    class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                >
                    Metadata
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Criado por -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Criado por
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ contact.created_by?.name || "-" }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ formatDate(contact.created_at) }}
                        </p>
                    </div>

                    <!-- Atualizado por -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Última atualização por
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ contact.updated_by?.name || "-" }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ formatDate(contact.updated_at) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
