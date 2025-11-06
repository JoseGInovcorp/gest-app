<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Label from "@/Components/ui/Label.vue";
import Checkbox from "@/Components/ui/Checkbox.vue";
import { Globe, ArrowLeft } from "lucide-vue-next";

const form = useForm({
    code: "",
    name: "",
    name_en: "",
    iso3: "",
    numeric_code: null,
    phone_prefix: "",
    vies_enabled: false,
    currency_code: "",
    timezone: "",
    active: true,
});

const isFormValid = computed(() => {
    return form.code.length === 2 && form.name.trim() !== "";
});

const submit = () => {
    if (!isFormValid.value) return;

    form.transform((data) => ({
        ...data,
        code: data.code.toUpperCase(),
        iso3: data.iso3 ? data.iso3.toUpperCase() : null,
        currency_code: data.currency_code
            ? data.currency_code.toUpperCase()
            : null,
    })).post(route("countries.store"));
};
</script>

<template>
    <Head title="Adicionar País" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Globe class="h-8 w-8 text-primary" />
                <div>
                    <h2
                        class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        Adicionar País
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Criar novo país no sistema
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-4">
                    <Link :href="route('countries.index')">
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
                                    Dados do País
                                </h3>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                                >
                                    Preencha os dados do novo país. Campos
                                    obrigatórios marcados com *
                                </p>
                            </div>

                            <div class="space-y-6">
                                <!-- Códigos ISO -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                >
                                    <div class="space-y-2">
                                        <Label for="code">
                                            Código ISO 2 *
                                            <span class="text-xs text-gray-500"
                                                >(ex: PT)</span
                                            >
                                        </Label>
                                        <Input
                                            id="code"
                                            v-model="form.code"
                                            type="text"
                                            maxlength="2"
                                            placeholder="PT"
                                            required
                                            class="uppercase"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.code,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.code"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.code }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="iso3">
                                            Código ISO 3
                                            <span class="text-xs text-gray-500"
                                                >(ex: PRT)</span
                                            >
                                        </Label>
                                        <Input
                                            id="iso3"
                                            v-model="form.iso3"
                                            type="text"
                                            maxlength="3"
                                            placeholder="PRT"
                                            class="uppercase"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.iso3,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.iso3"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.iso3 }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="numeric_code">
                                            Código Numérico
                                            <span class="text-xs text-gray-500"
                                                >(ex: 620)</span
                                            >
                                        </Label>
                                        <Input
                                            id="numeric_code"
                                            v-model="form.numeric_code"
                                            type="number"
                                            min="1"
                                            max="999"
                                            placeholder="620"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.numeric_code,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.numeric_code"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.numeric_code }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Nomes -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div class="space-y-2">
                                        <Label for="name">Nome *</Label>
                                        <Input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            placeholder="Portugal"
                                            required
                                            :class="{
                                                'border-red-500':
                                                    form.errors.name,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.name"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.name }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="name_en"
                                            >Nome (Inglês)</Label
                                        >
                                        <Input
                                            id="name_en"
                                            v-model="form.name_en"
                                            type="text"
                                            placeholder="Portugal"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.name_en,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.name_en"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.name_en }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Contacto e Moeda -->
                                <div
                                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                >
                                    <div class="space-y-2">
                                        <Label for="phone_prefix">
                                            Prefixo Telefone
                                            <span class="text-xs text-gray-500"
                                                >(ex: 351)</span
                                            >
                                        </Label>
                                        <Input
                                            id="phone_prefix"
                                            v-model="form.phone_prefix"
                                            type="text"
                                            placeholder="351"
                                            maxlength="10"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.phone_prefix,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.phone_prefix"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.phone_prefix }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="currency_code">
                                            Moeda
                                            <span class="text-xs text-gray-500"
                                                >(ex: EUR)</span
                                            >
                                        </Label>
                                        <Input
                                            id="currency_code"
                                            v-model="form.currency_code"
                                            type="text"
                                            maxlength="3"
                                            placeholder="EUR"
                                            class="uppercase"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.currency_code,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.currency_code"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.currency_code }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="timezone">
                                            Fuso Horário
                                            <span class="text-xs text-gray-500"
                                                >(ex: Europe/Lisbon)</span
                                            >
                                        </Label>
                                        <Input
                                            id="timezone"
                                            v-model="form.timezone"
                                            type="text"
                                            placeholder="Europe/Lisbon"
                                            :class="{
                                                'border-red-500':
                                                    form.errors.timezone,
                                            }"
                                        />
                                        <p
                                            v-if="form.errors.timezone"
                                            class="text-sm text-red-500"
                                        >
                                            {{ form.errors.timezone }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Checkboxes -->
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <Checkbox
                                            id="vies_enabled"
                                            v-model:checked="form.vies_enabled"
                                        />
                                        <Label
                                            for="vies_enabled"
                                            class="text-sm font-normal cursor-pointer"
                                        >
                                            Suporta validação VIES (União
                                            Europeia)
                                        </Label>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <Checkbox
                                            id="active"
                                            v-model:checked="form.active"
                                        />
                                        <Label
                                            for="active"
                                            class="text-sm font-normal cursor-pointer"
                                        >
                                            País ativo no sistema
                                        </Label>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div
                                    class="flex items-center justify-end gap-3 pt-4 border-t"
                                >
                                    <Link :href="route('countries.index')">
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
                                                : "Criar País"
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
