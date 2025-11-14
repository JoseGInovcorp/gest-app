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
    types: Array,
    actions: Array,
    entities: Array,
    users: Array,
    defaultData: String,
    defaultHora: String,
});

const form = useForm({
    user_id: null,
    entity_id: null,
    calendar_event_type_id: null,
    calendar_event_action_id: null,
    event_date: props.defaultData || new Date().toISOString().split("T")[0],
    event_time: props.defaultHora || "09:00",
    duracao: 60,
    partilha: false,
    shared_with: [],
    conhecimento: "",
    descricao: "",
    estado: "agendado",
});

const isFormValid = computed(() => {
    return (
        form.user_id &&
        form.calendar_event_type_id &&
        form.event_date &&
        form.event_time &&
        form.duracao > 0
    );
});

const submit = () => {
    if (!isFormValid.value) return;
    // Map back to expected field names before sending
    const data = {
        user_id: form.user_id,
        entity_id: form.entity_id,
        calendar_event_type_id: form.calendar_event_type_id,
        calendar_event_action_id: form.calendar_event_action_id,
        data: form.event_date,
        hora: form.event_time,
        duracao: form.duracao,
        partilha: form.partilha,
        shared_with: form.shared_with,
        conhecimento: form.conhecimento,
        descricao: form.descricao,
        estado: form.estado,
    };

    form.transform(() => data).post(route("calendar-events.store"));
};
</script>

