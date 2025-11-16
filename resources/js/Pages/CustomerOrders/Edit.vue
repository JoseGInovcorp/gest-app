<template>
    <Head title="Editar Encomenda" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <ShoppingCart
                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Editar Encomenda
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ order.number }}
                        </p>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="flex gap-3">
                    <!-- Botão Download PDF -->
                    <a
                        :href="route('customer-orders.pdf', order.id)"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition duration-150"
                    >
                        <FileText class="h-4 w-4" />
                        <span>Download PDF</span>
                    </a>

                    <!-- Botão Converter (só aparece se fechado) -->
                    <Button
                        v-if="form.status === 'closed'"
                        @click="convertToSupplierOrders"
                        :disabled="converting"
                        variant="success"
                    >
                        <Truck class="h-4 w-4 mr-2" />
                        <span v-if="converting">Convertendo...</span>
                        <span v-else>Converter para Encomendas Fornecedor</span>
                    </Button>
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
                        :href="route('customer-orders.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Encomendas
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Editar</li>
            </ol>
        </nav>

        <!-- Form -->
        <Form @submit.prevent="submit" class="space-y-6">
            <!-- Informações Gerais -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Informações Gerais
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Número (readonly) -->
                        <FormField
                            v-slot="{ field }"
                            name="number"
                            label="Número"
                            required
                        >
                            <Input
                                v-bind="field"
                                v-model="form.number"
                                readonly
                                class="bg-gray-50 dark:bg-gray-900"
                            />
                            <p
                                v-if="form.errors.number"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.number }}
                            </p>
                        </FormField>

                        <!-- Cliente -->
                        <FormField
                            v-slot="{ field }"
                            name="customer_id"
                            label="Cliente"
                            required
                        >
                            <Select v-bind="field" v-model="form.customer_id">
                                <option value="">Selecione um cliente</option>
                                <option
                                    v-for="customer in customers"
                                    :key="customer.id"
                                    :value="customer.id"
                                >
                                    {{ customer.name }}
                                </option>
                            </Select>
                            <p
                                v-if="form.errors.customer_id"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.customer_id }}
                            </p>
                        </FormField>

                        <!-- Data da Proposta -->
                        <FormField
                            v-slot="{ field }"
                            name="proposal_date"
                            label="Data da Proposta"
                        >
                            <Input
                                v-bind="field"
                                v-model="form.proposal_date"
                                type="date"
                            />
                            <p
                                v-if="form.errors.proposal_date"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.proposal_date }}
                            </p>
                        </FormField>

                        <!-- Validade -->
                        <FormField
                            v-slot="{ field }"
                            name="validity_date"
                            label="Validade"
                        >
                            <Input
                                v-bind="field"
                                v-model="form.validity_date"
                                type="date"
                            />
                            <p
                                v-if="form.errors.validity_date"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.validity_date }}
                            </p>
                        </FormField>

                        <!-- Estado -->
                        <FormField
                            v-slot="{ field }"
                            name="status"
                            label="Estado"
                            required
                        >
                            <Select v-bind="field" v-model="form.status">
                                <option value="draft">Rascunho</option>
                                <option value="closed">Fechado</option>
                            </Select>
                            <p
                                v-if="form.errors.status"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.status }}
                            </p>
                        </FormField>

                        <!-- Notas -->
                        <div class="md:col-span-2">
                            <FormField
                                v-slot="{ field }"
                                name="notes"
                                label="Notas"
                            >
                                <Textarea
                                    v-bind="field"
                                    v-model="form.notes"
                                    rows="3"
                                />
                            </FormField>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Artigos -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-white"
                        >
                            Artigos
                        </h3>
                        <Button
                            type="button"
                            @click="addItem"
                            variant="default"
                            size="sm"
                        >
                            <Plus class="h-4 w-4 mr-1" />
                            Adicionar Artigo
                        </Button>
                    </div>

                    <p
                        v-if="form.errors.items"
                        class="mb-4 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.items }}
                    </p>

                    <div class="space-y-4">
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg"
                        >
                            <div class="flex justify-between items-start mb-4">
                                <h4
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Artigo {{ index + 1 }}
                                </h4>
                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Artigo -->
                                <div class="md:col-span-2">
                                    <FormField
                                        v-slot="{ field }"
                                        :name="'items.' + index + '.article_id'"
                                        label="Artigo"
                                        required
                                    >
                                        <Select
                                            v-bind="field"
                                            v-model="item.article_id"
                                            @change="updateArticlePrice(index)"
                                        >
                                            <option value="">
                                                Selecione um artigo
                                            </option>
                                            <option
                                                v-for="article in articles"
                                                :key="article.id"
                                                :value="article.id"
                                            >
                                                {{ article.reference }} -
                                                {{ article.name }}
                                            </option>
                                        </Select>
                                        <p
                                            v-if="
                                                form.errors[
                                                    `items.${index}.article_id`
                                                ]
                                            "
                                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{
                                                form.errors[
                                                    `items.${index}.article_id`
                                                ]
                                            }}
                                        </p>
                                    </FormField>
                                </div>

                                <!-- Fornecedor -->
                                <div>
                                    <FormField
                                        v-slot="{ field }"
                                        :name="
                                            'items.' + index + '.supplier_id'
                                        "
                                        label="Fornecedor"
                                    >
                                        <Select
                                            v-bind="field"
                                            v-model="item.supplier_id"
                                        >
                                            <option value="">
                                                Sem fornecedor
                                            </option>
                                            <option
                                                v-for="supplier in suppliers"
                                                :key="supplier.id"
                                                :value="supplier.id"
                                            >
                                                {{ supplier.name }}
                                            </option>
                                        </Select>
                                    </FormField>
                                </div>

                                <!-- Quantidade -->
                                <div>
                                    <FormField
                                        v-slot="{ field }"
                                        :name="'items.' + index + '.quantity'"
                                        label="Quantidade"
                                        required
                                    >
                                        <Input
                                            v-bind="field"
                                            v-model.number="item.quantity"
                                            @input="calculateItemTotal(index)"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                        />
                                        <p
                                            v-if="
                                                form.errors[
                                                    `items.${index}.quantity`
                                                ]
                                            "
                                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{
                                                form.errors[
                                                    `items.${index}.quantity`
                                                ]
                                            }}
                                        </p>
                                    </FormField>
                                </div>

                                <!-- Preço Unitário -->
                                <div>
                                    <FormField
                                        v-slot="{ field }"
                                        :name="'items.' + index + '.unit_price'"
                                        label="Preço Unit."
                                        required
                                    >
                                        <Input
                                            v-bind="field"
                                            v-model.number="item.unit_price"
                                            @input="calculateItemTotal(index)"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                        />
                                        <p
                                            v-if="
                                                form.errors[
                                                    `items.${index}.unit_price`
                                                ]
                                            "
                                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{
                                                form.errors[
                                                    `items.${index}.unit_price`
                                                ]
                                            }}
                                        </p>
                                    </FormField>
                                </div>

                                <!-- Total da Linha -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Total
                                    </label>
                                    <div
                                        class="px-3 py-2 bg-gray-50 dark:bg-gray-900 rounded-md border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{
                                            formatCurrency(
                                                item.quantity * item.unit_price
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="form.items.length === 0"
                            class="text-center py-8 text-gray-500 dark:text-gray-400"
                        >
                            <Package
                                class="mx-auto h-12 w-12 mb-4 opacity-50"
                            />
                            <p>Nenhum artigo adicionado</p>
                            <p class="text-sm mt-1">
                                Clique em "Adicionar Artigo" para começar
                            </p>
                        </div>
                    </div>

                    <!-- Total Geral -->
                    <div
                        v-if="form.items.length > 0"
                        class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex justify-end">
                            <div class="text-right">
                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Total Geral
                                </p>
                                <p
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ formatCurrency(totalValue) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex justify-end gap-4">
                <Link
                    :href="route('customer-orders.index')"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-150"
                >
                    Cancelar
                </Link>
                <Button
                    type="submit"
                    :disabled="form.processing"
                    variant="default"
                >
                    <span v-if="form.processing">Guardando...</span>
                    <span v-else>Atualizar Encomenda</span>
                </Button>
            </div>
        </Form>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Button from "@/Components/ui/Button.vue";
import {
    ShoppingCart,
    Plus,
    Trash2,
    Package,
    Truck,
    FileText,
} from "lucide-vue-next";

const props = defineProps({
    order: Object,
    customers: Array,
    articles: Array,
    suppliers: Array,
});

const converting = ref(false);

const form = useForm({
    number: props.order.number,
    proposal_date: props.order.proposal_date,
    validity_date: props.order.validity_date,
    customer_id: props.order.customer_id,
    status: props.order.status,
    notes: props.order.notes,
    items: props.order.items || [],
});

const addItem = () => {
    form.items.push({
        article_id: "",
        supplier_id: "",
        quantity: 1,
        unit_price: 0,
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const updateArticlePrice = (index) => {
    const item = form.items[index];
    const article = props.articles.find((a) => a.id === item.article_id);
    if (article) {
        item.unit_price = article.sale_price || 0;
    }
};

const calculateItemTotal = (index) => {
    // Reactivity will handle this automatically
};

const convertToSupplierOrders = () => {
    if (
        confirm(
            "Deseja converter esta encomenda em encomendas de fornecedor? Serão criadas encomendas para cada fornecedor associado aos artigos."
        )
    ) {
        converting.value = true;
        router.post(
            route("customer-orders.convert", props.order.id),
            {},
            {
                onFinish: () => {
                    converting.value = false;
                },
            }
        );
    }
};

const totalValue = computed(() => {
    return form.items.reduce((sum, item) => {
        return sum + item.quantity * item.unit_price;
    }, 0);
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value || 0);
};

const submit = () => {
    form.patch(route("customer-orders.update", props.order.id));
};
</script>
