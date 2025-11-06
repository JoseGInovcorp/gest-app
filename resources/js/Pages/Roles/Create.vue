<script setup>
import { computed } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import Button from "@/Components/ui/Button.vue";
import { Shield, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    permissions: Array,
});

// Form data
const form = useForm({
    name: "",
    permissions: [],
    active: true,
});

// Computed properties
const isFormValid = computed(() => {
    return form.name.trim() !== "";
});

// Methods
const handleSubmit = () => {
    form.post(route("roles.store"));
};

const toggleModule = (module) => {
    const modulePermissions = Object.values(module.permissions).map(
        (p) => p.name
    );
    const isModuleActive = modulePermissions.every((p) =>
        form.permissions.includes(p)
    );

    if (isModuleActive) {
        // Remove all module permissions
        modulePermissions.forEach((p) => {
            const index = form.permissions.indexOf(p);
            if (index !== -1) form.permissions.splice(index, 1);
        });
    } else {
        // Add all module permissions
        modulePermissions.forEach((p) => {
            if (!form.permissions.includes(p)) {
                form.permissions.push(p);
            }
        });
    }
};

const isModuleActive = (module) => {
    const modulePermissions = Object.values(module.permissions).map(
        (p) => p.name
    );
    return modulePermissions.every((p) => form.permissions.includes(p));
};
</script>

<template>
    <Head title="Novo Grupo de Permissões" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600/10 dark:bg-blue-400/10"
                        >
                            <Shield
                                class="h-6 w-6 text-blue-600 dark:text-blue-400"
                            />
                        </div>
                        <div>
                            <h1
                                class="text-2xl font-bold text-gray-900 dark:text-white"
                            >
                                Novo Grupo de Permissões
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Criar novo grupo com permissões personalizadas
                            </p>
                        </div>
                    </div>

                    <Button
                        variant="outline"
                        @click="$inertia.visit(route('roles.index'))"
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
                    <!-- Nome do Grupo -->
                    <FormField
                        id="name"
                        label="Nome do Grupo"
                        required
                        :error="form.errors.name"
                        description="Ex: Gestor Comercial, Técnico Financeiro"
                        class="mb-6"
                    >
                        <Input
                            v-model="form.name"
                            id="name"
                            placeholder="Nome do grupo de permissões"
                            required
                        />
                    </FormField>

                    <!-- Permissões por Módulo -->
                    <div class="space-y-4">
                        <label
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Ativar ou Inativar Menus
                        </label>

                        <div class="space-y-3">
                            <div
                                v-for="module in permissions"
                                :key="module.key"
                                class="flex items-center justify-between border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                            >
                                <div class="flex items-center space-x-3">
                                    <Checkbox
                                        :checked="isModuleActive(module)"
                                        @update:checked="toggleModule(module)"
                                    />
                                    <span
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ module.name }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    Create, Read, Update, Delete
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="space-y-3">
                        <label
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Estado
                        </label>
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                :checked="form.active"
                                @update:checked="form.active = $event"
                            />
                            <label
                                class="text-sm text-gray-700 dark:text-gray-300"
                            >
                                Ativo
                            </label>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div
                        class="mt-8 flex items-center justify-end space-x-3 border-t border-gray-200 dark:border-gray-800 pt-6"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            @click="$inertia.visit(route('roles.index'))"
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            :disabled="!isFormValid || form.processing"
                            class="bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            {{ form.processing ? "A criar..." : "Criar Grupo" }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
