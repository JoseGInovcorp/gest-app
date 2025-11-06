<script setup>
import { computed } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import Button from "@/Components/ui/Button.vue";
import { Users, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    user: Object,
    roles: Array,
});

// Form data
const form = useForm({
    name: props.user.name || "",
    email: props.user.email || "",
    mobile: props.user.mobile || "",
    password: "",
    password_confirmation: "",
    role: props.user.role || "",
    active: props.user.active ?? true,
});

// Computed properties
const isFormValid = computed(() => {
    const basicValid =
        form.name.trim() !== "" && form.email.trim() !== "" && form.role !== "";

    // Se password preenchida, validar confirmação
    if (form.password) {
        return (
            basicValid &&
            form.password.length >= 8 &&
            form.password === form.password_confirmation
        );
    }

    return basicValid;
});

// Methods
const handleSubmit = () => {
    form.put(route("users.update", props.user.id));
};
</script>

<template>
    <Head :title="`Editar ${user.name}`" />

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
                                Editar Utilizador
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Atualizar dados de {{ user.name }}
                            </p>
                        </div>
                    </div>

                    <Button
                        variant="outline"
                        @click="$inertia.visit(route('users.index'))"
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
                        >
                            <Input
                                v-model="form.name"
                                id="name"
                                placeholder="Nome completo"
                                required
                            />
                        </FormField>

                        <!-- Email -->
                        <FormField
                            id="email"
                            label="Email"
                            required
                            :error="form.errors.email"
                        >
                            <Input
                                v-model="form.email"
                                id="email"
                                type="email"
                                placeholder="utilizador@example.com"
                                required
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
                                placeholder="912345678"
                            />
                        </FormField>

                        <!-- Grupo de Permissões -->
                        <FormField
                            id="role"
                            label="Grupo de Permissões"
                            required
                            :error="form.errors.role"
                        >
                            <Select v-model="form.role" id="role" required>
                                <option value="">Selecione um grupo</option>
                                <option
                                    v-for="role in roles"
                                    :key="role.value"
                                    :value="role.value"
                                >
                                    {{ role.label }}
                                </option>
                            </Select>
                        </FormField>

                        <!-- Password -->
                        <FormField
                            id="password"
                            label="Nova Password"
                            :error="form.errors.password"
                            description="Deixe em branco para manter a password atual"
                        >
                            <Input
                                v-model="form.password"
                                id="password"
                                type="password"
                                placeholder="••••••••"
                            />
                        </FormField>

                        <!-- Confirmar Password -->
                        <FormField
                            id="password_confirmation"
                            label="Confirmar Nova Password"
                            :error="form.errors.password_confirmation"
                        >
                            <Input
                                v-model="form.password_confirmation"
                                id="password_confirmation"
                                type="password"
                                placeholder="••••••••"
                            />
                        </FormField>

                        <!-- Estado Ativo -->
                        <FormField
                            id="active"
                            label="Estado"
                            :error="form.errors.active"
                            class="lg:col-span-2"
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
                                    Utilizador ativo
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
                            @click="$inertia.visit(route('users.index'))"
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
                                    : "Atualizar Utilizador"
                            }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
