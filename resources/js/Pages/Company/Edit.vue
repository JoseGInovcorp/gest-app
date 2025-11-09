<script setup>
import { ref, computed } from "vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Button from "@/Components/ui/Button.vue";
import { Building, Upload, Save, CheckCircle2 } from "lucide-vue-next";

const page = usePage();
const flashMessage = computed(() => page.props.flash?.success);

const props = defineProps({
    company: Object,
    can: {
        type: Object,
        default: () => ({
            update: false,
        }),
    },
});

const logoPreview = ref(
    props.company.logo ? `/storage/${props.company.logo}` : null
);

// Form data
const form = useForm({
    name: props.company.name || "",
    nif: props.company.nif || "",
    address: props.company.address || "",
    postal_code: props.company.postal_code || "",
    city: props.company.city || "",
    logo: null,
});

// Handle logo file selection
const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Handle form submission
const handleSubmit = () => {
    form.transform((data) => ({
        ...data,
        _method: "PATCH", // Necessário para Laravel aceitar PATCH via POST
    })).post(route("company.update"), {
        forceFormData: true, // Necessário para upload de ficheiros
        preserveScroll: true,
        onSuccess: () => {
            // Success message handled by backend
        },
    });
};
</script>

<template>
    <Head title="Configurações da Empresa" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <Building
                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Configurações da Empresa
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Personalize os dados que surgem na aplicação e
                        documentos
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
                    <span class="hover:text-gray-700 dark:hover:text-gray-200">
                        Configurações
                    </span>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Empresa</li>
            </ol>
        </nav>

        <!-- Success Message -->
        <div
            v-if="flashMessage"
            class="mb-6 rounded-lg border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/20 p-4"
        >
            <div class="flex items-center">
                <CheckCircle2
                    class="h-5 w-5 text-green-600 dark:text-green-400 mr-3"
                />
                <p
                    class="text-sm font-medium text-green-800 dark:text-green-300"
                >
                    {{ flashMessage }}
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div
            class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6"
        >
            <Form @submit.prevent="handleSubmit">
                <!-- Logo Upload Section -->
                <div class="mb-8">
                    <label
                        class="block text-sm font-medium text-gray-900 dark:text-white mb-4"
                    >
                        Logotipo da Empresa
                    </label>

                    <div class="flex items-start space-x-6">
                        <!-- Logo Preview -->
                        <div
                            class="flex-shrink-0 w-32 h-32 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-800"
                        >
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                alt="Logo Preview"
                                class="max-w-full max-h-full object-contain rounded-lg"
                            />
                            <Building v-else class="h-12 w-12 text-gray-400" />
                        </div>

                        <!-- Upload Button -->
                        <div class="flex-1">
                            <input
                                type="file"
                                id="logo"
                                accept="image/jpeg,image/jpg,image/png,image/gif"
                                @change="handleLogoChange"
                                class="hidden"
                                :disabled="!can.update"
                            />
                            <label
                                v-if="can.update"
                                for="logo"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg cursor-pointer transition-colors"
                            >
                                <Upload class="h-4 w-4 mr-2" />
                                Escolher Ficheiro
                            </label>
                            <p
                                class="mt-2 text-xs text-gray-600 dark:text-gray-400"
                            >
                                PNG, JPG, GIF até 2MB. Este logotipo aparece no
                                login, página inicial e documentos PDF.
                            </p>
                            <p
                                v-if="form.errors.logo"
                                class="mt-1 text-sm text-red-600 dark:text-red-400"
                            >
                                {{ form.errors.logo }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dados da Empresa -->
                <div class="space-y-6">
                    <!-- Nome -->
                    <FormField
                        id="name"
                        label="Nome da Empresa"
                        :error="form.errors.name"
                        description="Nome que aparece em documentos e cabeçalhos"
                    >
                        <Input
                            v-model="form.name"
                            id="name"
                            placeholder="Nome da sua empresa"
                            :disabled="!can.update"
                        />
                    </FormField>

                    <!-- NIF -->
                    <FormField
                        id="nif"
                        label="Número Contribuinte (NIF)"
                        :error="form.errors.nif"
                        description="9 dígitos sem espaços"
                    >
                        <Input
                            v-model="form.nif"
                            id="nif"
                            placeholder="123456789"
                            maxlength="9"
                            :disabled="!can.update"
                        />
                    </FormField>

                    <!-- Morada -->
                    <FormField
                        id="address"
                        label="Morada"
                        :error="form.errors.address"
                    >
                        <Input
                            v-model="form.address"
                            id="address"
                            placeholder="Rua, Nº, Andar"
                            :disabled="!can.update"
                        />
                    </FormField>

                    <!-- Código Postal e Localidade (Grid 2 colunas) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <FormField
                            id="postal_code"
                            label="Código Postal"
                            :error="form.errors.postal_code"
                        >
                            <Input
                                v-model="form.postal_code"
                                id="postal_code"
                                placeholder="1000-001"
                                maxlength="10"
                                :disabled="!can.update"
                            />
                        </FormField>

                        <FormField
                            id="city"
                            label="Localidade"
                            :error="form.errors.city"
                        >
                            <Input
                                v-model="form.city"
                                id="city"
                                placeholder="Lisboa"
                                :disabled="!can.update"
                            />
                        </FormField>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end">
                    <Button
                        v-if="can.update"
                        type="submit"
                        :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white"
                    >
                        <Save class="h-4 w-4 mr-2" />
                        {{
                            form.processing
                                ? "A guardar..."
                                : "Guardar Alterações"
                        }}
                    </Button>
                    <p v-else class="text-sm text-gray-600 dark:text-gray-400">
                        Não tem permissão para editar dados da empresa
                    </p>
                </div>
            </Form>
        </div>

        <!-- Info Box -->
        <div
            class="mt-6 rounded-lg border border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-900/20 p-4"
        >
            <h3
                class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2"
            >
                ℹ️ Onde são utilizados estes dados?
            </h3>
            <ul
                class="text-sm text-blue-800 dark:text-blue-400 space-y-1 list-disc list-inside"
            >
                <li>
                    <strong>Logotipo:</strong> Página de login, cabeçalho da
                    aplicação e documentos PDF
                </li>
                <li>
                    <strong>Nome e NIF:</strong> Faturas, propostas, orçamentos
                    e outros documentos
                </li>
                <li>
                    <strong>Morada completa:</strong> Rodapé de documentos
                    oficiais e relatórios
                </li>
            </ul>
        </div>
    </AuthenticatedLayout>
</template>
