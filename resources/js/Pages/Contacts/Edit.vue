<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Users class="h-8 w-8 text-green-600" />
                <div>
                    <h2
                        class="text-xl font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Editar Contacto
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Atualizar dados do contacto
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                            >
                                <Home class="w-4 h-4 mr-2" />
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    class="w-4 h-4 text-gray-400 mx-1"
                                />
                                <Link
                                    :href="route('contacts.index')"
                                    class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white"
                                >
                                    Contactos
                                </Link>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <ChevronRight
                                    class="w-4 h-4 text-gray-400 mx-1"
                                />
                                <span
                                    class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400"
                                >
                                    Editar Contacto
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Form -->
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <Form @submit="handleSubmit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Número -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Número"
                                        description="Número sequencial gerado automaticamente"
                                    >
                                        <Input
                                            v-model="form.number"
                                            type="number"
                                            disabled
                                            :placeholder="`#${contact.number}`"
                                            class="bg-gray-50 dark:bg-gray-900"
                                        />
                                    </FormField>
                                </div>

                                <!-- Entidade -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Entidade *"
                                        description="Selecione a entidade (cliente/fornecedor) associada"
                                        :error="errors.entity_id"
                                    >
                                        <Select
                                            v-model="form.entity_id"
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
                                                    getEntityTypeLabel(
                                                        entity.type
                                                    )
                                                }})
                                            </option>
                                        </Select>
                                    </FormField>
                                </div>

                                <!-- Nome -->
                                <div>
                                    <FormField
                                        label="Nome *"
                                        description="Nome próprio do contacto"
                                        :error="errors.first_name"
                                    >
                                        <Input
                                            v-model="form.first_name"
                                            type="text"
                                            placeholder="Ex: João"
                                            required
                                        />
                                    </FormField>
                                </div>

                                <!-- Apelido -->
                                <div>
                                    <FormField
                                        label="Apelido *"
                                        description="Apelido do contacto"
                                        :error="errors.last_name"
                                    >
                                        <Input
                                            v-model="form.last_name"
                                            type="text"
                                            placeholder="Ex: Silva"
                                            required
                                        />
                                    </FormField>
                                </div>

                                <!-- Função -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Função"
                                        description="Função na empresa (ex: Diretor Comercial, Gerente, etc.)"
                                        :error="errors.function"
                                    >
                                        <Input
                                            v-model="form.function"
                                            type="text"
                                            placeholder="Ex: Diretor Comercial"
                                        />
                                    </FormField>
                                </div>

                                <!-- Telefone -->
                                <div>
                                    <FormField
                                        label="Telefone"
                                        description="Telefone fixo"
                                        :error="errors.phone"
                                    >
                                        <Input
                                            v-model="form.phone"
                                            type="tel"
                                            placeholder="211 000 000"
                                            @input="formatPhone('phone')"
                                        />
                                    </FormField>
                                </div>

                                <!-- Telemóvel -->
                                <div>
                                    <FormField
                                        label="Telemóvel"
                                        description="Número de telemóvel"
                                        :error="errors.mobile"
                                    >
                                        <Input
                                            v-model="form.mobile"
                                            type="tel"
                                            placeholder="911 000 000"
                                            @input="formatPhone('mobile')"
                                        />
                                    </FormField>
                                </div>

                                <!-- Email -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Email"
                                        description="Endereço de email"
                                        :error="errors.email"
                                    >
                                        <Input
                                            v-model="form.email"
                                            type="email"
                                            placeholder="contacto@exemplo.com"
                                        />
                                    </FormField>
                                </div>

                                <!-- Consentimento RGPD -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Consentimento RGPD"
                                        description="O contacto deu consentimento para tratamento de dados pessoais?"
                                    >
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <Checkbox
                                                v-model="form.rgpd_consent"
                                                :checked="form.rgpd_consent"
                                            />
                                            <label class="text-sm">
                                                Sim, o contacto autorizou o
                                                tratamento dos seus dados
                                                pessoais
                                            </label>
                                        </div>
                                    </FormField>
                                </div>

                                <!-- Observações -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Observações"
                                        description="Notas adicionais sobre o contacto"
                                        :error="errors.observations"
                                    >
                                        <Textarea
                                            v-model="form.observations"
                                            placeholder="Observações ou notas relevantes..."
                                            rows="4"
                                        />
                                    </FormField>
                                </div>

                                <!-- Estado -->
                                <div class="md:col-span-2">
                                    <FormField
                                        label="Estado *"
                                        description="Estado inicial do contacto"
                                        :error="errors.status"
                                    >
                                        <Select v-model="form.status" required>
                                            <option value="active">
                                                Ativo
                                            </option>
                                            <option value="inactive">
                                                Inativo
                                            </option>
                                        </Select>
                                    </FormField>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div
                                class="flex items-center justify-end mt-8 space-x-3"
                            >
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="
                                        router.visit(route('contacts.index'))
                                    "
                                    :disabled="processing"
                                >
                                    Cancelar
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="processing || !isFormValid"
                                >
                                    <Loader2
                                        v-if="processing"
                                        class="w-4 h-4 mr-2 animate-spin"
                                    />
                                    {{
                                        processing
                                            ? "A criar..."
                                            : "Criar Contacto"
                                    }}
                                </Button>
                            </div>

                            <!-- Mensagem validação -->
                            <div
                                v-if="!isFormValid"
                                class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-md"
                            >
                                <p
                                    class="text-sm text-amber-700 dark:text-amber-200"
                                >
                                    ℹ️ Preencha os campos obrigatórios para
                                    continuar.
                                </p>
                            </div>
                        </Form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watchEffect } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, Home, ChevronRight, Loader2 } from "lucide-vue-next";

const props = defineProps({
    contact: {
        type: Object,
        required: true,
    },
    entities: {
        type: Array,
        required: true,
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
</script>
