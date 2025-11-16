<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { FileText, Mail } from "lucide-vue-next";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Button from "@/Components/ui/Button.vue";

const props = defineProps({
    invoice: Object,
    suppliers: Array,
    supplierOrders: Array,
});

const form = useForm({
    data_fatura: props.invoice.data_fatura,
    data_vencimento: props.invoice.data_vencimento,
    supplier_id: props.invoice.supplier_id,
    supplier_order_id: props.invoice.supplier_order_id || "",
    valor_total: props.invoice.valor_total,
    documento: null,
    estado: props.invoice.estado,
});

// Controle do diálogo de comprovativo
const showPaymentProofDialog = ref(false);
const paymentProofFile = ref(null);
const sendingProof = ref(false);

// Estado original para detectar mudança para "paga"
const originalEstado = ref(props.invoice.estado);

// Filtrar encomendas por fornecedor selecionado
const filteredOrders = computed(() => {
    if (!form.supplier_id) return [];
    return props.supplierOrders.filter(
        (order) => order.supplier_id == form.supplier_id
    );
});

// Detectar mudança de estado para "paga"
watch(
    () => form.estado,
    (newEstado) => {
        if (originalEstado.value === "pendente" && newEstado === "paga") {
            showPaymentProofDialog.value = true;
        }
    }
);

const handleFileChange = (event) => {
    form.documento = event.target.files[0];
};

const handlePaymentProofChange = (event) => {
    paymentProofFile.value = event.target.files[0];
};

const submit = () => {
    form.post(route("supplier-invoices.update", props.invoice.id), {
        _method: "PATCH",
        preserveScroll: true,
        onSuccess: () => {
            // Atualizar estado original após salvar
            originalEstado.value = form.estado;
        },
    });
};

