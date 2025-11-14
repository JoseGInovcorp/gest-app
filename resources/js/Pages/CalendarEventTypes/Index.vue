<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Table from "@/Components/ui/table/Table.vue";
import TableBody from "@/Components/ui/table/TableBody.vue";
import TableCell from "@/Components/ui/table/TableCell.vue";
import TableHead from "@/Components/ui/table/TableHead.vue";
import TableHeader from "@/Components/ui/table/TableHeader.vue";
import TableRow from "@/Components/ui/table/TableRow.vue";
import Input from "@/Components/ui/Input.vue";
import Badge from "@/Components/ui/Badge.vue";
import { Plus, Search, Pencil, Trash2, Calendar } from "lucide-vue-next";

const props = defineProps({
    eventTypes: Array,
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

const searchQuery = ref("");

const filteredEventTypes = computed(() => {
    if (!searchQuery.value) {
        return props.eventTypes;
    }

    const query = searchQuery.value.toLowerCase();
    return props.eventTypes.filter(
        (type) =>
            type.name.toLowerCase().includes(query) ||
            (type.description && type.description.toLowerCase().includes(query))
    );
});

const deleteEventType = (id) => {
    if (confirm("Tem certeza que deseja eliminar este tipo de evento?")) {
        router.delete(route("calendar-event-types.destroy", id));
    }
};
</script>

<template>
    <Head title="Calendário - Tipos" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
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
                        Calendário - Tipos de Eventos
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir tipos de eventos do calendário
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
                <li>
                    <span class="hover:text-gray-700 dark:hover:text-gray-200">
                        Configurações
                    </span>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">
                    Calendário - Tipos
                </li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Pesquisar por nome ou descrição..."
                                class="pl-10"
                            />
                        </div>
                    </div>

                    <Link
                        v-if="can.create"
                        :href="route('calendar-event-types.create')"
                    >
                        <Button
                            class="bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            <Plus class="h-4 w-4 mr-2" />
                            Adicionar Tipo
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="p-6">
                <div class="rounded-md border dark:border-gray-700">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nome</TableHead>
                                <TableHead>Descrição</TableHead>
                                <TableHead>Cor</TableHead>
                                <TableHead>Ícone</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="filteredEventTypes.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="text-center text-gray-500 dark:text-gray-400"
                                >
                                    Nenhum tipo de evento encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow
                                v-for="eventType in filteredEventTypes"
                                :key="eventType.id"
                            >
                                <TableCell class="font-medium">
                                    {{ eventType.name }}
                                </TableCell>
                                <TableCell>
                                    {{ eventType.description || "-" }}
                                </TableCell>
                                <TableCell>
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600"
                                            :style="{
                                                backgroundColor:
                                                    eventType.color,
                                            }"
                                        ></div>
                                        <span
                                            class="text-sm text-gray-600 dark:text-gray-400"
                                        >
                                            {{ eventType.color }}
                                        </span>
                                    </div>
                                </TableCell>
                                <TableCell>
                                    <code
                                        class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded"
                                    >
                                        {{ eventType.icon }}
                                    </code>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :class="
                                            eventType.is_active
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
                                        "
                                    >
                                        {{
                                            eventType.is_active
                                                ? "Ativo"
                                                : "Inativo"
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            v-if="can.edit"
                                            :href="
                                                route(
                                                    'calendar-event-types.edit',
                                                    eventType.id
                                                )
                                            "
                                        >
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="h-8 w-8 p-0"
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="can.delete"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                                            @click="
                                                deleteEventType(eventType.id)
                                            "
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
