<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Label from "@/Components/ui/Label.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import { ListChecks, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    eventAction: Object,
});

const form = useForm({
    name: props.eventAction.name,
    description: props.eventAction.description || "",
    is_active: props.eventAction.is_active,
});

const isFormValid = computed(() => {
    return form.name.trim() !== "";
});

const submit = () => {
    if (!isFormValid.value) return;
    form.patch(route("calendar-event-actions.update", props.eventAction.id));
};
</script>

<template>
    <Head title="Editar Ação de Evento" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <ListChecks
                        class="h-6 w-6 text-green-600 dark:text-green-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Editar Ação de Evento
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Atualizar dados da ação de evento
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
                    <Link
                        :href="route('calendar-event-actions.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Calendário - Ações
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Editar</li>
            </ol>
        </nav>

        <div class="mb-4">
            <Link :href="route('calendar-event-actions.index')">
                <Button variant="ghost" size="sm">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Voltar à Lista
                </Button>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6">
                    <div class="mb-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Dados da Ação de Evento
                        </h3>
                        <p
                            class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                        >
                            Atualize os dados da ação. Campos obrigatórios
                            marcados com *
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Nome -->
                        <div class="space-y-2">
                            <Label for="name">
                                Nome da Ação *
                                <span class="text-xs text-gray-500">
                                    (ex: Confirmar, Reagendar, Aprovar)
                                </span>
                            </Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Confirmar"
                                required
                                :class="{
                                    'border-red-500': form.errors.name,
                                }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Descrição -->
                        <div class="space-y-2">
                            <Label for="description">
                                Descrição
                                <span class="text-xs text-gray-500">
                                    (Opcional)
                                </span>
                            </Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Descrição da ação..."
                                rows="3"
                                :class="{
                                    'border-red-500': form.errors.description,
                                }"
                            />
                            <p
                                v-if="form.errors.description"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Estado -->
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                            />
                            <Label for="is_active" class="cursor-pointer">
                                Ação ativa
                            </Label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3"
                >
                    <Link :href="route('calendar-event-actions.index')">
                        <Button type="button" variant="outline">
                            Cancelar
                        </Button>
                    </Link>
                    <Button
                        type="submit"
                        :disabled="!isFormValid || form.processing"
                        class="bg-green-600 hover:bg-green-700 text-white"
                    >
                        {{
                            form.processing ? "A guardar..." : "Atualizar Ação"
                        }}
                    </Button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
