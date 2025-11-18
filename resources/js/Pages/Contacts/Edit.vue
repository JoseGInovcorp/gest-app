<template>
    <Head title="Editar Contacto" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-600/10 dark:bg-green-400/10"
                        >
                            <Users
                                class="h-6 w-6 text-green-600 dark:text-green-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Editar Contacto
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Criar um Editar Contacto associado a uma
                                entidade
                            </p>
                        </div>
                    </div>

                    <Button
                        variant="outline"
                        @click="router.visit(route('contacts.index'))"
                        class="flex items-center space-x-2"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        <span>Voltar à Lista</span>
                    </Button>
                </div>
            </div>

            <!-- Form -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6"
            >
                <Form @submit.prevent="handleSubmit">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Número -->
                        <FormField id="number" label="Número" required>
                            <Input
                                v-model="form.number"
                                id="number"
                                type="number"
                                disabled
                                class="bg-gray-50 dark:bg-gray-800"
                                :placeholder="`#${contact.number}`"
                            />
                        </FormField>

                        <!-- Entidade -->
                        <FormField
                            id="entity_id"
                            label="Entidade"
                            required
                            :error="errors.entity_id"
                            description="Selecione a entidade (cliente/fornecedor) associada"
                        >
                            <Select
                                v-model="form.entity_id"
                                id="entity_id"
                                required
                            >
                                <option value="">
                                    Selecione uma entidade...
                                </option>
                                <option
                                    v-for="entity in entities"
                                    :key="entity.id"
                                    :value="entity.id"
                                >
                                    {{ entity.name }} ({{
                                        getEntityTypeLabel(entity.type)
                                    }})
                                </option>
                            </Select>
                        </FormField>

                        <!-- Nome -->
                        <FormField
                            id="first_name"
                            label="Nome"
                            required
                            :error="errors.first_name"
                            description="Nome próprio do contacto"
                        >
                            <Input
                                v-model="form.first_name"
                                id="first_name"
                                type="text"
                                placeholder="Ex: João"
                                required
                            />
                        </FormField>

                        <!-- Apelido -->
                        <FormField
                            id="last_name"
                            label="Apelido"
                            required
                            :error="errors.last_name"
                            description="Apelido do contacto"
                        >
                            <Input
                                v-model="form.last_name"
                                id="last_name"
                                type="text"
                                placeholder="Ex: Silva"
                                required
                            />
                        </FormField>

                        <!-- Função -->
                        <FormField
                            id="function"
                            label="Função"
                            :error="errors.function"
                            description="Selecione a função do contacto na empresa"
                        >
                            <Select v-model="form.function" id="function">
                                <option value="">
                                    Selecione uma função...
                                </option>
                                <option
                                    v-for="func in functions"
                                    :key="func.id"
                                    :value="func.name"
                                >
                                    {{ func.name }}
                                </option>
                            </Select>
                        </FormField>

                        <!-- Telefone -->
                        <FormField
                            id="phone"
                            label="Telefone"
                            :error="errors.phone"
                            description="Telefone fixo"
                        >
                            <Input
                                v-model="form.phone"
                                id="phone"
                                type="tel"
                                placeholder="211 000 000"
                                @input="formatPhone('phone')"
                            />
                        </FormField>

                        <!-- Telemóvel -->
                        <FormField
                            id="mobile"
                            label="Telemóvel"
                            :error="errors.mobile"
                            description="Número de telemóvel"
                        >
                            <Input
                                v-model="form.mobile"
                                id="mobile"
                                type="tel"
                                placeholder="911 000 000"
                                @input="formatPhone('mobile')"
                            />
                        </FormField>

                        <!-- Email -->
                        <FormField
                            id="email"
                            label="Email"
                            :error="errors.email"
                            description="Endereço de email"
                        >
                            <Input
                                v-model="form.email"
                                id="email"
                                type="email"
                                placeholder="contacto@exemplo.com"
                            />
                        </FormField>
                    </div>

                    <!-- Campos de largura total -->
                    <div class="space-y-6">
                        <!-- Consentimento RGPD -->
                        <FormField id="rgpd_consent" label="">
                            <Checkbox
                                :id="'rgpd_consent'"
                                v-model:checked="form.rgpd_consent"
                                label="Consentimento RGPD - O contacto autorizou o tratamento dos seus dados pessoais"
                            />
                        </FormField>

                        <!-- Observações -->
                        <FormField
                            id="observations"
                            label="Observações"
                            :error="errors.observations"
                            description="Notas adicionais sobre o contacto"
                        >
                            <Textarea
                                v-model="form.observations"
                                id="observations"
                                rows="3"
                                placeholder="Observações ou notas relevantes..."
                            />
                        </FormField>

                        <!-- Estado -->
                        <FormField
                            id="status"
                            label="Estado"
                            required
                            :error="errors.status"
                            description="Estado inicial do contacto"
                        >
                            <Select v-model="form.status" id="status" required>
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                            </Select>
                        </FormField>
                    </div>

                    <!-- Validação visual -->
                    <div
                        v-if="!isFormValid"
                        class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-yellow-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3
                                    class="text-sm font-medium text-yellow-800 dark:text-yellow-200"
                                >
                                    Campos obrigatórios em falta:
                                </h3>
                                <div
                                    class="mt-2 text-sm text-yellow-700 dark:text-yellow-300"
                                >
                                    <ul class="list-disc list-inside space-y-1">
                                        <li v-if="!form.entity_id">Entidade</li>
                                        <li v-if="!form.first_name">Nome</li>
                                        <li v-if="!form.last_name">Apelido</li>
                                        <li v-if="!form.status">Estado</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div
                        class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-800"
                    >
                        <Button
                            variant="outline"
                            type="button"
                            @click="router.visit(route('contacts.index'))"
                        >
                            Cancelar
                        </Button>

                        <Button
                            type="submit"
                            :disabled="processing || !isFormValid"
                            :class="[
                                'flex items-center space-x-2 bg-green-600 hover:bg-green-700 focus:ring-green-500',
                                !isFormValid
                                    ? 'opacity-50 cursor-not-allowed'
                                    : '',
                            ]"
                        >
                            <Loader2
                                v-if="processing"
                                class="w-4 h-4 mr-2 animate-spin"
                            />
                            <span v-if="processing">A atualizar...</span>
                            <span v-else-if="!isFormValid"
                                >Preencha os campos obrigatórios</span
                            >
                            <span v-else>Atualizar Contacto</span>
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import { ref, computed, watchEffect } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, Loader2, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    contact: {
        type: Object,
        required: true,
    },
    entities: {
        type: Array,
        required: true,
    },
    functions: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

