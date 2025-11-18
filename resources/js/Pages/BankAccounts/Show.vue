<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Button from "@/Components/ui/Button.vue";
import {
    ArrowLeft,
    TrendingUp,
    TrendingDown,
    Calendar,
    FileDown,
    CreditCard,
    Pencil,
} from "lucide-vue-next";

const props = defineProps({
    account: Object,
    can: Object,
});

// Formatar valores monetários
const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: props.account.moeda || "EUR",
    }).format(value);
};

// Formatar data
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("pt-PT");
};

// Calcular totais
const totais = {
    creditos:
        props.account.transactions
            ?.filter((t) => t.tipo === "credito")
            .reduce((sum, t) => sum + parseFloat(t.valor), 0) || 0,
    debitos:
        props.account.transactions
            ?.filter((t) => t.tipo === "debito")
            .reduce((sum, t) => sum + parseFloat(t.valor), 0) || 0,
};

const saldoCalculado =
    parseFloat(props.account.saldo_inicial) + totais.creditos - totais.debitos;
</script>

<template>
    <Head :title="`Extrato - ${account.nome}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10 dark:bg-blue-400/10"
                        >
                            <CreditCard
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                {{ account.nome }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ account.banco }} • {{ account.iban }}
                            </p>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <Link :href="route('bank-accounts.index')">
                            <Button variant="outline">
                                <ArrowLeft class="h-4 w-4 mr-2" />
                                Voltar
                            </Button>
                        </Link>

                        <a
                            :href="
                                route('bank-accounts.export-pdf', account.id)
                            "
                            target="_blank"
                        >
                            <Button
                                class="bg-green-600 hover:bg-green-700 text-white dark:bg-green-600 dark:hover:bg-green-700"
                            >
                                <FileDown class="h-4 w-4 mr-2" />
                                Exportar PDF
                            </Button>
                        </a>

                        <Link
                            v-if="can.edit"
                            :href="route('bank-accounts.edit', account.id)"
                        >
                            <Button>
                                <Pencil class="h-4 w-4 mr-2" />
                                Editar
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Resumo Financeiro -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Saldo Inicial -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p
                        class="text-sm font-medium text-gray-600 dark:text-gray-400"
                    >
                        Saldo Inicial
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-900 dark:text-white mt-2"
                    >
                        {{ formatCurrency(account.saldo_inicial) }}
                    </p>
                </div>

                <!-- Total Créditos -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <p
                            class="text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Total Créditos
                        </p>
                        <TrendingUp class="w-5 h-5 text-green-500" />
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-2">
                        {{ formatCurrency(totais.creditos) }}
                    </p>
                </div>

                <!-- Total Débitos -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <p
                            class="text-sm font-medium text-gray-600 dark:text-gray-400"
                        >
                            Total Débitos
                        </p>
                        <TrendingDown class="w-5 h-5 text-red-500" />
                    </div>
                    <p class="text-2xl font-bold text-red-600 mt-2">
                        {{ formatCurrency(totais.debitos) }}
                    </p>
                </div>

                <!-- Saldo Atual -->
                <div
                    class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg shadow p-6"
                >
                    <p class="text-sm font-medium text-blue-100">Saldo Atual</p>
                    <p class="text-2xl font-bold text-white mt-2">
                        {{ formatCurrency(account.saldo_atual) }}
                    </p>
                    <p class="text-xs text-blue-100 mt-1">
                        Calculado: {{ formatCurrency(saldoCalculado) }}
                    </p>
                </div>
            </div>

            <!-- Extrato de Movimentos -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700"
                >
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Extrato de Movimentos
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ account.transactions?.length || 0 }} movimentos
                        registados
                    </p>
                </div>

                <!-- Lista de Movimentos -->
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div
                        v-if="
                            !account.transactions ||
                            account.transactions.length === 0
                        "
                        class="px-6 py-12 text-center"
                    >
                        <Calendar
                            class="w-12 h-12 mx-auto text-gray-400 mb-4"
                        />
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Nenhum movimento registado nesta conta.
                        </p>
                    </div>

                    <div
                        v-for="transaction in account.transactions"
                        :key="transaction.id"
                        class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div
                                        :class="[
                                            'flex items-center justify-center w-10 h-10 rounded-full',
                                            transaction.tipo === 'credito'
                                                ? 'bg-green-100 dark:bg-green-900/30'
                                                : 'bg-red-100 dark:bg-red-900/30',
                                        ]"
                                    >
                                        <TrendingUp
                                            v-if="
                                                transaction.tipo === 'credito'
                                            "
                                            class="w-5 h-5 text-green-600 dark:text-green-400"
                                        />
                                        <TrendingDown
                                            v-else
                                            class="w-5 h-5 text-red-600 dark:text-red-400"
                                        />
                                    </div>

                                    <div>
                                        <p
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ transaction.descricao }}
                                        </p>
                                        <div
                                            class="flex items-center space-x-3 mt-1"
                                        >
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                {{
                                                    formatDate(
                                                        transaction.data_movimento
                                                    )
                                                }}
                                            </span>
                                            <span
                                                v-if="transaction.categoria"
                                                class="text-xs px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                                            >
                                                {{ transaction.categoria }}
                                            </span>
                                            <span
                                                v-if="transaction.referencia"
                                                class="text-xs text-gray-500 dark:text-gray-400"
                                            >
                                                Ref:
                                                {{ transaction.referencia }}
                                            </span>
                                        </div>
                                        <p
                                            v-if="transaction.observacoes"
                                            class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                        >
                                            {{ transaction.observacoes }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right ml-4">
                                <p
                                    :class="[
                                        'text-lg font-semibold',
                                        transaction.tipo === 'credito'
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-red-600 dark:text-red-400',
                                    ]"
                                >
                                    {{
                                        transaction.tipo === "credito"
                                            ? "+"
                                            : "-"
                                    }}
                                    {{ formatCurrency(transaction.valor) }}
                                </p>
                                <p
                                    v-if="transaction.saldo_apos"
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                >
                                    Saldo:
                                    {{ formatCurrency(transaction.saldo_apos) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
