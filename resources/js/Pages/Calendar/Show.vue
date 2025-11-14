<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Badge from "@/Components/ui/Badge.vue";
import {
    Calendar,
    ArrowLeft,
    Pencil,
    Trash2,
    Clock,
    User,
    Building2,
    Tag,
    Zap,
} from "lucide-vue-next";

const props = defineProps({
    event: Object,
});

const deleteEvent = () => {
    if (
        confirm(
            "Tem certeza que deseja eliminar este evento? Esta ação não pode ser revertida."
        )
    ) {
        router.delete(route("calendar-events.destroy", props.event.id), {
            onSuccess: () => {
                router.visit(route("calendar.index"));
            },
        });
    }
};
</script>

<template>
    <Head title="Detalhes do Evento" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <Calendar
                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Detalhes do Evento
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            Visualizar informações do evento
                        </p>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Link
                        v-if="
                            $page.props.auth.permissions.includes(
                                'calendar-events.update'
                            )
                        "
                        :href="route('calendar-events.edit', event.id)"
                    >
                        <Button
                            class="bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            <Pencil class="h-4 w-4 mr-2" />
                            Editar
                        </Button>
                    </Link>
                    <Button
                        v-if="
                            $page.props.auth.permissions.includes(
                                'calendar-events.delete'
                            )
                        "
                        @click="deleteEvent"
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
                        :href="route('calendar.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Calendário
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Detalhes</li>
            </ol>
        </nav>

        <div class="mb-4">
            <Link :href="route('calendar.index')">
                <Button variant="ghost" size="sm">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Voltar ao Calendário
                </Button>
            </Link>
        </div>

        <!-- Event Details -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Estado -->
                    <div>
                        <Badge :class="event.estado_badge_class">
                            {{ event.estado_label }}
                        </Badge>
                    </div>

                    <!-- Data e Hora -->
                    <div class="flex items-start gap-3">
                        <Clock
                            class="h-5 w-5 text-gray-400 dark:text-gray-500 mt-0.5"
                        />
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Data e Hora
                            </h3>
                            <p class="text-gray-900 dark:text-white">
                                {{
                                    new Date(event.data).toLocaleDateString(
                                        "pt-PT"
                                    )
                                }}
                                às {{ event.hora }}
                            </p>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                            >
                                Duração: {{ event.duracao }} minutos
                            </p>
                        </div>
                    </div>

                    <!-- Utilizador -->
                    <div class="flex items-start gap-3">
                        <User
                            class="h-5 w-5 text-gray-400 dark:text-gray-500 mt-0.5"
                        />
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Utilizador Responsável
                            </h3>
                            <p class="text-gray-900 dark:text-white">
                                {{ event.user?.name || "N/D" }}
                            </p>
                        </div>
                    </div>

                    <!-- Entidade -->
                    <div v-if="event.entity" class="flex items-start gap-3">
                        <Building2
                            class="h-5 w-5 text-gray-400 dark:text-gray-500 mt-0.5"
                        />
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Entidade
                            </h3>
                            <p class="text-gray-900 dark:text-white">
                                {{
                                    event.entity.commercial_name ||
                                    event.entity.name
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Tipo -->
                    <div class="flex items-start gap-3">
                        <Tag
                            class="h-5 w-5 text-gray-400 dark:text-gray-500 mt-0.5"
                        />
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Tipo
                            </h3>
                            <div class="flex items-center gap-2">
                                <div
                                    v-if="event.event_type?.color"
                                    class="w-4 h-4 rounded"
                                    :style="{
                                        backgroundColor: event.event_type.color,
                                    }"
                                ></div>
                                <p class="text-gray-900 dark:text-white">
                                    {{ event.event_type?.name || "N/D" }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ação -->
                    <div
                        v-if="event.event_action"
                        class="flex items-start gap-3"
                    >
                        <Zap
                            class="h-5 w-5 text-gray-400 dark:text-gray-500 mt-0.5"
                        />
                        <div>
                            <h3
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >
                                Ação
                            </h3>
                            <p class="text-gray-900 dark:text-white">
                                {{ event.event_action.name }}
                            </p>
                        </div>
                    </div>

                    <!-- Partilha -->
                    <div>
                        <h3
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Partilhado
                        </h3>
                        <Badge
                            :class="
                                event.partilha
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
                            "
                        >
                            {{ event.partilha ? "Sim" : "Não" }}
                        </Badge>

                        <!-- Mostrar utilizadores partilhados se houver -->
                        <div
                            v-if="
                                event.partilha &&
                                event.shared_with &&
                                event.shared_with.length > 0
                            "
                            class="mt-2"
                        >
                            <p
                                class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                            >
                                Partilhado com:
                            </p>
                            <div class="flex flex-wrap gap-1">
                                <Badge
                                    v-for="user in event.shared_with"
                                    :key="user.id"
                                    class="bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                                >
                                    {{ user.name }}
                                </Badge>
                            </div>
                        </div>
                        <p
                            v-else-if="event.partilha"
                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                        >
                            Partilhado com todos
                        </p>
                    </div>

                    <!-- Conhecimento -->
                    <div v-if="event.conhecimento">
                        <h3
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Conhecimento
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p
                                class="text-gray-900 dark:text-white whitespace-pre-wrap"
                            >
                                {{ event.conhecimento }}
                            </p>
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div v-if="event.descricao">
                        <h3
                            class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Descrição
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p
                                class="text-gray-900 dark:text-white whitespace-pre-wrap"
                            >
                                {{ event.descricao }}
                            </p>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div
                        class="pt-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">
                                    Criado em:
                                </span>
                                <p class="text-gray-900 dark:text-white mt-1">
                                    {{
                                        new Date(
                                            event.created_at
                                        ).toLocaleString("pt-PT")
                                    }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">
                                    Última atualização:
                                </span>
                                <p class="text-gray-900 dark:text-white mt-1">
                                    {{
                                        new Date(
                                            event.updated_at
                                        ).toLocaleString("pt-PT")
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
