<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { FileText } from "lucide-vue-next";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Button from "@/Components/ui/Button.vue";

const props = defineProps({
    suppliers: Array,
    supplierOrders: Array,
});

const form = useForm({
    data_fatura: new Date().toISOString().split("T")[0],
    data_vencimento: "",
    supplier_id: "",
    supplier_order_id: "",
    valor_total: "",
    documento: null,
    estado: "pendente",
});

// Filtrar encomendas por fornecedor selecionado
const filteredOrders = computed(() => {
    if (!form.supplier_id) return [];
    return props.supplierOrders.filter(
        (order) => order.supplier_id == form.supplier_id
    );
});

watch(
    () => form.supplier_id,
    () => {
        // Limpar encomenda selecionada se fornecedor mudar
        form.supplier_order_id = "";
    }
);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    console.log("Ficheiro selecionado:", {
        name: file?.name,
        size: file?.size,
        type: file?.type,
    });
    form.documento = file;
};

const submit = () => {
    console.log("Submetendo formulário:", {
        has_documento: !!form.documento,
        documento_name: form.documento?.name,
        form_data: {
            data_fatura: form.data_fatura,
            data_vencimento: form.data_vencimento,
            supplier_id: form.supplier_id,
            supplier_order_id: form.supplier_order_id,
            valor_total: form.valor_total,
            estado: form.estado,
        },
    });

    form.post(route("supplier-invoices.store"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: (response) => {
            console.log("Sucesso:", response);
        },
        onError: (errors) => {
            console.error("Erros de validação:", errors);
        },
        onFinish: () => {
            console.log("Request finalizado");
        },
    });
};
</script>

<template>
    <Head title="Nova Fatura de Fornecedor" />

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
                        Nova Fatura de Fornecedor
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Registar nova fatura recebida de fornecedor
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
                <li class="text-gray-900 dark:text-white">Nova Fatura</li>
            </ol>
        </nav>

        <!-- Formulário -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <Form @submit.prevent="submit" class="p-6 space-y-6">
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

                    <!-- Encomenda Fornecedor -->
                    <FormField
                        id="supplier_order_id"
                        label="Encomenda Fornecedor (opcional)"
                        :error="form.errors.supplier_order_id"
                    >
                        <Select
                            v-model="form.supplier_order_id"
                            id="supplier_order_id"
                            :disabled="
                                !form.supplier_id || filteredOrders.length === 0
                            "
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
                            >
                                €
                            </span>
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

                <!-- Upload Documento -->
                <FormField
                    id="documento"
                    label="Documento da Fatura (PDF, JPG, PNG - máx 5MB)"
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
                        <span v-else>Criar Fatura</span>
                    </Button>
                </div>
            </Form>
        </div>
    </AuthenticatedLayout>
</template>