const sendPaymentProof = async () => {
    if (!paymentProofFile.value) {
        alert("Por favor, selecione o comprovativo de pagamento.");
        return;
    }

    sendingProof.value = true;

    const proofForm = new FormData();
    proofForm.append("comprovativo", paymentProofFile.value);

    try {
        await axios.post(
            route("supplier-invoices.send-payment-proof", props.invoice.id),
            proofForm,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        showPaymentProofDialog.value = false;
        paymentProofFile.value = null;

        // Recarregar a página para mostrar o comprovativo enviado
        window.location.reload();
    } catch (error) {
        alert(
            "Erro ao enviar comprovativo: " +
                (error.response?.data?.message || error.message)
        );
    } finally {
        sendingProof.value = false;
    }
};

const skipPaymentProof = () => {
    showPaymentProofDialog.value = false;
    paymentProofFile.value = null;
    submit();
};

const cancelEstadoChange = () => {
    form.estado = originalEstado.value;
    showPaymentProofDialog.value = false;
    paymentProofFile.value = null;
};
</script>

<template>
    <Head title="Editar Fatura de Fornecedor" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/20 rounded-lg">
                    <FileText class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Editar Fatura de Fornecedor
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Fatura {{ invoice.numero }}
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
                        :href="route('supplier-invoices.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Faturas de Fornecedores
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">
                    Editar {{ invoice.numero }}
                </li>
            </ol>
        </nav>

        <!-- Formulário -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <Form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Número da Fatura (readonly) -->
                <FormField id="numero" label="Número da Fatura">
                    <Input
                        :value="invoice.numero"
                        readonly
                        class="bg-gray-100 dark:bg-gray-900 cursor-not-allowed"
                    />
                </FormField>

                <!-- Linha 1: Datas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Data da Fatura -->
                    <FormField
                        id="data_fatura"
                        label="Data da Fatura"
                        required
                        :error="form.errors.data_fatura"
                    >
                        <Input
                            v-model="form.data_fatura"
                            type="date"
                            id="data_fatura"
                            required
                        />
                    </FormField>

                    <!-- Data de Vencimento -->
                    <FormField
                        id="data_vencimento"
                        label="Data de Vencimento"
                        required
                        :error="form.errors.data_vencimento"
                    >
                        <Input
                            v-model="form.data_vencimento"
                            type="date"
                            id="data_vencimento"
                            required
                        />
                    </FormField>
                </div>

                <!-- Linha 2: Fornecedor e Encomenda -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fornecedor -->
                    <FormField
                        id="supplier_id"
                        label="Fornecedor"
                        required
                        :error="form.errors.supplier_id"
                    >
                        <Select
                            v-model="form.supplier_id"
                            id="supplier_id"
                            required
                        >
                            <option value="">Selecione...</option>
                            <option
                                v-for="supplier in suppliers"
                                :key="supplier.id"
                                :value="supplier.id"
                            >
                                {{ supplier.name }}
                            </option>
                        </Select>
                    </FormField>

                    <!-- Encomenda Fornecedor (opcional) -->
                    <FormField
                        id="supplier_order_id"
                        label="Encomenda Fornecedor (opcional)"
                        :error="form.errors.supplier_order_id"
                    >
                        <Select
                            v-model="form.supplier_order_id"
                            id="supplier_order_id"
                            :disabled="!form.supplier_id"
                            class="disabled:bg-gray-100 dark:disabled:bg-gray-900"
                        >
                            <option value="">Nenhuma</option>
                            <option
                                v-for="order in filteredOrders"
                                :key="order.id"
                                :value="order.id"
                            >
                                {{ order.number }}
                            </option>
                        </Select>
                    </FormField>
                </div>

                <!-- Linha 3: Valor e Estado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Valor Total -->
                    <FormField
                        id="valor_total"
                        label="Valor Total"
                        required
                        :error="form.errors.valor_total"
                    >
                        <div class="relative">
                            <Input
                                v-model="form.valor_total"
                                type="number"
                                id="valor_total"
                                step="0.01"
                                min="0.01"
                                class="pl-8"
                                required
                            />
                            <span
                                class="absolute left-3 top-2.5 text-gray-500 dark:text-gray-400"
                                >€</span
                            >
                        </div>
                    </FormField>

                    <!-- Estado -->
                    <FormField
                        id="estado"
                        label="Estado"
                        required
                        :error="form.errors.estado"
                    >
                        <Select v-model="form.estado" id="estado" required>
                            <option value="pendente">
                                Pendente de Pagamento
                            </option>
                            <option value="paga">Paga</option>
                        </Select>
                    </FormField>
                </div>

                <!-- Documento Atual -->
                <div
                    v-if="invoice.documento"
                    class="bg-gray-50 dark:bg-gray-900 p-4 rounded-md"
                >
                    <p
                        class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Documento Atual:
                    </p>
                    <a
                        :href="`/storage/${invoice.documento}`"
                        target="_blank"
                        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                    >
                        <FileText class="h-4 w-4 mr-2" />
                        Ver documento
                    </a>
                </div>

                <!-- Upload Novo Documento -->
                <FormField
                    id="documento"
                    label="Atualizar Documento (PDF, JPG, PNG - máx 5MB)"
                    :error="form.errors.documento"
                >
                    <input
                        type="file"
                        id="documento"
                        @change="handleFileChange"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                    />
                </FormField>

                <!-- Comprovativo Atual (se existir) -->
                <div
                    v-if="invoice.comprovativo_pagamento"
                    class="bg-green-50 dark:bg-green-900/20 p-4 rounded-md"
                >
                    <p
                        class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                    >
                        Comprovativo de Pagamento:
                    </p>
                    <a
                        :href="`/storage/${invoice.comprovativo_pagamento}`"
                        target="_blank"
                        class="inline-flex items-center text-sm text-green-600 hover:text-green-800 dark:text-green-400"
                    >
                        <FileText class="h-4 w-4 mr-2" />
                        Ver comprovativo enviado
                    </a>
                </div>

                <!-- Botões -->
                <div
                    class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700"
                >
                    <Link
                        :href="route('supplier-invoices.index')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors"
                    >
                        Cancelar
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
                    >
                        <span v-if="form.processing">A guardar...</span>
                        <span v-else>Atualizar Fatura</span>
                    </Button>
                </div>
            </Form>
        </div>

        <!-- Modal de Comprovativo de Pagamento -->
        <div
            v-if="showPaymentProofDialog"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4"
            >
                <!-- Header -->
                <div
                    class="flex items-center space-x-3 p-6 border-b border-gray-200 dark:border-gray-700"
                >
                    <Mail class="h-5 w-5 text-blue-600" />
                    <h3
                        class="text-lg font-semibold text-gray-900 dark:text-white"
                    >
                        Enviar Comprovativo ao Fornecedor?
                    </h3>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        A fatura foi marcada como paga. Pretende enviar o
                        comprovativo de pagamento ao fornecedor por e-mail?
                    </p>

                    <div>
                        <label
                            for="comprovativo"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Comprovativo de Pagamento (PDF, JPG, PNG - máx 5MB)
                            <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="file"
                            id="comprovativo"
                            @change="handlePaymentProofChange"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                        />
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        @click="cancelEstadoChange"
                        :disabled="sendingProof"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="skipPaymentProof"
                        :disabled="sendingProof"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Não Enviar
                    </button>
                    <button
                        @click="sendPaymentProof"
                        :disabled="sendingProof || !paymentProofFile"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="sendingProof">Enviando...</span>
                        <span v-else>Enviar</span>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
