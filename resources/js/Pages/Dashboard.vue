<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    FileText,
    ShoppingCart,
    CreditCard,
    Briefcase,
    TrendingUp,
    Wallet,
    AlertCircle,
    CheckSquare,
    Clock,
    AlertTriangle,
} from "lucide-vue-next";

const props = defineProps({
    stats: Object,
    urgentTasks: Array,
    recentActivity: Array,
    permissions: Object,
});

const getTaskTypeLabel = (type) => {
    const labels = {
        validate_stock: "Validar Stock",
        order_supplier: "Encomendar Fornecedor",
        warehouse_collect: "Recolher Armazém",
        receive_goods: "Receção Mercadoria",
        pack_order: "Embalar Encomenda",
        transport_guide: "Guia Transporte",
        schedule_transport: "Agendar Transporte",
        send_order: "Enviar Encomenda",
        pickup_order: "Levantamento Cliente",
        deliver_order: "Entregar ao Cliente",
        confirm_order: "Confirmar Encomenda",
        create_customer_invoice: "Criar Fatura Cliente",
    };
    return labels[type] || type;
};

const getStatusBadge = (status) => {
    const badges = {
        pendente:
            "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200",
        em_progresso:
            "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200",
        concluida:
            "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200",
    };
    return badges[status] || "";
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("pt-PT");
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <TrendingUp
                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Dashboard
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            Visão geral do sistema
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
            >
                <!-- Propostas Pendentes -->
                <div
                    v-if="
                        permissions.canViewProposals &&
                        stats.proposals_pending !== undefined
                    "
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg"
                                >
                                    <FileText
                                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Propostas Pendentes
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.proposals_pending }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Encomendas Cliente Ativas -->
                <div
                    v-if="
                        permissions.canViewCustomerOrders &&
                        stats.customer_orders_active !== undefined
                    "
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg"
                                >
                                    <ShoppingCart
                                        class="h-6 w-6 text-green-600 dark:text-green-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Encomendas Ativas
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.customer_orders_active }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faturas Fornecedor Pendentes -->
                <div
                    v-if="
                        permissions.canViewSupplierInvoices &&
                        stats.supplier_invoices_pending !== undefined
                    "
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-red-100 dark:bg-red-900/20 rounded-lg"
                                >
                                    <CreditCard
                                        class="h-6 w-6 text-red-600 dark:text-red-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Faturas Pendentes
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.supplier_invoices_pending }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ordens de Trabalho Em Progresso -->
                <div
                    v-if="
                        permissions.canViewWorkOrders &&
                        stats.work_orders_in_progress !== undefined
                    "
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg"
                                >
                                    <Briefcase
                                        class="h-6 w-6 text-purple-600 dark:text-purple-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Ordens Em Progresso
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.work_orders_in_progress }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saldo Contas Bancárias -->
                <div
                    v-if="
                        permissions.canViewBankAccounts &&
                        stats.bank_accounts_balance !== undefined
                    "
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-emerald-100 dark:bg-emerald-900/20 rounded-lg"
                                >
                                    <Wallet
                                        class="h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Saldo Bancário
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{
                                        formatCurrency(
                                            stats.bank_accounts_balance
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clientes com Saldo Devedor -->
                <div
                    v-if="
                        permissions.canViewClientAccounts &&
                        stats.clients_with_debt !== undefined
                    "
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-orange-100 dark:bg-orange-900/20 rounded-lg"
                                >
                                    <AlertCircle
                                        class="h-6 w-6 text-orange-600 dark:text-orange-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Clientes Devedores
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.clients_with_debt }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Minhas Tarefas Pendentes -->
                <div
                    v-if="stats.my_tasks_pending !== undefined"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg"
                                >
                                    <CheckSquare
                                        class="h-6 w-6 text-yellow-600 dark:text-yellow-400"
                                    />
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p
                                    class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Minhas Tarefas
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.my_tasks_pending }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tarefas Urgentes -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div
                        class="p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <Clock
                                    class="h-5 w-5 text-gray-500 dark:text-gray-400"
                                />
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                >
                                    Minhas Tarefas Urgentes
                                </h3>
                            </div>
                            <Link
                                :href="route('work-orders.my-tasks')"
                                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                Ver Todas
                            </Link>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="urgentTasks.length > 0" class="space-y-4">
                            <div
                                v-for="task in urgentTasks"
                                :key="task.id"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div
                                            class="flex items-center space-x-2 mb-2"
                                        >
                                            <AlertTriangle
                                                v-if="task.is_overdue"
                                                class="h-4 w-4 text-red-500"
                                            />
                                            <h4
                                                class="font-medium text-gray-900 dark:text-white"
                                            >
                                                {{
                                                    getTaskTypeLabel(
                                                        task.task_type
                                                    )
                                                }}
                                            </h4>
                                        </div>
                                        <p
                                            class="text-sm text-gray-600 dark:text-gray-400 mb-2"
                                        >
                                            {{ task.work_order_title }} -
                                            {{ task.customer_name }}
                                        </p>
                                        <div
                                            class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            <span
                                                >Prazo:
                                                {{
                                                    formatDate(task.due_date)
                                                }}</span
                                            >
                                            <span>•</span>
                                            <span>{{
                                                task.assigned_to_name
                                            }}</span>
                                        </div>
                                    </div>
                                    <span
                                        :class="getStatusBadge(task.status)"
                                        class="px-2 py-1 text-xs font-medium rounded-full"
                                    >
                                        {{
                                            task.status === "pendente"
                                                ? "Pendente"
                                                : "Em Progresso"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <CheckSquare
                                class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600 mb-3"
                            />
                            <p class="text-gray-500 dark:text-gray-400">
                                Sem tarefas urgentes no momento
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Atividade Recente -->
                <div
                    v-if="permissions.canViewRecentActivity"
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <div
                        class="p-6 border-b border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center space-x-3">
                            <TrendingUp
                                class="h-5 w-5 text-gray-500 dark:text-gray-400"
                            />
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                Atividade Recente
                            </h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="recentActivity.length > 0" class="space-y-4">
                            <div
                                v-for="activity in recentActivity"
                                :key="activity.id"
                                class="flex items-start space-x-3 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0"
                            >
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center"
                                    >
                                        <span
                                            class="text-xs font-medium text-blue-600 dark:text-blue-400"
                                        >
                                            {{ activity.causer_name.charAt(0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="text-sm text-gray-900 dark:text-white"
                                    >
                                        <span class="font-medium">{{
                                            activity.causer_name
                                        }}</span>
                                        {{
                                            activity.description === "created"
                                                ? "criou"
                                                : activity.description ===
                                                  "updated"
                                                ? "atualizou"
                                                : "eliminou"
                                        }}
                                        <span class="font-medium">{{
                                            activity.subject_type
                                        }}</span>
                                    </p>
                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                    >
                                        {{ activity.created_at }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <TrendingUp
                                class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600 mb-3"
                            />
                            <p class="text-gray-500 dark:text-gray-400">
                                Sem atividade recente
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
