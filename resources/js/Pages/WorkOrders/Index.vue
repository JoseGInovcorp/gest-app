<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { Plus, Search, Filter, Briefcase } from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";

const props = defineProps({
    workOrders: Object,
    filters: Object,
    can: Object,
});

const search = ref(props.filters?.search || "");
const statusFilter = ref(props.filters?.status || "");
const priorityFilter = ref(props.filters?.priority || "");

const applyFilters = () => {
    router.get(
        route("work-orders.index"),
        {
            search: search.value,
            status: statusFilter.value,
            priority: priorityFilter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const clearFilters = () => {
    search.value = "";
    statusFilter.value = "";
    priorityFilter.value = "";
    applyFilters();
};

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

const deleteWorkOrder = (id) => {
    if (
        confirm("Tens a certeza que desejas eliminar esta ordem de trabalho?")
    ) {
        router.delete(route("work-orders.destroy", id));
    }
};
</script>

<template>
    <Head title="Ordens de Trabalho" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-gray-100"
                    >
                        Ordens de Trabalho
                    </h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Gerir todas as ordens de trabalho
                    </p>
                </div>
                <Link
                    v-if="can.create"
                    :href="route('work-orders.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                >
                    <Plus class="h-5 w-5 mr-2" />
                    Nova Ordem
                </Link>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Pesquisar
                    </label>
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400"
                        />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Pesquisar por título ou nº encomenda..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
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
                        v-model="statusFilter"
                        @change="applyFilters"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    >
                        <option value="">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="em_progresso">Em Progresso</option>
                        <option value="concluida">Concluída</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Prioridade
                    </label>
                    <select
                        v-model="priorityFilter"
                        @change="applyFilters"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    >
                        <option value="">Todas</option>
                        <option value="urgente">Urgente</option>
                        <option value="alta">Alta</option>
                        <option value="normal">Normal</option>
                        <option value="baixa">Baixa</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <Button @click="applyFilters" variant="default" size="sm">
                    <Filter class="h-4 w-4 mr-1" />
                    Aplicar Filtros
                </Button>
                <Button @click="clearFilters" variant="outline" size="sm">
                    Limpar
                </Button>
            </div>
        </div>

        <!-- Work Orders List -->
        <div class="space-y-4">
            <div
                v-for="workOrder in workOrders.data"
                :key="workOrder.id"
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 transition"
            >
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    {{ workOrder.title }}
                                </h3>
                                <span
                                    :class="getStatusBadge(workOrder.status)"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{
                                        workOrder.status
                                            .charAt(0)
                                            .toUpperCase() +
                                        workOrder.status
                                            .slice(1)
                                            .replace("_", " ")
                                    }}
                                </span>
                                <span
                                    :class="
                                        getPriorityBadge(workOrder.priority)
                                    "
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{
                                        workOrder.priority
                                            .charAt(0)
                                            .toUpperCase() +
                                        workOrder.priority.slice(1)
                                    }}
                                </span>
                            </div>

                            <p
                                v-if="workOrder.description"
                                class="text-sm text-gray-600 dark:text-gray-400 mb-3"
                            >
                                {{ workOrder.description }}
                            </p>

                            <div
                                class="flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400"
                            >
                                <span v-if="workOrder.customer_order">
                                    Enc: #{{
                                        workOrder.customer_order.order_number
                                    }}
                                </span>
                                <span v-if="workOrder.customer_order?.customer">
                                    Cliente:
                                    {{ workOrder.customer_order.customer.name }}
                                </span>
                                <span>
                                    Criada:
                                    {{
                                        new Date(
                                            workOrder.created_at
                                        ).toLocaleDateString("pt-PT")
                                    }}
                                </span>
                                <span
                                    v-if="
                                        workOrder.progress_percentage !==
                                        undefined
                                    "
                                >
                                    Progresso:
                                    {{ workOrder.progress_percentage }}%
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 ml-4">
                            <Link
                                :href="route('work-orders.show', workOrder.id)"
                                class="px-3 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
                            >
                                Ver Detalhes
                            </Link>
                            <button
                                v-if="can.delete"
                                @click="deleteWorkOrder(workOrder.id)"
                                class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg transition"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="workOrders.data.length === 0"
                class="bg-white dark:bg-gray-800 p-12 text-center rounded-lg border border-gray-200 dark:border-gray-700"
            >
                <Briefcase
                    class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4"
                />
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2"
                >
                    Sem ordens de trabalho
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Ainda não existem ordens de trabalho registadas.
                </p>
                <Link
                    v-if="can.create"
                    :href="route('work-orders.create')"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
                >
                    <Plus class="h-5 w-5 mr-2" />
                    Criar Primeira Ordem
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="workOrders.links && workOrders.links.length > 3"
            class="mt-6"
        >
            <div class="flex items-center justify-center gap-1">
                <component
                    v-for="(link, index) in workOrders.links"
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
