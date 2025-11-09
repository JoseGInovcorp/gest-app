<template>
    <Head title="Nova Encomenda - Fornecedor" />
    <AuthenticatedLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Nova Encomenda - Fornecedor
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                O número será gerado automaticamente:
                <span
                    class="font-semibold text-green-600 dark:text-green-400"
                    >{{ nextNumber }}</span
                >
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <h3
                    class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                >
                    Informações Gerais
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Fornecedor *</label
                        >
                        <select
                            v-model="form.supplier_id"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Selecione um fornecedor</option>
                            <option
                                v-for="supplier in suppliers"
                                :key="supplier.id"
                                :value="supplier.id"
                            >
                                {{ supplier.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.supplier_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.supplier_id }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Data da Encomenda</label
                        >
                        <input
                            v-model="form.order_date"
                            type="date"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Data de Entrega</label
                        >
                        <input
                            v-model="form.delivery_date"
                            type="date"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Estado *</label
                        >
                        <select
                            v-model="form.status"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="draft">Rascunho</option>
                            <option value="sent">Enviado</option>
                            <option value="confirmed">Confirmado</option>
                            <option value="received">Recebido</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Notas</label
                        >
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        ></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white"
                    >
                        Artigos
                    </h3>
                    <button
                        type="button"
                        @click="addItem"
                        class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded"
                    >
                        + Adicionar Artigo
                    </button>
                </div>
                <div
                    v-for="(item, index) in form.items"
                    :key="index"
                    class="grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 dark:bg-gray-900 rounded"
                >
                    <div class="col-span-5">
                        <select
                            v-model="item.article_id"
                            @change="updateArticlePrice(index)"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">Artigo</option>
                            <option
                                v-for="article in articles"
                                :key="article.id"
                                :value="article.id"
                            >
                                {{ article.reference }} - {{ article.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <input
                            v-model.number="item.quantity"
                            type="number"
                            step="1"
                            min="1"
                            placeholder="Qtd"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div class="col-span-3">
                        <input
                            v-model.number="item.unit_price"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="Preço"
                            required
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                    </div>
                    <div class="col-span-1">
                        <button
                            type="button"
                            @click="removeItem(index)"
                            class="w-full px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded"
                        >
                            ×
                        </button>
                    </div>
                </div>
                <div
                    v-if="form.items.length > 0"
                    class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <p
                        class="text-right text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Total: {{ formatCurrency(totalValue) }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <Link
                    :href="route('supplier-orders.index')"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg"
                    >Cancelar</Link
                >
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold rounded-lg"
                >
                    <span v-if="form.processing">Guardando...</span>
                    <span v-else>Guardar Encomenda</span>
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    suppliers: Array,
    articles: Array,
    nextNumber: String,
});

const form = useForm({
    order_date: null,
    delivery_date: null,
    supplier_id: "",
    status: "draft",
    notes: "",
    items: [],
});

const addItem = () => {
    form.items.push({ article_id: "", quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const updateArticlePrice = (index) => {
    const item = form.items[index];
    const article = props.articles.find((a) => a.id === item.article_id);
    if (article) {
        item.unit_price = parseFloat(article.unit_price) || 0;
    }
};

const totalValue = computed(() => {
    return form.items.reduce(
        (sum, item) => sum + item.quantity * item.unit_price,
        0
    );
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value || 0);
};

const submit = () => {
    form.post(route("supplier-orders.store"));
};
</script>
