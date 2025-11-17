<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ArrowLeft, Plus, Trash2 } from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";
import { watch } from "vue";

const props = defineProps({
    customerOrders: Array,
    users: Array,
    roles: Array,
    taskTypes: Object,
});

const form = useForm({
    customer_order_id: null,
    title: "",
    description: "",
    priority: "normal",
    tasks: [
        {
            task_type: "",
            title: "",
            description: "",
            assigned_to: null,
            assigned_group: null,
            due_date: "",
            sequence_order: 1,
        },
    ],
});

const onTaskTypeChange = (task, index) => {
    if (task.task_type && props.taskTypes[task.task_type]) {
        const taskData = props.taskTypes[task.task_type];
        task.title = taskData.label;
        task.description = taskData.description || "";
        task.assigned_group = taskData.assigned_group || null;
    }
};

const addTask = () => {
    form.tasks.push({
        task_type: "",
        title: "",
        description: "",
        assigned_to: null,
        assigned_group: null,
        due_date: "",
        sequence_order: form.tasks.length + 1,
    });
};

const removeTask = (index) => {
    form.tasks.splice(index, 1);
    // Reorder sequence
    form.tasks.forEach((task, idx) => {
        task.sequence_order = idx + 1;
    });
};

const submit = () => {
    form.post(route("work-orders.store"));
};
</script>

<template>
    <Head title="Criar Ordem de Trabalho" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <Link
                :href="route('work-orders.index')"
                class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-4"
            >
                <ArrowLeft class="h-4 w-4 mr-1" />
                Voltar às Ordens de Trabalho
            </Link>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                Criar Ordem de Trabalho
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Criar uma nova ordem de trabalho manualmente
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Work Order Details -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <h2
                    class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4"
                >
                    Informações da Ordem
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Encomenda de Cliente
                        </label>
                        <select
                            v-model="form.customer_order_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        >
                            <option :value="null">Nenhuma encomenda</option>
                            <option
                                v-for="order in customerOrders"
                                :key="order.id"
                                :value="order.id"
                            >
                                #{{ order.order_number }} -
                                {{ order.customer?.name }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.customer_order_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.customer_order_id }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Prioridade <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.priority"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                        >
                            <option value="baixa">Baixa</option>
                            <option value="normal">Normal</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select>
                        <p
                            v-if="form.errors.priority"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.priority }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
                        >
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                            placeholder="Ex: Processar Encomenda #123"
                        />
                        <p
                            v-if="form.errors.title"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.title }}
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
                            placeholder="Descrição opcional da ordem de trabalho..."
                        ></textarea>
                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tasks -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center justify-between mb-4">
                    <h2
                        class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                    >
                        Tarefas ({{ form.tasks.length }})
                    </h2>
                    <Button
                        @click="addTask"
                        type="button"
                        variant="outline"
                        size="sm"
                    >
                        <Plus class="h-4 w-4 mr-1" />
                        Adicionar Tarefa
                    </Button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(task, index) in form.tasks"
                        :key="index"
                        class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <h3
                                class="font-medium text-gray-900 dark:text-gray-100"
                            >
                                Tarefa {{ index + 1 }}
                            </h3>
                            <button
                                v-if="form.tasks.length > 1"
                                @click="removeTask(index)"
                                type="button"
                                class="text-red-600 hover:text-red-700"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Tipo de Tarefa
                                    <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="task.task_type"
                                    @change="onTaskTypeChange(task, index)"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                                >
                                    <option value="">Selecionar tipo...</option>
                                    <option
                                        v-for="(
                                            typeData, typeCode
                                        ) in taskTypes"
                                        :key="typeCode"
                                        :value="typeCode"
                                    >
                                        {{ typeData.label }}
                                    </option>
                                </select>
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Selecionar irá preencher título, descrição e
                                    grupo
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Título <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="task.title"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                                    placeholder="Título da tarefa"
                                />
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Pode editar o título sugerido
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Descrição
                                </label>
                                <textarea
                                    v-model="task.description"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                                    placeholder="Descrição da tarefa..."
                                ></textarea>
                                <p
                                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Pode editar a descrição sugerida
                                </p>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Atribuir a Utilizador
                                </label>
                                <select
                                    v-model="task.assigned_to"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                                >
                                    <option :value="null">
                                        Nenhum utilizador
                                    </option>
                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Atribuir a Grupo
                                </label>
                                <select
                                    v-model="task.assigned_group"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
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
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >
                                    Data Limite
                                </label>
                                <input
                                    v-model="task.due_date"
                                    type="date"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <p v-if="form.errors.tasks" class="text-sm text-red-600">
                        {{ form.errors.tasks }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 justify-end">
                <Link
                    :href="route('work-orders.index')"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                >
                    Cancelar
                </Link>
                <Button
                    type="submit"
                    variant="default"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? "A criar..."
                            : "Criar Ordem de Trabalho"
                    }}
                </Button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
