<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Label from "@/Components/ui/Label.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import { Calendar, ArrowLeft } from "lucide-vue-next";

const props = defineProps({
    eventType: Object,
});

const form = useForm({
    name: props.eventType.name,
    description: props.eventType.description || "",
    color: props.eventType.color,
    icon: props.eventType.icon || "",
    is_active: props.eventType.is_active,
});

const isFormValid = computed(() => {
    return form.name.trim() !== "" && form.color.match(/^#[0-9A-Fa-f]{6}$/);
});

const submit = () => {
    if (!isFormValid.value) return;
    form.patch(route("calendar-event-types.update", props.eventType.id));
};
</script>

<template>
    <Head title="Editar Tipo de Evento" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <Calendar
                        class="h-6 w-6 text-blue-600 dark:text-blue-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Editar Tipo de Evento
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Atualizar dados do tipo de evento
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
                        :href="route('calendar-event-types.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Calendário - Tipos
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Editar</li>
            </ol>
        </nav>

        <div class="mb-4">
            <Link :href="route('calendar-event-types.index')">
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
                            Dados do Tipo de Evento
                        </h3>
                        <p
                            class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                        >
                            Atualize os dados do tipo. Campos obrigatórios
                            marcados com *
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Nome -->
                        <div class="space-y-2">
                            <Label for="name">
                                Nome do Tipo *
                                <span class="text-xs text-gray-500">
                                    (ex: Visita, Reunião, Intervenção Técnica)
                                </span>
                            </Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Visita"
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
                                placeholder="Descrição do tipo de evento..."
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

                        <!-- Cor -->
                        <div class="space-y-2">
                            <Label for="color">
                                Cor *
                                <span class="text-xs text-gray-500">
                                    (Código hexadecimal)
                                </span>
                            </Label>
                            <div class="flex items-center gap-3">
                                <input
                                    id="color"
                                    v-model="form.color"
                                    type="color"
                                    class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer"
                                />
                                <Input
                                    v-model="form.color"
                                    type="text"
                                    placeholder="#3B82F6"
                                    pattern="^#[0-9A-Fa-f]{6}$"
                                    class="flex-1"
                                    :class="{
                                        'border-red-500': form.errors.color,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.color"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.color }}
                            </p>
                        </div>

                        <!-- Ícone -->
                        <div class="space-y-2">
                            <Label for="icon">
                                Ícone
                                <span class="text-xs text-gray-500">
                                    (Nome do ícone Lucide, opcional)
                                </span>
                            </Label>
                            <Input
                                id="icon"
                                v-model="form.icon"
                                type="text"
                                placeholder="Calendar"
                                maxlength="50"
                                :class="{
                                    'border-red-500': form.errors.icon,
                                }"
                            />
                            <p class="text-xs text-gray-500">
                                Consulte
                                <a
                                    href="https://lucide.dev/icons/"
                                    target="_blank"
                                    class="text-blue-600 hover:underline"
                                    >lucide.dev</a
                                >
                                para ver ícones disponíveis
                            </p>
                            <p
                                v-if="form.errors.icon"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.icon }}
                            </p>
                        </div>

                        <!-- Estado -->
                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                            />
                            <Label for="is_active" class="cursor-pointer">
                                Tipo ativo
                            </Label>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3"
                >
                    <Link :href="route('calendar-event-types.index')">
                        <Button type="button" variant="outline">
                            Cancelar
                        </Button>
                    </Link>
                    <Button
                        type="submit"
                        :disabled="!isFormValid || form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white"
                    >
                        {{
                            form.processing ? "A guardar..." : "Atualizar Tipo"
                        }}
                    </Button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
