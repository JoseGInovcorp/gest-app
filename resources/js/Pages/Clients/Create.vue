<script setup>
import { ref, computed, watch } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    countries: Array,
    nextNumber: Number,
});

// Form data
const form = useForm({
    type: "client", // Pre-selecionado para clientes
    number: props.nextNumber || "",
    nif: "",
    name: "",
    address: "",
    postal_code: "",
    city: "",
    country: "PT", // Portugal por defeito
    phone: "",
    mobile: "",
    website: "",
    email: "",
    gdpr_consent: false,
    observations: "",
    active: true,
});

// Estados para validação NIF
const nifValidation = ref({
    checking: false,
    exists: false,
    message: "",
    error: "",
});

// Debounce timeout para validação NIF
let nifValidationTimeout = null;

// Estados para VIES lookup
const viesLookup = ref({
    loading: false,
    error: "",
    success: false,
});

// Países que suportam VIES - agora dinâmico da base de dados
const viesCountries = computed(() => {
    return (
        props.countries
            ?.filter((country) => country.vies_enabled)
            .map((country) => country.code) || []
    );
});

// Computed properties
const isFormValid = computed(() => {
    const basicValid = !!(form.nif && form.name && form.address && form.city);
    const nifValid =
        !nifValidation.value.exists && !nifValidation.value.checking;
    const valid = basicValid && nifValid;

    return valid;
});

// Methods
const handleSubmit = () => {
    form.post(route("clients.store"), {
        onSuccess: (response) => {
            // Redireciona para a lista após sucesso
        },
        onError: (errors) => {
            // Erros serão mostrados automaticamente nos campos
        },
    });
};

const formatPostalCode = (event) => {
    let value = event.target.value.replace(/\D/g, ""); // Remove não números
    if (value.length >= 4) {
        value = value.substring(0, 4) + "-" + value.substring(4, 7);
    }
    form.postal_code = value;
};

const validateNIF = async () => {
    if (!form.nif || form.nif.length < 9) {
        nifValidation.value = {
            checking: false,
            exists: false,
            message: "",
            error: "",
        };
        return;
    }

    nifValidation.value.checking = true;
    nifValidation.value.error = "";

    try {
        const response = await axios.get(`/api/entities/check-nif/${form.nif}`);

        nifValidation.value = {
            checking: false,
            exists: response.data.exists,
            message: response.data.message,
            error: "",
        };

        // Se NIF é válido e não existe, verificar VIES para países UE
        if (
            !response.data.exists &&
            viesCountries.value.includes(form.country)
        ) {
            await performViesLookup();
        }
    } catch (error) {
        console.error("Erro ao validar NIF:", error);
        nifValidation.value = {
            checking: false,
            exists: false,
            message: "",
            error: "Erro ao verificar NIF",
        };
    }
};

const performViesLookup = async () => {
    if (!form.nif || !viesCountries.value.includes(form.country)) {
        return;
    }

    viesLookup.value.loading = true;
    viesLookup.value.error = "";
    viesLookup.value.success = false;

    try {
        const response = await axios.get(
            `/api/entities/vies-lookup/${form.country}/${form.nif}`
        );

        if (response.data.success && response.data.valid) {
            // Preencher campos automaticamente
            if (response.data.data.name && !form.name) {
                form.name = response.data.data.name;
            }
            if (response.data.data.address && !form.address) {
                form.address = response.data.data.address;
            }

            viesLookup.value.success = true;
        } else {
            viesLookup.value.error =
                response.data.message || "Erro na consulta VIES";
        }
    } catch (error) {
        console.error("Erro VIES lookup:", error);
        viesLookup.value.error = "Erro ao consultar dados VIES";
    } finally {
        viesLookup.value.loading = false;
    }
};

const debouncedValidateNIF = () => {
    if (nifValidationTimeout) {
        clearTimeout(nifValidationTimeout);
    }
    nifValidationTimeout = setTimeout(() => {
        validateNIF();
    }, 800); // 800ms delay
};

// Watcher para mudanças de país - re-executar VIES se necessário
watch(
    () => form.country,
    async (newCountry, oldCountry) => {
        if (
            newCountry !== oldCountry &&
            form.nif &&
            viesCountries.value.includes(newCountry)
        ) {
            // Reset VIES state
            viesLookup.value = { loading: false, error: "", success: false };
            // Re-executar validação que inclui VIES
            await validateNIF();
        }
    }
);
</script>

