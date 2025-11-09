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
    role: Object,
    permissions: Array,
    rolePermissions: Array,
});

// Form data
const form = useForm({
    name: props.role.name || "",
    permissions: props.rolePermissions || [],
    active: props.role.active ?? true,
});

// Computed properties
const isFormValid = computed(() => {
    return form.name.trim() !== "";
});

// Methods
const handleSubmit = () => {
    form.patch(route("roles.update", props.role.id));
};

// Toggle individual permission
const togglePermission = (permissionName) => {
    const index = form.permissions.indexOf(permissionName);
    if (index !== -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionName);
    }
};

// Check if permission is active
const isPermissionActive = (permissionName) => {
    return form.permissions.includes(permissionName);
};

// Get permission label in Portuguese
const getPermissionLabel = (action) => {
    const labels = {
        create: "Criar",
        read: "Visualizar",
        update: "Editar",
        delete: "Eliminar",
    };
    return labels[action] || action;
};

// Get action color
const getActionColor = (action) => {
    const colors = {
        create: "text-green-600 dark:text-green-400",
        read: "text-blue-600 dark:text-blue-400",
        update: "text-yellow-600 dark:text-yellow-400",
        delete: "text-red-600 dark:text-red-400",
    };
    return colors[action] || "text-gray-600 dark:text-gray-400";
};
</script>

<template>
    <Head :title="`Editar ${role.name}`" />

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
                                Editar Grupo de Permissões
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Atualizar grupo: {{ role.name }}
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

                    <!-- Permissões por Menu -->
                    <div class="space-y-4">
                        <label
                            class="text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Permissões por Menu
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Organizadas pela ordem da sidebar
                        </p>

                        <div class="space-y-4">
                            <div
                                v-for="module in permissions"
                                :key="module.key"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                            >
                                <div class="mb-3">
                                    <span
                                        class="font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ module.name }}
                                    </span>
                                    <span
                                        v-if="module.group"
                                        class="ml-2 text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        ({{ module.group }})
                                    </span>
                                </div>

                                <div
                                    class="grid grid-cols-2 md:grid-cols-4 gap-3"
                                >
                                    <div
                                        v-for="(
                                            permission, action
                                        ) in module.permissions"
                                        :key="permission.name"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox
                                            :id="`permission-${permission.name}`"
                                            :checked="
                                                isPermissionActive(
                                                    permission.name
                                                )
                                            "
                                            @update:checked="
                                                togglePermission(
                                                    permission.name
                                                )
                                            "
                                        />
                                        <label
                                            :for="`permission-${permission.name}`"
                                            :class="[
                                                'text-sm font-medium cursor-pointer',
                                                getActionColor(action),
                                            ]"
                                        >
                                            {{ getPermissionLabel(action) }}
                                        </label>
                                    </div>
                                </div>
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
                                id="active-checkbox"
                                :checked="form.active"
                                @update:checked="form.active = $event"
                            />
                            <label
                                for="active-checkbox"
                                class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer"
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
                            {{
                                form.processing
                                    ? "A guardar..."
                                    : "Atualizar Grupo"
                            }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
