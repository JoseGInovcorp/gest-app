<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft } from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";

const props = defineProps({
    template: Object,
    roles: Array,
});

const form = useForm({
    code: props.template.code,
    label: props.template.label,
    description: props.template.description,
    assigned_group: props.template.assigned_group,
    default_sequence: props.template.default_sequence,
    is_active: props.template.is_active,
});

const submit = () => {
    form.patch(route("task-templates.update", props.template.id));
};
</script>

<template>
    <Head title="Editar Template de Tarefa" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <Link
                :href="route('task-templates.index')"
                class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-4"
            >
                <ArrowLeft class="h-4 w-4 mr-1" />
                Voltar aos Templates
            </Link>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Editar Template de Tarefa
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Atualizar template: {{ template.label }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.code"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-mono"
                            placeholder="Ex: VALIDATE_STOCK"
                        />
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            Identificador único em MAIÚSCULAS_COM_UNDERSCORES
                        </p>
                        <p
                            v-if="form.errors.code"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.label"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            placeholder="Ex: Validar Disponibilidade em Armazém"
                        />
                        <p
                            v-if="form.errors.label"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.label }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Descrição
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            placeholder="Descrição detalhada da tarefa..."
                        ></textarea>
                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Grupo Padrão
                        </label>
                        <select
                            v-model="form.assigned_group"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        >
                            <option :value="null">Nenhum grupo</option>
                            <option
                                v-for="role in roles"
                                :key="role.name"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </option>
                        </select>
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            Grupo sugerido ao criar tarefa com este template
                        </p>
                        <p
                            v-if="form.errors.assigned_group"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.assigned_group }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Ordem no Workflow
                        </label>
                        <input
                            v-model.number="form.default_sequence"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        />
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            Ordem padrão de execução (menor número = primeiro)
                        </p>
                        <p
                            v-if="form.errors.default_sequence"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.default_sequence }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            />
                            <span
                                class="ml-2 text-sm text-gray-700 dark:text-gray-300"
                            >
                                Template ativo
                            </span>
                        </label>
                        <p
                            class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                        >
                            Apenas templates ativos aparecem na criação de
                            ordens de trabalho
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end">
                <Link
                    :href="route('task-templates.index')"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                >
                    Cancelar
                </Link>
                <Button type="submit" :disabled="form.processing">
                    {{
                        form.processing
                            ? "A atualizar..."
                            : "Atualizar Template"
                    }}
                </Button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
