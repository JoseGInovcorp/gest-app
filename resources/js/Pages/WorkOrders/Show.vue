<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import {
    ArrowLeft,
    Clock,
    User,
    Users,
    AlertCircle,
    CheckCircle2,
    Play,
    Plus,
    Lock,
    Unlock,
} from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";

const props = defineProps({
    workOrder: Object,
    can: Object,
    users: Array,
    roles: Array,
    taskTypes: Object,
});

const getStatusBadge = (status) => {
    const badges = {
        pendente:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        em_progresso:
            "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        concluida:
            "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
        cancelada:
            "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
    };
    return badges[status] || badges.pendente;
};

const getPriorityBadge = (priority) => {
    const badges = {
        urgente: "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200",
        alta: "bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200",
        normal: "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        baixa: "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200",
    };
    return badges[priority] || badges.normal;
};

const assigningTask = ref(null);
const showSyncDialog = ref(false);
const assignForm = useForm({
    assigned_to: null,
    assigned_group: null,
});

const showAssignModal = (task) => {
    assigningTask.value = task;
    assignForm.assigned_to = task.assigned_to?.id || null;
    assignForm.assigned_group = task.assigned_group || null;
};

const assignTask = () => {
    assignForm.post(route("work-order-tasks.assign", assigningTask.value.id), {
        onSuccess: () => {
            assigningTask.value = null;
            assignForm.reset();
        },
    });
};

const startTask = (taskId) => {
    router.post(
        route("work-order-tasks.start", taskId),
        {},
        {
            preserveScroll: true,
        }
    );
};

const completeTask = (taskId) => {
    const notes = prompt("Observações sobre a conclusão da tarefa (opcional):");
    router.post(
        route("work-order-tasks.complete", taskId),
        {
            notes: notes,
        },
        {
            preserveScroll: true,
        }
    );
};

const openSyncDialog = () => {
    showSyncDialog.value = true;
};

const confirmSync = () => {
    router.post(
        route("work-orders.sync-template", props.workOrder.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                showSyncDialog.value = false;
            },
        }
    );
};

const cancelSync = () => {
    showSyncDialog.value = false;
};
</script>

