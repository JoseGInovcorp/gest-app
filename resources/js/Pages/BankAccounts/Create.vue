<template>
    <Head title="Nova Conta Bancária" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <CreditCard
                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Nova Conta Bancária
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Adicionar uma nova conta bancária à empresa
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
                    <Link
                        :href="route('bank-accounts.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Contas Bancárias
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Nova Conta</li>
            </ol>
        </nav>

        <!-- Form Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <Form @submit="submit" class="p-6">
                <!-- Informações Básicas -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Informações Básicas
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome da Conta -->
                        <FormField v-slot="{ field }" name="nome">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Nome da Conta
                                <span class="text-red-500">*</span>
                            </label>
                            <Input
                                v-bind="field"
                                v-model="form.nome"
                                type="text"
                                required
                            />
                            <p
                                v-if="form.errors.nome"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.nome }}
                            </p>
                        </FormField>

                        <!-- Banco -->
                        <FormField v-slot="{ field }" name="banco">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Banco
                                <span class="text-red-500">*</span>
                            </label>
                            <Input
                                v-bind="field"
                                v-model="form.banco"
                                type="text"
                                required
                            />
                            <p
                                v-if="form.errors.banco"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.banco }}
                            </p>
                        </FormField>

                        <!-- IBAN -->
                        <FormField v-slot="{ field }" name="iban">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                IBAN
                                <span class="text-red-500">*</span>
                            </label>
                            <Input
                                v-bind="field"
                                v-model="form.iban"
                                type="text"
                                placeholder="PT50 0000 0000 0000 0000 0000 0"
                                required
                            />
                            <p
                                v-if="form.errors.iban"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.iban }}
                            </p>
                        </FormField>

                        <!-- SWIFT/BIC -->
                        <FormField v-slot="{ field }" name="swift_bic">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                SWIFT/BIC
                            </label>
                            <Input
                                v-bind="field"
                                v-model="form.swift_bic"
                                type="text"
                                maxlength="11"
                            />
                            <p
                                v-if="form.errors.swift_bic"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.swift_bic }}
                            </p>
                        </FormField>
                    </div>
                </div>

                <!-- Detalhes Financeiros -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Detalhes Financeiros
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Saldo Inicial -->
                        <FormField v-slot="{ field }" name="saldo_inicial">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Saldo Inicial
                                <span class="text-red-500">*</span>
                            </label>
                            <Input
                                v-bind="field"
                                v-model="form.saldo_inicial"
                                type="number"
                                step="0.01"
                                required
                            />
                            <p
                                v-if="form.errors.saldo_inicial"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.saldo_inicial }}
                            </p>
                        </FormField>

                        <!-- Moeda -->
                        <FormField v-slot="{ field }" name="moeda">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Moeda
                                <span class="text-red-500">*</span>
                            </label>
                            <Select
                                v-bind="field"
                                v-model="form.moeda"
                                required
                            >
                                <option value="EUR">EUR (€)</option>
                                <option value="USD">USD ($)</option>
                                <option value="GBP">GBP (£)</option>
                            </Select>
                            <p
                                v-if="form.errors.moeda"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.moeda }}
                            </p>
                        </FormField>

                        <!-- Tipo de Conta -->
                        <FormField v-slot="{ field }" name="tipo">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Tipo de Conta
                                <span class="text-red-500">*</span>
                            </label>
                            <Select
                                v-bind="field"
                                v-model="form.tipo"
                                required
                            >
                                <option value="corrente">Corrente</option>
                                <option value="poupanca">Poupança</option>
                                <option value="credito">Crédito</option>
                                <option value="investimento">
                                    Investimento
                                </option>
                            </Select>
                            <p
                                v-if="form.errors.tipo"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.tipo }}
                            </p>
                        </FormField>
                    </div>
                </div>

                <!-- Status e Observações -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Status e Observações
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Estado -->
                        <FormField v-slot="{ field }" name="estado">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Estado
                                <span class="text-red-500">*</span>
                            </label>
                            <Select
                                v-bind="field"
                                v-model="form.estado"
                                required
                            >
                                <option value="ativa">Ativa</option>
                                <option value="inativa">Inativa</option>
                                <option value="encerrada">Encerrada</option>
                            </Select>
                            <p
                                v-if="form.errors.estado"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.estado }}
                            </p>
                        </FormField>

                        <!-- Observações -->
                        <FormField v-slot="{ field }" name="observacoes" class="md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Observações
                            </label>
                            <Textarea
                                v-bind="field"
                                v-model="form.observacoes"
                                rows="3"
                            />
                            <p
                                v-if="form.errors.observacoes"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.observacoes }}
                            </p>
                        </FormField>
                    </div>
                </div>

                <!-- Buttons -->
                <div
                    class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700"
                >
                    <Link
                        :href="route('bank-accounts.index')"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                        Cancelar
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        variant="default"
                    >
                        <span v-if="form.processing">A guardar...</span>
                        <span v-else>Criar Conta</span>
                    </Button>
                </div>
            </Form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Button from "@/Components/ui/Button.vue";
import { CreditCard } from "lucide-vue-next";

const form = useForm({
    nome: "",
    banco: "",
    iban: "",
    swift_bic: "",
    saldo_inicial: 0,
    moeda: "EUR",
    tipo: "corrente",
    estado: "ativa",
    observacoes: "",
});

const submit = () => {
    form.post(route("bank-accounts.store"), {
        onSuccess: () => {
            // Redireciona para o index após sucesso
        },
    });
};
</script>