<template>
    <Head title="Novo Cliente" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10 dark:bg-blue-400/10"
                        >
                            <Users
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Novo Cliente
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Criar nova entidade do tipo cliente
                            </p>
                        </div>
                    </div>

                    <Button
                        variant="outline"
                        @click="$inertia.visit(route('clients.index'))"
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
                        <!-- Tipo -->
                        <FormField id="type" label="Tipo" required>
                            <Select v-model="form.type" id="type" required>
                                <option value="client">Cliente</option>
                                <option value="supplier">Fornecedor</option>
                                <option value="both">
                                    Cliente e Fornecedor
                                </option>
                            </Select>
                        </FormField>

                        <!-- Número -->
                        <FormField id="number" label="Número" required>
                            <Input
                                v-model="form.number"
                                id="number"
                                type="number"
                                readonly
                                class="bg-gray-50 dark:bg-gray-800"
                                placeholder="Gerado automaticamente"
                            />
                        </FormField>

                        <!-- NIF -->
                        <FormField
                            id="nif"
                            label="NIF"
                            required
                            :error="
                                form.errors.nif ||
                                nifValidation.error ||
                                (nifValidation.exists
                                    ? 'NIF já existe na base de dados'
                                    : '')
                            "
                            :description="
                                nifValidation.checking || viesLookup.loading
                                    ? 'A verificar NIF...'
                                    : viesLookup.success
                                    ? '✅ Dados preenchidos via VIES'
                                    : viesLookup.error
                                    ? `⚠️ ${viesLookup.error}`
                                    : nifValidation.exists
                                    ? 'Este NIF já está registado'
                                    : nifValidation.message ||
                                      'Número de Identificação Fiscal'
                            "
                        >
                            <Input
                                v-model="form.nif"
                                id="nif"
                                @input="debouncedValidateNIF"
                                @blur="validateNIF"
                                placeholder="123456789"
                                maxlength="20"
                                required
                                :class="[
                                    nifValidation.exists
                                        ? 'border-red-500'
                                        : nifValidation.message &&
                                          !nifValidation.exists
                                        ? 'border-green-500'
                                        : '',
                                ]"
                            />
                        </FormField>

                        <!-- Nome -->
                        <FormField
                            id="name"
                            label="Nome"
                            required
                            :error="form.errors.name"
                        >
                            <Input
                                v-model="form.name"
                                id="name"
                                placeholder="Nome da empresa ou pessoa"
                                required
                            />
                        </FormField>

                        <!-- Morada -->
                        <FormField
                            id="address"
                            label="Morada"
                            required
                            :error="form.errors.address"
                        >
                            <Input
                                v-model="form.address"
                                id="address"
                                placeholder="Rua, número, andar"
                                required
                            />
                        </FormField>

                        <!-- Código Postal -->
                        <FormField
                            id="postal_code"
                            label="Código Postal"
                            :error="form.errors.postal_code"
                            description="Formato: XXXX-XXX"
                        >
                            <Input
                                v-model="form.postal_code"
                                id="postal_code"
                                @input="formatPostalCode"
                                placeholder="1000-001"
                                maxlength="8"
                            />
                        </FormField>

                        <!-- Localidade -->
                        <FormField
                            id="city"
                            label="Localidade"
                            required
                            :error="form.errors.city"
                        >
                            <Input
                                v-model="form.city"
                                id="city"
                                placeholder="Lisboa"
                                required
                            />
                        </FormField>

                        <!-- País -->
                        <FormField
                            id="country"
                            label="País"
                            :error="form.errors.country"
                        >
                            <Select v-model="form.country" id="country">
                                <option
                                    v-for="country in countries"
                                    :key="country.code"
                                    :value="country.code"
                                >
                                    {{ country.name }}
                                </option>
                            </Select>
                        </FormField>

                        <!-- Telefone -->
                        <FormField
                            id="phone"
                            label="Telefone"
                            :error="form.errors.phone"
                        >
                            <Input
                                v-model="form.phone"
                                id="phone"
                                type="tel"
                                placeholder="+351 211 000 000"
                            />
                        </FormField>

                        <!-- Telemóvel -->
                        <FormField
                            id="mobile"
                            label="Telemóvel"
                            :error="form.errors.mobile"
                        >
                            <Input
                                v-model="form.mobile"
                                id="mobile"
                                type="tel"
                                placeholder="+351 911 000 000"
                            />
                        </FormField>

                        <!-- Website -->
                        <FormField
                            id="website"
                            label="Website"
                            :error="form.errors.website"
                        >
                            <Input
                                v-model="form.website"
                                id="website"
                                type="url"
                                placeholder="https://exemplo.com"
                            />
                        </FormField>

                        <!-- Email -->
                        <FormField
                            id="email"
                            label="Email"
                            :error="form.errors.email"
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
                        <FormField
                            id="gdpr_consent"
                            label=""
                            :error="form.errors.gdpr_consent"
                        >
                            <Checkbox
                                :id="'gdpr_consent'"
                                v-model:checked="form.gdpr_consent"
                                label="Consentimento RGPD - Aceita o tratamento de dados pessoais"
                            />
                        </FormField>

                        <!-- Observações -->
                        <FormField
                            id="observations"
                            label="Observações"
                            :error="form.errors.observations"
                            description="Notas internas sobre a entidade"
                        >
                            <Textarea
                                v-model="form.observations"
                                id="observations"
                                rows="3"
                                placeholder="Observações internas..."
                            />
                        </FormField>

                        <!-- Estado -->
                        <FormField id="active" label="">
                            <Checkbox
                                :id="'active'"
                                v-model:checked="form.active"
                                label="Entidade ativa"
                            />
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
                                        <li v-if="!form.nif">NIF</li>
                                        <li v-if="!form.name">Nome</li>
                                        <li v-if="!form.address">Morada</li>
                                        <li v-if="!form.city">Localidade</li>
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
                            @click="$inertia.visit(route('clients.index'))"
                        >
                            Cancelar
                        </Button>

                        <Button
                            type="submit"
                            :disabled="form.processing || !isFormValid"
                            :class="[
                                'flex items-center space-x-2',
                                !isFormValid
                                    ? 'opacity-50 cursor-not-allowed'
                                    : '',
                            ]"
                        >
                            <span v-if="form.processing">A criar...</span>
                            <span v-else-if="!isFormValid"
                                >Preencha os campos obrigatórios</span
                            >
                            <span v-else>Criar Cliente</span>
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