<template>
    <Head :title="workOrder.title" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <Link
                :href="route('work-orders.index')"
                class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-4"
            >
                <ArrowLeft class="h-4 w-4 mr-1" />
                Voltar às Ordens de Trabalho
            </Link>

            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1
                            class="text-3xl font-bold text-gray-900 dark:text-gray-100"
                        >
                            {{ workOrder.title }}
                        </h1>
                        <span
                            :class="getStatusBadge(workOrder.status)"
                            class="px-3 py-1 text-sm font-medium rounded-full"
                        >
                            {{
                                workOrder.status.charAt(0).toUpperCase() +
                                workOrder.status.slice(1).replace("_", " ")
                            }}
                        </span>
                        <span
                            :class="getPriorityBadge(workOrder.priority)"
                            class="px-3 py-1 text-sm font-medium rounded-full"
                        >
                            {{
                                workOrder.priority.charAt(0).toUpperCase() +
                                workOrder.priority.slice(1)
                            }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Progresso: {{ workOrder.progress_percentage }}%
                    </p>
                </div>
            </div>
        </div>

        <!-- Work Order Info -->
        <div
            class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <h2
                class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4"
            >
                Informações da Ordem
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 dark:text-gray-400"
                        >Encomenda:</span
                    >
                    <span
                        class="ml-2 font-medium text-gray-900 dark:text-gray-100"
                    >
                        <Link
                            v-if="workOrder.customer_order"
                            :href="
                                route(
                                    'customer-orders.show',
                                    workOrder.customer_order.id
                                )
                            "
                            class="text-blue-600 hover:text-blue-700"
                        >
                            #{{ workOrder.customer_order.order_number }}
                        </Link>
                        <span v-else>-</span>
                    </span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400"
                        >Cliente:</span
                    >
                    <span
                        class="ml-2 font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ workOrder.customer_order?.customer?.name || "-" }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400"
                        >Criada por:</span
                    >
                    <span
                        class="ml-2 font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{ workOrder.creator?.name || "-" }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400"
                        >Data de Criação:</span
                    >
                    <span
                        class="ml-2 font-medium text-gray-900 dark:text-gray-100"
                    >
                        {{
                            new Date(workOrder.created_at).toLocaleDateString(
                                "pt-PT"
                            )
                        }}
                    </span>
                </div>
            </div>
            <div v-if="workOrder.description" class="mt-4">
                <span class="text-gray-600 dark:text-gray-400">Descrição:</span>
                <p class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ workOrder.description }}
                </p>
            </div>
        </div>

        <!-- Tasks Timeline -->
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex items-center justify-between mb-6">
                <h2
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                >
                    Tarefas ({{ workOrder.tasks?.length || 0 }})
                </h2>

                <!-- Botão Sincronizar com Template -->
                <button
                    v-if="can.edit"
                    @click="openSyncDialog"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Atualizar tarefas com configurações atuais do template"
                >
                    <svg
                        class="w-4 h-4 mr-2"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        ></path>
                    </svg>
                    Sincronizar Template
                </button>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(task, index) in workOrder.tasks"
                    :key="task.id"
                    class="relative"
                >
                    <!-- Timeline connector -->
                    <div
                        v-if="index < workOrder.tasks.length - 1"
                        class="absolute left-6 top-12 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"
                    ></div>

                    <!-- Task Card -->
                    <div
                        class="relative flex gap-4 p-4 rounded-lg border transition"
                        :class="[
                            task.status === 'concluida'
                                ? 'border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-950'
                                : task.status === 'em_progresso'
                                ? 'border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-950'
                                : task.can_start
                                ? 'border-gray-200 dark:border-gray-700'
                                : 'border-gray-200 dark:border-gray-700 opacity-60',
                        ]"
                    >
                        <!-- Status Icon -->
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center"
                                :class="[
                                    task.status === 'concluida'
                                        ? 'bg-green-100 dark:bg-green-900'
                                        : task.status === 'em_progresso'
                                        ? 'bg-blue-100 dark:bg-blue-900'
                                        : task.can_start
                                        ? 'bg-gray-100 dark:bg-gray-800'
                                        : 'bg-gray-100 dark:bg-gray-800',
                                ]"
                            >
                                <CheckCircle2
                                    v-if="task.status === 'concluida'"
                                    class="h-6 w-6 text-green-600 dark:text-green-400"
                                />
                                <Play
                                    v-else-if="task.status === 'em_progresso'"
                                    class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                />
                                <Lock
                                    v-else-if="!task.can_start"
                                    class="h-6 w-6 text-gray-400"
                                />
                                <Unlock
                                    v-else
                                    class="h-6 w-6 text-gray-600 dark:text-gray-400"
                                />
                            </div>
                        </div>

                        <!-- Task Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3
                                            class="font-semibold text-gray-900 dark:text-gray-100"
                                        >
                                            {{ task.title }}
                                        </h3>
                                        <span
                                            :class="getStatusBadge(task.status)"
                                            class="px-2 py-0.5 text-xs font-medium rounded-full"
                                        >
                                            {{
                                                task.status
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                task.status
                                                    .slice(1)
                                                    .replace("_", " ")
                                            }}
                                        </span>
                                    </div>

                                    <p
                                        v-if="task.description"
                                        class="text-sm text-gray-600 dark:text-gray-400 mb-2"
                                    >
                                        {{ task.description }}
                                    </p>

                                    <div
                                        class="flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400"
                                    >
                                        <span
                                            v-if="task.assigned_to"
                                            class="flex items-center gap-1"
                                        >
                                            <User class="h-3 w-3" />
                                            {{ task.assigned_to.name }}
                                        </span>
                                        <span
                                            v-if="task.assigned_group"
                                            class="flex items-center gap-1"
                                        >
                                            <Users class="h-3 w-3" />
                                            {{ task.assigned_group }}
                                        </span>
                                        <span
                                            v-if="task.due_date"
                                            class="flex items-center gap-1"
                                        >
                                            <Clock class="h-3 w-3" />
                                            {{
                                                new Date(
                                                    task.due_date
                                                ).toLocaleDateString("pt-PT")
                                            }}
                                            <span
                                                v-if="task.is_overdue"
                                                class="text-red-600 font-medium"
                                            >
                                                (Atrasada)
                                            </span>
                                        </span>
                                        <span
                                            v-if="task.depends_on"
                                            class="flex items-center gap-1"
                                        >
                                            <AlertCircle class="h-3 w-3" />
                                            Depende: {{ task.depends_on.title }}
                                        </span>
                                    </div>

                                    <p
                                        v-if="task.notes"
                                        class="mt-2 text-xs text-gray-600 dark:text-gray-400 italic"
                                    >
                                        Observações: {{ task.notes }}
                                    </p>
                                </div>

                                <!-- Task Actions -->
                                <div class="flex gap-2 flex-shrink-0">
                                    <Button
                                        v-if="
                                            can.update &&
                                            task.status === 'pendente'
                                        "
                                        @click="showAssignModal(task)"
                                        variant="outline"
                                        size="sm"
                                    >
                                        Atribuir
                                    </Button>

                                    <Button
                                        v-if="
                                            task.status === 'pendente' &&
                                            task.can_start
                                        "
                                        @click="startTask(task.id)"
                                        variant="outline"
                                        size="sm"
                                    >
                                        <Play class="h-4 w-4 mr-1" />
                                        Iniciar
                                    </Button>

                                    <Button
                                        v-if="task.status === 'em_progresso'"
                                        @click="completeTask(task.id)"
                                        variant="default"
                                        size="sm"
                                    >
                                        <CheckCircle2 class="h-4 w-4 mr-1" />
                                        Concluir
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Task Modal -->
        <div
            v-if="assigningTask"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="assigningTask = null"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4"
            >
                <h3
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4"
                >
                    Atribuir Tarefa
                </h3>

                <div class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Atribuir a Utilizador
                        </label>
                        <select
                            v-model="assignForm.assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        >
                            <option :value="null">
                                Nenhum utilizador específico
                            </option>
                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Atribuir a Grupo/Papel
                        </label>
                        <select
                            v-model="assignForm.assigned_group"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        >
                            <option :value="null">Nenhum grupo</option>
                            <option
                                v-for="role in roles"
                                :key="role.name"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <Button @click="assigningTask = null" variant="outline">
                        Cancelar
                    </Button>
                    <Button @click="assignTask" variant="default">
                        Atribuir
                    </Button>
                </div>
            </div>
        </div>

        <!-- Diálogo Sincronizar Template -->
        <ConfirmDialog
            :show="showSyncDialog"
            title="Sincronizar com Template"
            message="Tem a certeza que pretende sincronizar as tarefas com as configurações atuais do template? Isto irá atualizar os grupos atribuídos, títulos e descrições de todas as tarefas."
            @confirm="confirmSync"
            @cancel="cancelSync"
        />
    </AuthenticatedLayout>
</template>
