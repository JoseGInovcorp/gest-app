<template>
    <Head title="Editar Proposta" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <FileText
                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Editar Proposta
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ proposal.numero }}
                        </p>
                    </div>
                </div>

                <!-- Botões de Ação do Header -->
                <div class="flex gap-3">
                    <!-- Botão Download PDF -->
                    <a
                        :href="route('proposals.pdf', proposal.id)"
                        target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition duration-150"
                    >
                        <FileText class="h-4 w-4" />
                        <span>Download PDF</span>
                    </a>

                    <!-- Botão Converter (só aparece se fechado) -->
                    <Button
                        v-if="form.estado === 'fechado'"
                        @click="convertToOrder"
                        :disabled="converting"
                        variant="success"
                    >
                        <Truck class="h-4 w-4 mr-2" />
                        <span v-if="converting">Convertendo...</span>
                        <span v-else>Converter para Encomenda</span>
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
                        :href="route('proposals.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Propostas
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
                            name="numero"
                            label="Número"
                            required
                        >
                            <Input
                                v-bind="field"
                                v-model="form.numero"
                                readonly
                                class="bg-gray-50 dark:bg-gray-900"
                            />
                            <p
                                v-if="form.errors.numero"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.numero }}
                            </p>
                        </FormField>

                        <!-- Cliente -->
                        <FormField
                            v-slot="{ field }"
                            name="entity_id"
                            label="Cliente"
                            required
                        >
                            <Select v-bind="field" v-model="form.entity_id">
                                <option value="">Selecione um cliente</option>
                                <option
                                    v-for="customer in clients"
                                    :key="customer.id"
                                    :value="customer.id"
                                >
                                    {{ customer.name }}
                                </option>
                            </Select>
                            <p
                                v-if="form.errors.entity_id"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.entity_id }}
                            </p>
                        </FormField>

                        <!-- Data da Proposta -->
                        <FormField
                            v-slot="{ field }"
                            name="data_proposta"
                            label="Data da Proposta"
                        >
                            <Input
                                v-bind="field"
                                v-model="form.data_proposta"
                                type="date"
                            />
                            <p
                                v-if="form.errors.data_proposta"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.data_proposta }}
                            </p>
                        </FormField>

                        <!-- Validade -->
                        <FormField
                            v-slot="{ field }"
                            name="validade"
                            label="Validade"
                        >
                            <Input
                                v-bind="field"
                                v-model="form.validade"
                                type="date"
                            />
                            <p
                                v-if="form.errors.validade"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.validade }}
                            </p>
                        </FormField>

                        <!-- Estado -->
                        <FormField
                            v-slot="{ field }"
                            name="estado"
                            label="Estado"
                            required
                        >
                            <Select v-bind="field" v-model="form.estado">
                                <option value="rascunho">Rascunho</option>
                                <option value="fechado">Fechado</option>
                            </Select>
                            <p
                                v-if="form.errors.estado"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.estado }}
                            </p>
                        </FormField>

                        <!-- Observações -->
                        <div class="md:col-span-2">
                            <FormField
                                v-slot="{ field }"
                                name="observacoes"
                                label="Observações"
                            >
                                <Textarea
                                    v-bind="field"
                                    v-model="form.observacoes"
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
                        v-if="form.errors.lines"
                        class="mb-4 text-sm text-red-600 dark:text-red-400"
                    >
                        {{ form.errors.lines }}
                    </p>

                    <div class="space-y-4">
                        <div
                            v-for="(item, index) in form.lines"
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
                                        :name="'lines.' + index + '.article_id'"
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
                                                    `lines.${index}.article_id`
                                                ]
                                            "
                                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{
                                                form.errors[
                                                    `lines.${index}.article_id`
                                                ]
                                            }}
                                        </p>
                                    </FormField>
                                </div>

                                <!-- Fornecedor -->
                                <div>
                                    <FormField
                                        v-slot="{ field }"
                                        :name="'lines.' + index + '.entity_id'"
                                        label="Fornecedor"
                                    >
                                        <Select
                                            v-bind="field"
                                            v-model="item.entity_id"
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
                                        :name="'lines.' + index + '.quantidade'"
                                        label="Quantidade"
                                        required
                                    >
                                        <Input
                                            v-bind="field"
                                            v-model.number="item.quantidade"
                                            @input="calculateItemTotal(index)"
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                        />
                                        <p
                                            v-if="
                                                form.errors[
                                                    `lines.${index}.quantidade`
                                                ]
                                            "
                                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{
                                                form.errors[
                                                    `lines.${index}.quantidade`
                                                ]
                                            }}
                                        </p>
                                    </FormField>
                                </div>

                                <!-- Preço Unitário -->
                                <div>
                                    <FormField
                                        v-slot="{ field }"
                                        :name="
                                            'lines.' + index + '.preco_custo'
                                        "
                                        label="Preço Unit."
                                        required
                                    >
                                        <Input
                                            v-bind="field"
                                            v-model.number="item.preco_custo"
                                            @input="calculateItemTotal(index)"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                        />
                                        <p
                                            v-if="
                                                form.errors[
                                                    `lines.${index}.preco_custo`
                                                ]
                                            "
                                            class="mt-1 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {{
                                                form.errors[
                                                    `lines.${index}.preco_custo`
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
                                                item.quantidade *
                                                    item.preco_custo
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="form.lines.length === 0"
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
                        v-if="form.lines.length > 0"
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
                    :href="route('proposals.index')"
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
                    <span v-else>Atualizar Proposta</span>
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
import { FileText, Plus, Trash2, Package, Truck } from "lucide-vue-next";

const props = defineProps({
    proposal: Object,
    clients: Array,
    articles: Array,
    suppliers: Array,
});

const converting = ref(false);

const form = useForm({
    numero: props.proposal.numero,
    data_proposta: props.proposal.data_proposta,
    validade: props.proposal.validade,
    entity_id: props.proposal.entity_id,
    estado: props.proposal.estado,
    observacoes: props.proposal.observacoes,
    lines: props.proposal.lines || [],
});

const addItem = () => {
    form.lines.push({
        article_id: "",
        entity_id: "",
        quantidade: 1,
        preco_custo: 0,
    });
};

const removeItem = (index) => {
    form.lines.splice(index, 1);
};

const updateArticlePrice = (index) => {
    const item = form.lines[index];
    const article = props.articles.find((a) => a.id === item.article_id);
    if (article) {
        item.preco_custo = article.sale_price || 0;
    }
};

const calculateItemTotal = (index) => {
    // Reactivity will handle this automatically
};

const convertToOrder = () => {
    if (
        confirm(
            "Deseja converter esta proposta em encomenda de cliente? A encomenda será criada no estado Rascunho."
        )
    ) {
        converting.value = true;
        router.post(
            route("proposals.convert-to-order", props.proposal.id),
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
    return form.lines.reduce((sum, item) => {
        return sum + item.quantidade * item.preco_custo;
    }, 0);
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("pt-PT", {
        style: "currency",
        currency: "EUR",
    }).format(value || 0);
};

const submit = () => {
    form.patch(route("proposals.update", props.proposal.id));
};
</script>