<template>
    <Head title="Criar Evento" />

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
                        Criar Evento
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Agendar nova atividade no calendário
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
                        :href="route('calendar.index')"
                        class="hover:text-gray-700 dark:hover:text-gray-200"
                    >
                        Calendário
                    </Link>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Criar Evento</li>
            </ol>
        </nav>

        <div class="mb-4">
            <Link :href="route('calendar.index')">
                <Button variant="ghost" size="sm">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Voltar ao Calendário
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
                            Dados do Evento
                        </h3>
                        <p
                            class="text-sm text-gray-600 dark:text-gray-400 mt-1"
                        >
                            Campos obrigatórios marcados com *
                        </p>
                    </div>

                    <div class="space-y-6">
                        <!-- Utilizador -->
                        <div class="space-y-2">
                            <Label for="user_id">
                                Utilizador *
                                <span class="text-xs text-gray-500">
                                    (Responsável pelo evento)
                                </span>
                            </Label>
                            <select
                                id="user_id"
                                v-model="form.user_id"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{
                                    'border-red-500': form.errors.user_id,
                                }"
                                required
                            >
                                <option :value="null" disabled>
                                    Selecione um utilizador
                                </option>
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.user_id"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.user_id }}
                            </p>
                        </div>

                        <!-- Entidade -->
                        <div class="space-y-2">
                            <Label for="entity_id"
                                >Entidade (Cliente/Fornecedor)</Label
                            >
                            <select
                                id="entity_id"
                                v-model="form.entity_id"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{
                                    'border-red-500': form.errors.entity_id,
                                }"
                            >
                                <option :value="null">Sem entidade</option>
                                <option
                                    v-for="entity in entities"
                                    :key="entity.id"
                                    :value="entity.id"
                                >
                                    {{ entity.commercial_name || entity.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.entity_id"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.entity_id }}
                            </p>
                        </div>

                        <!-- Tipo -->
                        <div class="space-y-2">
                            <Label for="calendar_event_type_id">Tipo *</Label>
                            <select
                                id="calendar_event_type_id"
                                v-model="form.calendar_event_type_id"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{
                                    'border-red-500':
                                        form.errors.calendar_event_type_id,
                                }"
                                required
                            >
                                <option :value="null" disabled>
                                    Selecione um tipo
                                </option>
                                <option
                                    v-for="type in types"
                                    :key="type.id"
                                    :value="type.id"
                                >
                                    {{ type.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.calendar_event_type_id"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.calendar_event_type_id }}
                            </p>
                        </div>

                        <!-- Ação -->
                        <div class="space-y-2">
                            <Label for="calendar_event_action_id">Ação</Label>
                            <select
                                id="calendar_event_action_id"
                                v-model="form.calendar_event_action_id"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{
                                    'border-red-500':
                                        form.errors.calendar_event_action_id,
                                }"
                            >
                                <option :value="null">Sem ação</option>
                                <option
                                    v-for="action in actions"
                                    :key="action.id"
                                    :value="action.id"
                                >
                                    {{ action.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.calendar_event_action_id"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.calendar_event_action_id }}
                            </p>
                        </div>

                        <!-- Data e Hora -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="data">Data *</Label>
                                <Input
                                    id="data"
                                    v-model="form.event_date"
                                    type="date"
                                    required
                                    :class="{
                                        'border-red-500': form.errors.data,
                                    }"
                                />
                                <p
                                    v-if="form.errors.data"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.data }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="hora">Hora *</Label>
                                <Input
                                    id="hora"
                                    v-model="form.event_time"
                                    type="time"
                                    required
                                    :class="{
                                        'border-red-500': form.errors.hora,
                                    }"
                                />
                                <p
                                    v-if="form.errors.hora"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.hora }}
                                </p>
                            </div>
                        </div>

                        <!-- Duração -->
                        <div class="space-y-2">
                            <Label for="duracao"> Duração * (minutos) </Label>
                            <Input
                                id="duracao"
                                v-model.number="form.duracao"
                                type="number"
                                min="0"
                                step="15"
                                required
                                :class="{
                                    'border-red-500': form.errors.duracao,
                                }"
                            />
                            <p
                                v-if="form.errors.duracao"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.duracao }}
                            </p>
                        </div>

                        <!-- Estado -->
                        <div class="space-y-2">
                            <Label for="estado">Estado *</Label>
                            <select
                                id="estado"
                                v-model="form.estado"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                :class="{
                                    'border-red-500': form.errors.estado,
                                }"
                                required
                            >
                                <option value="agendado">Agendado</option>
                                <option value="em_curso">Em Curso</option>
                                <option value="concluido">Concluído</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                            <p
                                v-if="form.errors.estado"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.estado }}
                            </p>
                        </div>

                        <!-- Partilha -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="partilha"
                                    v-model:checked="form.partilha"
                                />
                                <Label for="partilha" class="cursor-pointer">
                                    Partilhar evento
                                </Label>
                            </div>

                            <!-- Seleção de utilizadores para partilha -->
                            <div v-if="form.partilha" class="ml-6 space-y-2">
                                <Label class="text-sm">
                                    Partilhar com:
                                    <span class="text-xs text-gray-500">
                                        (deixe vazio para partilhar com todos)
                                    </span>
                                </Label>
                                <div
                                    class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto border rounded-md p-3 bg-gray-50 dark:bg-gray-800"
                                >
                                    <div
                                        v-for="user in users"
                                        :key="user.id"
                                        class="flex items-center space-x-2"
                                    >
                                        <input
                                            type="checkbox"
                                            :id="`user-${user.id}`"
                                            :value="user.id"
                                            v-model="form.shared_with"
                                            class="h-4 w-4 shrink-0 rounded-sm border border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                        />
                                        <Label
                                            :for="`user-${user.id}`"
                                            class="cursor-pointer text-sm"
                                        >
                                            {{ user.name }}
                                        </Label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conhecimento -->
                        <div class="space-y-2">
                            <Label for="conhecimento">
                                Conhecimento
                                <span class="text-xs text-gray-500">
                                    (Opcional)
                                </span>
                            </Label>
                            <Textarea
                                id="conhecimento"
                                v-model="form.conhecimento"
                                placeholder="Informação relevante ou lições aprendidas..."
                                rows="3"
                                :class="{
                                    'border-red-500': form.errors.conhecimento,
                                }"
                            />
                            <p
                                v-if="form.errors.conhecimento"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.conhecimento }}
                            </p>
                        </div>

                        <!-- Descrição -->
                        <div class="space-y-2">
                            <Label for="descricao">
                                Descrição
                                <span class="text-xs text-gray-500">
                                    (Opcional)
                                </span>
                            </Label>
                            <Textarea
                                id="descricao"
                                v-model="form.descricao"
                                placeholder="Descrição detalhada do evento..."
                                rows="3"
                                :class="{
                                    'border-red-500': form.errors.descricao,
                                }"
                            />
                            <p
                                v-if="form.errors.descricao"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.descricao }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3"
                >
                    <Link :href="route('calendar.index')">
                        <Button type="button" variant="outline">
                            Cancelar
                        </Button>
                    </Link>
                    <Button
                        type="submit"
                        :disabled="!isFormValid || form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white"
                    >
                        {{ form.processing ? "A guardar..." : "Criar Evento" }}
                    </Button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