// Form data
const form = useForm({
    entity_id: props.contact.entity_id,
    first_name: props.contact.first_name,
    last_name: props.contact.last_name,
    function: props.contact.function || "",
    phone: props.contact.phone || "",
    mobile: props.contact.mobile || "",
    email: props.contact.email || "",
    rgpd_consent: props.contact.rgpd_consent,
    observations: props.contact.observations || "",
    status: props.contact.status,
    number: props.contact.number,
});

const processing = ref(false);

// Validação do formulário
const isFormValid = computed(() => {
    return (
        form.entity_id &&
        form.first_name?.trim() &&
        form.last_name?.trim() &&
        form.status
    );
});

// Formatação de telefone
const formatPhone = (field) => {
    const phone = form[field];
    if (!phone) return;

    // Remove tudo exceto números
    const cleaned = phone.replace(/\D/g, "");

    // Aplicar formato português (9 dígitos)
    if (cleaned.length <= 9) {
        const formatted = cleaned.replace(/(\d{3})(\d{3})(\d{3})/, "$1 $2 $3");
        form[field] = formatted.trim();
    }
};

// Obter label do tipo de entidade
const getEntityTypeLabel = (type) => {
    const labels = {
        client: "Cliente",
        supplier: "Fornecedor",
        both: "Cliente/Fornecedor",
    };
    return labels[type] || "N/D";
};

// Submit do formulário
const handleSubmit = () => {
    processing.value = true;

    form.patch(route("contacts.update", props.contact.id), {
        onSuccess: () => {
            // Redirecionamento é feito pelo controller
        },
        onError: () => {
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

// Auto-preencher número
watchEffect(() => {
    if (props.contact.number) {
        form.number = props.contact.number;
    }
});
</script>
