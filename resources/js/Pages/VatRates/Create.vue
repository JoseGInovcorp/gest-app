<script setup>
import { computed } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import Button from "@/Components/ui/Button.vue";
import { Percent, ArrowLeft } from "lucide-vue-next";

// Form data
const form = useForm({
    name: "",
    rate: "",
    is_default: false,
    active: true,
});

// Computed properties
const isFormValid = computed(() => {
    return (
        form.name.trim() !== "" &&
        form.rate !== "" &&
        form.rate >= 0 &&
        form.rate <= 100
    );
});

// Methods
const handleSubmit = () => {
    form.post(route("vat-rates.store"));
};
</script>

<template>
    <Head title="Nova Taxa de IVA" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10 dark:bg-blue-400/10"
                        >
                            <Percent
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Nova Taxa de IVA
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Criar nova taxa de IVA no sistema
                            </p>
                        </div>
                    </div>

                    <Button
                        variant="outline"
                        @click="$inertia.visit(route('vat-rates.index'))"
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
                        <!-- Nome -->
                        <FormField
                            id="name"
                            label="Nome"
                            required
                            :error="form.errors.name"
                            description="Ex: IVA Normal, IVA Reduzido"
                        >
                            <Input
                                v-model="form.name"
                                id="name"
                                placeholder="IVA Normal"
                                required
                            />
                        </FormField>

                        <!-- Taxa (%) -->
                        <FormField
                            id="rate"
                            label="Taxa (%)"
                            required
                            :error="form.errors.rate"
                            description="Valor entre 0 e 100"
                        >
                            <Input
                                v-model="form.rate"
                                id="rate"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                placeholder="23.00"
                                required
                            />
                        </FormField>

                        <!-- Taxa Padrão -->
                        <FormField
                            id="is_default"
                            label="Taxa Padrão"
                            :error="form.errors.is_default"
                            description="Definir como taxa padrão do sistema"
                        >
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    v-model:checked="form.is_default"
                                    id="is_default"
                                />
                                <label
                                    for="is_default"
                                    class="text-sm text-gray-700 dark:text-gray-300"
                                >
                                    Esta é a taxa padrão
                                </label>
                            </div>
                        </FormField>

                        <!-- Estado Ativo -->
                        <FormField
                            id="active"
                            label="Estado"
                            :error="form.errors.active"
                            description="Taxa ativa e disponível para uso"
                        >
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    v-model:checked="form.active"
                                    id="active"
                                />
                                <label
                                    for="active"
                                    class="text-sm text-gray-700 dark:text-gray-300"
                                >
                                    Taxa ativa
                                </label>
                            </div>
                        </FormField>
                    </div>

                    <!-- Botões de Ação -->
                    <div
                        class="mt-8 flex items-center justify-end space-x-3 border-t border-gray-200 dark:border-gray-800 pt-6"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            @click="$inertia.visit(route('vat-rates.index'))"
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            :disabled="!isFormValid || form.processing"
                            class="bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            {{
                                form.processing
                                    ? "A guardar..."
                                    : "Guardar Taxa IVA"
                            }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
