<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import {
    CheckSquare,
    Clock,
    AlertCircle,
    Play,
    CheckCircle2,
    Funnel,
} from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";
import Select from "@/Components/ui/Select.vue";

const props = defineProps({
    tasks: Object,
    filters: Object,
    taskTypes: Object,
    customers: Array,
});

// Filtros locais
const selectedCustomer = ref(props.filters?.customer_id || "");
const selectedStatus = ref(props.filters?.status || "");
const showOverdue = ref(props.filters?.overdue || false);

// Watch para aplicar filtros
watch([selectedCustomer, selectedStatus, showOverdue], () => {
    router.get(
        route("work-orders.my-tasks"),
        {
            customer_id: selectedCustomer.value || undefined,
            status: selectedStatus.value || undefined,
            overdue: showOverdue.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
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
</script>

<template>
    <Head title="Minhas Tarefas" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-gray-100"
                    >
                        Minhas Tarefas
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Gerir tarefas atribuídas a mim
                    </p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div
            class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex items-center gap-3 mb-3">
                <Funnel class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                <h3
                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                    Filtros
                </h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Filtro de Cliente -->
                <div>
                    <label
                        for="customer_filter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                        Cliente
                    </label>
                    <Select
                        id="customer_filter"
                        v-model="selectedCustomer"
                        class="w-full"
                    >
                        <option value="">Todos os clientes</option>
                        <option
                            v-for="customer in customers"
                            :key="customer.id"
                            :value="customer.id"
                        >
                            {{ customer.name }}
                        </option>
                    </Select>
                </div>

                <!-- Filtro de Estado -->
                <div>
                    <label
                        for="status_filter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                    >
                        Estado
                    </label>
                    <Select
                        id="status_filter"
                        v-model="selectedStatus"
                        class="w-full"
                    >
                        <option value="">Pendentes e Em Progresso</option>
                        <option value="pendente">Pendente</option>
                        <option value="em_progresso">Em Progresso</option>
                        <option value="concluida">Concluída</option>
                        <option value="cancelada">Cancelada</option>
                    </Select>
                </div>

                <!-- Filtro de Atrasadas -->
                <div class="flex items-end">
                    <label class="flex items-center gap-2">
                        <input
                            v-model="showOverdue"
                            type="checkbox"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <span
                            class="text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Apenas atrasadas
                        </span>
                    </label>
                </div>

                <!-- Limpar Filtros -->
                <div class="flex items-end">
                    <Button
                        @click="
                            () => {
                                selectedCustomer = '';
                                selectedStatus = '';
                                showOverdue = false;
                            }
                        "
                        variant="outline"
                        size="sm"
                        class="w-full"
                    >
                        Limpar Filtros
                    </Button>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="space-y-4">
            <div
                v-for="task in tasks.data"
                :key="task.id"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Task Header -->
                            <div class="flex items-center gap-3 mb-3">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    {{ task.title }}
                                </h3>
                                <span
                                    :class="getStatusBadge(task.status)"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{
                                        task.status.charAt(0).toUpperCase() +
                                        task.status.slice(1).replace("_", " ")
                                    }}
                                </span>
                            </div>

                            <!-- Task Details -->
                            <div
                                class="space-y-2 text-sm text-gray-600 dark:text-gray-400"
                            >
                                <p v-if="task.description">
                                    {{ task.description }}
                                </p>
                                <div class="flex items-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <CheckSquare class="h-4 w-4" />
                                        Ordem: {{ task.work_order.title }}
                                    </span>
                                    <span
                                        v-if="task.work_order.customer_order"
                                        class="flex items-center gap-1"
                                    >
                                        Cliente:
                                        {{
                                            task.work_order.customer_order
                                                .customer?.name
                                        }}
                                    </span>
                                </div>
                                <div
                                    v-if="task.due_date"
                                    class="flex items-center gap-1"
                                >
                                    <Clock class="h-4 w-4" />
                                    Prazo:
                                    {{
                                        new Date(
                                            task.due_date
                                        ).toLocaleDateString("pt-PT")
                                    }}
                                    <span
                                        v-if="task.is_overdue"
                                        class="text-red-600 dark:text-red-400 font-medium"
                                    >
                                        (Atrasada)
                                    </span>
                                </div>
                                <div
                                    v-if="task.depends_on"
                                    class="flex items-center gap-1 text-amber-600 dark:text-amber-400"
                                >
                                    <AlertCircle class="h-4 w-4" />
                                    Depende de: {{ task.depends_on.title }}
                                    <span
                                        v-if="!task.can_start"
                                        class="font-medium"
                                    >
                                        (Bloqueada)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <Button
                                v-if="
                                    task.status === 'pendente' && task.can_start
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

                            <Link
                                :href="
                                    route(
                                        'work-orders.show',
                                        task.work_order.id
                                    )
                                "
                                class="px-3 py-1.5 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400"
                            >
                                Ver Ordem
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="tasks.data.length === 0"
                class="bg-white dark:bg-gray-800 p-12 text-center rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <CheckSquare
                    class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4"
                />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"
                >
                    Sem tarefas pendentes
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Não tens tarefas atribuídas neste momento.
                </p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="tasks.links && tasks.links.length > 3" class="mt-6">
            <div class="flex items-center justify-center gap-1">
                <component
                    v-for="(link, index) in tasks.links"
                    :key="index"
                    :is="link.url ? Link : 'span'"
                    :href="link.url || undefined"
                    v-html="link.label"
                    :class="[
                        'px-3 py-2 text-sm rounded',
                        link.active
                            ? 'bg-blue-600 text-white'
                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                        !link.url && 'opacity-50 cursor-not-allowed',
                    ]"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
