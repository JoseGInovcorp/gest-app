<template>
    <Head title="Editar Movimento" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
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
                        Editar Movimento
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Atualizar débito ou crédito na conta corrente
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
                        :href="route('client-accounts.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Conta Corrente Clientes
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Editar Movimento</li>
            </ol>
        </nav>

        <!-- Form Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <form @submit.prevent="submit" class="p-6">
                <!-- Informações Básicas -->
                <div class="mb-8">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-white mb-4"
                    >
                        Informações do Movimento
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cliente -->
                        <div>
                            <label
                                for="entity_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Cliente
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="entity_id"
                                v-model="form.entity_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="">Selecione um cliente</option>
                                <option
                                    v-for="entity in entities"
                                    :key="entity.id"
                                    :value="entity.id"
                                >
                                    {{ entity.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.entity_id"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.entity_id }}
                            </p>
                        </div>

                        <!-- Data do Movimento -->
                        <div>
                            <label
                                for="data_movimento"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Data do Movimento
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="data_movimento"
                                v-model="form.data_movimento"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <p
                                v-if="form.errors.data_movimento"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.data_movimento }}
                            </p>
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label
                                for="tipo"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Tipo
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="tipo"
                                v-model="form.tipo"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="debito">
                                    Débito (Cliente deve)
                                </option>
                                <option value="credito">
                                    Crédito (Cliente pagou)
                                </option>
                            </select>
                            <p
                                v-if="form.errors.tipo"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.tipo }}
                            </p>
                        </div>

                        <!-- Valor -->
                        <div>
                            <label
                                for="valor"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Valor (€)
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="valor"
                                v-model="form.valor"
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <p
                                v-if="form.errors.valor"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.valor }}
                            </p>
                        </div>

                        <!-- Categoria -->
                        <div>
                            <label
                                for="categoria"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Categoria
                                <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="categoria"
                                v-model="form.categoria"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                                <option value="fatura">Fatura</option>
                                <option value="pagamento">Pagamento</option>
                                <option value="nota_credito">
                                    Nota Crédito
                                </option>
                                <option value="nota_debito">Nota Débito</option>
                                <option value="juros">Juros</option>
                                <option value="ajuste">Ajuste</option>
                                <option value="outros">Outros</option>
                            </select>
                            <p
                                v-if="form.errors.categoria"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.categoria }}
                            </p>
                        </div>

                        <!-- Referência -->
                        <div>
                            <label
                                for="referencia"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Referência
                            </label>
                            <input
                                id="referencia"
                                v-model="form.referencia"
                                type="text"
                                placeholder="Nº da fatura, recibo, etc."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            />
                            <p
                                v-if="form.errors.referencia"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.referencia }}
                            </p>
                        </div>

                        <!-- Descrição -->
                        <div class="md:col-span-2">
                            <label
                                for="descricao"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Descrição
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="descricao"
                                v-model="form.descricao"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            />
                            <p
                                v-if="form.errors.descricao"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.descricao }}
                            </p>
                        </div>

                        <!-- Observações -->
                        <div class="md:col-span-2">
                            <label
                                for="observacoes"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                            >
                                Observações
                            </label>
                            <textarea
                                id="observacoes"
                                v-model="form.observacoes"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                            <p
                                v-if="form.errors.observacoes"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.observacoes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div
                    class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700"
                >
                    <Link
                        :href="route('client-accounts.index')"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="form.processing">A guardar...</span>
                        <span v-else>Atualizar Movimento</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { DollarSign } from "lucide-vue-next";

const props = defineProps({
    movement: Object,
    entities: Array,
});

const form = useForm({
    entity_id: props.movement.entity_id,
    data_movimento: props.movement.data_movimento,
    tipo: props.movement.tipo,
    valor: props.movement.valor,
    descricao: props.movement.descricao,
    categoria: props.movement.categoria,
    referencia: props.movement.referencia,
    observacoes: props.movement.observacoes,
});

const submit = () => {
    form.patch(route("client-accounts.update", props.movement.id), {
        onSuccess: () => {
            // Redireciona para o index após sucesso
        },
    });
};
</script>
