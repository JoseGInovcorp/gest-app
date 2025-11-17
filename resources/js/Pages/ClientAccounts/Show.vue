<template>
    <Head title="Detalhes do Movimento" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <DollarSign
                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Detalhes do Movimento
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            Visualizar informações do movimento
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('client-accounts.edit', movement.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <Pencil class="h-4 w-4" />
                        Editar
                    </Link>
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
                        :href="route('client-accounts.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Conta Corrente Clientes
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Detalhes</li>
            </ol>
        </nav>

        <!-- Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info Card -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h2
                            class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                        >
                            Informações do Movimento
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Cliente -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Cliente
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ movement.entity.name }}
                                </p>
                            </div>

                            <!-- Data do Movimento -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Data do Movimento
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ formatDate(movement.data_movimento) }}
                                </p>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Tipo
                                </label>
                                <p>
                                    <span
                                        :class="[
                                            'px-3 py-1 text-sm rounded-full font-medium',
                                            movement.tipo === 'debito'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
                                                : 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                        ]"
                                    >
                                        {{
                                            movement.tipo === "debito"
                                                ? "Débito (Cliente deve)"
                                                : "Crédito (Cliente pagou)"
                                        }}
                                    </span>
                                </p>
                            </div>

                            <!-- Valor -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Valor
                                </label>
                                <p
                                    :class="[
                                        'text-lg font-semibold',
                                        movement.tipo === 'debito'
                                            ? 'text-red-600 dark:text-red-400'
                                            : 'text-green-600 dark:text-green-400',
                                    ]"
                                >
                                    {{ formatCurrency(movement.valor) }}
                                </p>
                            </div>

                            <!-- Categoria -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Categoria
                                </label>
                                <p>
                                    <span
                                        :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            getCategoryBadgeClass(
                                                movement.categoria
                                            ),
                                        ]"
                                    >
                                        {{ formatCategory(movement.categoria) }}
                                    </span>
                                </p>
                            </div>

                            <!-- Referência -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Referência
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ movement.referencia || "-" }}
                                </p>
                            </div>

                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Descrição
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white"
                                >
                                    {{ movement.descricao }}
                                </p>
                            </div>

                            <!-- Observações -->
                            <div
                                v-if="movement.observacoes"
                                class="md:col-span-2"
                            >
                                <label
                                    class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"
                                >
                                    Observações
                                </label>
                                <p
                                    class="text-base text-gray-900 dark:text-white whitespace-pre-wrap"
                                >
                                    {{ movement.observacoes }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Saldo Card -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2"
                        >
                            Saldo Após Movimento
                        </h3>
                        <p
                            :class="[
                                'text-2xl font-bold',
                                movement.saldo_apos > 0
                                    ? 'text-red-600 dark:text-red-400'
                                    : movement.saldo_apos < 0
                                    ? 'text-green-600 dark:text-green-400'
                                    : 'text-gray-600 dark:text-gray-400',
                            ]"
                        >
                            {{ formatCurrency(movement.saldo_apos) }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                        >
                            {{
                                movement.saldo_apos > 0
                                    ? "Cliente deve à empresa"
                                    : movement.saldo_apos < 0
                                    ? "Empresa deve ao cliente"
                                    : "Saldo equilibrado"
                            }}
                        </p>
                    </div>
                </div>

                <!-- Metadata Card -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4"
                        >
                            Informações do Sistema
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label
                                    class="block text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Criado em
                                </label>
                                <p
                                    class="text-sm text-gray-900 dark:text-white"
                                >
                                    {{ formatDateTime(movement.created_at) }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Última atualização
                                </label>
                                <p
                                    class="text-sm text-gray-900 dark:text-white"
                                >
                                    {{ formatDateTime(movement.updated_at) }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs text-gray-500 dark:text-gray-400"
                                >
                                    ID do Movimento
                                </label>
                                <p
                                    class="text-sm text-gray-900 dark:text-white font-mono"
                                >
                                    #{{ movement.id }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3
                            class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4"
                        >
                            Ações
                        </h3>
                        <div class="space-y-2">
                            <Link
                                :href="
                                    route('client-accounts.edit', movement.id)
                                "
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <Pencil class="h-4 w-4" />
                                Editar Movimento
                            </Link>
                            <button
                                @click="openDeleteDialog"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                            >
                                <Trash2 class="h-4 w-4" />
                                Eliminar Movimento
                            </button>
                            <Link
                                :href="route('client-accounts.index')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                            >
                                <ArrowLeft class="h-4 w-4" />
                                Voltar à Lista
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            type="danger"
            title="Eliminar Movimento"
            message="Tem a certeza que deseja eliminar este movimento?"
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            @confirm="deleteMovement"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import { DollarSign, Pencil, Trash2, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    movement: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("pt-PT");
};

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString("pt-PT");
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value);
};

const formatCategory = (category) => {
    const categories = {
        fatura: "Fatura",
        pagamento: "Pagamento",
        nota_credito: "Nota Crédito",
        nota_debito: "Nota Débito",
        juros: "Juros",
        ajuste: "Ajuste",
        outros: "Outros",
    };
    return categories[category] || category;
};

const getCategoryBadgeClass = (category) => {
    const classes = {
        fatura: "bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400",
        pagamento:
            "bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400",
        nota_credito:
            "bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400",
        nota_debito:
            "bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400",
        juros: "bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400",
        ajuste: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400",
        outros: "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400",
    };
    return (
        classes[category] ||
        "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400"
    );
};

const showDeleteDialog = ref(false);

const openDeleteDialog = () => {
    showDeleteDialog.value = true;
};

const deleteMovement = () => {
    router.delete(route("client-accounts.destroy", props.movement.id), {
        onSuccess: () => {
            router.visit(route("client-accounts.index"));
        },
        onFinish: () => {
            showDeleteDialog.value = false;
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
};
</script>
