<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import { Calendar as CalendarIcon, Plus, Filter } from "lucide-vue-next";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import listPlugin from "@fullcalendar/list";
import ptBrLocale from "@fullcalendar/core/locales/pt";

const props = defineProps({
    types: Array,
    actions: Array,
    users: Array,
    entities: Array,
    can: {
        type: Object,
        default: () => ({
            create: false,
            update: false,
            delete: false,
        }),
    },
});

const filterUser = ref(null);
const filterEntity = ref(null);
const calendarRef = ref(null);

const calendarOptions = computed(() => ({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
    initialView: "dayGridMonth",
    locale: ptBrLocale,
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
    },
    buttonText: {
        today: "Hoje",
        month: "Mês",
        week: "Semana",
        day: "Dia",
        list: "Lista",
    },
    events: (fetchInfo, successCallback, failureCallback) => {
        const params = new URLSearchParams({
            start: fetchInfo.startStr,
            end: fetchInfo.endStr,
        });

        if (filterUser.value) {
            params.append("user_id", filterUser.value);
        }

        if (filterEntity.value) {
            params.append("entity_id", filterEntity.value);
        }

        fetch(route("calendar.events.json") + "?" + params.toString())
            .then((response) => response.json())
            .then((data) => successCallback(data))
            .catch((error) => {
                console.error("Error fetching events:", error);
                failureCallback(error);
            });
    },
    editable: props.can.update,
    selectable: props.can.create,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    select: handleDateSelect,
    eventClick: handleEventClick,
    eventDrop: handleEventUpdate,
    eventResize: handleEventUpdate,
    height: "auto",
    contentHeight: 650,
}));

const handleDateSelect = (selectInfo) => {
    if (!props.can.create) return;

    // Extract date and time from selection
    const start = new Date(selectInfo.start);
    const data = start.toISOString().split("T")[0];
    const hora = start.toTimeString().substring(0, 5);

    // Navigate to create with query params
    router.get(
        route("calendar-events.create"),
        { data, hora },
        { preserveScroll: true }
    );

    // Clear selection
    let calendarApi = selectInfo.view.calendar;
    calendarApi.unselect();
};

const handleEventClick = (clickInfo) => {
    const eventId = clickInfo.event.id;
    router.visit(route("calendar-events.show", eventId));
};

const handleEventUpdate = (eventDropInfo) => {
    if (!props.can.update) return;

    const event = eventDropInfo.event;
    const start = event.start;

    // Update event via API
    router.patch(
        route("calendar-events.update", event.id),
        {
            data: start.toISOString().split("T")[0],
            hora: start.toTimeString().substring(0, 5),
        },
        {
            preserveScroll: true,
            onError: () => {
                eventDropInfo.revert();
            },
        }
    );
};

const applyFilters = () => {
    if (calendarRef.value) {
        const calendarApi = calendarRef.value.getApi();
        calendarApi.refetchEvents();
    }
};

const clearFilters = () => {
    filterUser.value = null;
    filterEntity.value = null;
    applyFilters();
};
</script>

<template>
    <Head title="Calendário" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <CalendarIcon
                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        />
                    </div>
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Calendário
                        </h1>
                        <p class="text-gray-500 dark:text-gray-400">
                            Gestão de eventos e atividades
                        </p>
                    </div>
                </div>

                <Link v-if="can.create" :href="route('calendar-events.create')">
                    <Button class="bg-blue-600 hover:bg-blue-700 text-white">
                        <Plus class="h-4 w-4 mr-2" />
                        Criar Evento
                    </Button>
                </Link>
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
                <li class="text-gray-900 dark:text-white">Calendário</li>
            </ol>
        </nav>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4 mb-6">
            <div class="flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <Filter class="h-4 w-4 text-gray-400" />
                    <span
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Filtros:
                    </span>
                </div>

                <div class="flex-1 min-w-[200px] max-w-xs">
                    <label
                        for="filterUser"
                        class="block text-xs text-gray-600 dark:text-gray-400 mb-1"
                    >
                        Utilizador
                    </label>
                    <select
                        id="filterUser"
                        v-model="filterUser"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        @change="applyFilters"
                    >
                        <option :value="null">Todos os utilizadores</option>
                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px] max-w-xs">
                    <label
                        for="filterEntity"
                        class="block text-xs text-gray-600 dark:text-gray-400 mb-1"
                    >
                        Entidade
                    </label>
                    <select
                        id="filterEntity"
                        v-model="filterEntity"
                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                        @change="applyFilters"
                    >
                        <option :value="null">Todas as entidades</option>
                        <option
                            v-for="entity in entities"
                            :key="entity.id"
                            :value="entity.id"
                        >
                            {{ entity.display_name }}
                        </option>
                    </select>
                </div>

                <Button
                    variant="outline"
                    size="sm"
                    @click="clearFilters"
                    class="mt-5"
                >
                    Limpar Filtros
                </Button>
            </div>
        </div>

        <!-- Calendar -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <div class="p-6">
                <FullCalendar ref="calendarRef" :options="calendarOptions" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* FullCalendar Dark Mode Overrides */
.dark .fc {
    --fc-border-color: rgb(55 65 81);
    --fc-bg-event-opacity: 0.9;
}

.dark .fc .fc-button-primary {
    background-color: rgb(37 99 235);
    border-color: rgb(37 99 235);
}

.dark .fc .fc-button-primary:not(:disabled):hover {
    background-color: rgb(29 78 216);
    border-color: rgb(29 78 216);
}

.dark .fc .fc-button-primary:disabled {
    background-color: rgb(55 65 81);
    border-color: rgb(55 65 81);
}

.dark .fc-theme-standard td,
.dark .fc-theme-standard th {
    border-color: rgb(55 65 81);
}

.dark .fc-theme-standard .fc-scrollgrid {
    border-color: rgb(55 65 81);
}

.dark .fc .fc-col-header-cell {
    background-color: rgb(31 41 55);
}

.dark .fc .fc-daygrid-day-number,
.dark .fc .fc-timegrid-slot-label,
.dark .fc .fc-list-day-cushion {
    color: rgb(209 213 219);
}

.dark .fc .fc-daygrid-day.fc-day-today {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>
