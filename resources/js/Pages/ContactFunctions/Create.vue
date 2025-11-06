<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Label from "@/Components/ui/Label.vue";
import Textarea from "@/Components/ui/Textarea.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import { UserCog, ArrowLeft } from "lucide-vue-next";

const form = useForm({
    name: "",
    description: "",
    active: true,
});

const isFormValid = computed(() => {
    return form.name.trim() !== "";
});

const submit = () => {
    if (!isFormValid.value) return;
    form.post(route("contact-functions.store"));
};
</script>

<template>
    <Head title="Adicionar Função" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <UserCog class="h-8 w-8 text-primary" />
                <div>
                    <h2
                        class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        Adicionar Função
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Criar nova função para contactos
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link :href="route('contact-functions.index')">
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
                                    Dados da Função
                                </h3>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                                >
                                    Preencha os dados da nova função. Campos
                                    obrigatórios marcados com *
                                </p>
                            </div>

                            <div class="space-y-6">
                                <!-- Nome -->
                                <div class="space-y-2">
                                    <Label for="name">
                                        Nome da Função *
                                        <span class="text-xs text-gray-500">
                                            (ex: Diretor, Gerente, Técnico)
                                        </span>
                                    </Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Diretor Comercial"
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
                                            (opcional)
                                        </span>
                                    </Label>
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        placeholder="Descrição detalhada da função..."
                                        rows="3"
                                        :class="{
                                            'border-red-500':
                                                form.errors.description,
                                        }"
                                    />
                                    <p
                                        v-if="form.errors.description"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.description }}
                                    </p>
                                </div>

                                <!-- Estado Ativo -->
                                <div class="flex items-center space-x-2">
                                    <Checkbox
                                        id="active"
                                        v-model:checked="form.active"
                                    />
                                    <Label
                                        for="active"
                                        class="text-sm font-normal cursor-pointer"
                                    >
                                        Função ativa no sistema
                                    </Label>
                                </div>

                                <!-- Actions -->
                                <div
                                    class="flex items-center justify-end gap-3 pt-4 border-t"
                                >
                                    <Link
                                        :href="route('contact-functions.index')"
                                    >
                                        <Button
                                            type="button"
                                            variant="outline"
                                            :disabled="form.processing"
                                        >
                                            Cancelar
                                        </Button>
                                    </Link>
                                    <Button
                                        type="submit"
                                        :disabled="
                                            !isFormValid || form.processing
                                        "
                                    >
                                        {{
                                            form.processing
                                                ? "A criar..."
                                                : "Criar Função"
                                        }}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
