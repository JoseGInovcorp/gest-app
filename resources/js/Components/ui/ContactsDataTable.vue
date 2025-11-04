<template>
    <DataTable
        :columns="columns"
        :data="contacts.data"
        :pagination="contacts"
        :loading="loading"
        @sort-change="handleSort"
        @page-change="handlePageChange"
    >
        <!-- Toolbar -->
        <template #toolbar>
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <Search
                            class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground"
                        />
                        <Input
                            v-model="searchQuery"
                            placeholder="Pesquisar contactos..."
                            class="pl-8 w-64"
                            @input="debouncedSearch"
                        />
                    </div>
                    <select
                        v-model="statusFilter"
                        @change="applyFilters"
                        class="rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
                    >
                        <option value="all">Todos</option>
                        <option value="active">Ativos</option>
                        <option value="inactive">Inativos</option>
                    </select>
                    <select
                        v-model="entityFilter"
                        @change="applyFilters"
                        class="rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm"
                    >
                        <option value="">Todas as Entidades</option>
                        <option
                            v-for="entity in entities"
                            :key="entity.id"
                            :value="entity.id"
                        >
                            {{ entity.name }}
                        </option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <Button variant="outline" size="sm" @click="clearFilters">
                        <Filter class="h-4 w-4 mr-2" />
                        Limpar Filtros
                    </Button>
                    <Button @click="$emit('create')" v-if="canCreate">
                        <Plus class="h-4 w-4 mr-2" />
                        Novo Contacto
                    </Button>
                </div>
            </div>
        </template>

        <!-- Nome Column -->
        <template #cell-first_name="{ item }">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-3">
                    <div
                        class="h-8 w-8 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center"
                    >
                        <span class="text-xs font-medium text-white">
                            {{ item.first_name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="font-medium text-sm">{{ item.first_name }}</div>
                    <div class="text-xs text-muted-foreground">
                        #{{ item.number }}
                        <span
                            v-if="item.rgpd_consent"
                            class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                        >
                            RGPD ✓
                        </span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Apelido Column -->
        <template #cell-last_name="{ item }">
            <div class="font-medium text-sm">{{ item.last_name }}</div>
        </template>

        <!-- Função Column -->
        <template #cell-function="{ item }">
            <div v-if="item.function" class="text-sm">
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                >
                    {{ item.function }}
                </span>
            </div>
            <div v-else class="text-sm text-muted-foreground">—</div>
        </template>

        <!-- Entidade Column -->
        <template #cell-entity_name="{ item }">
            <div class="text-sm">
                <div class="font-medium">{{ item.entity?.name }}</div>
                <div class="text-xs text-muted-foreground">
                    {{ getEntityTypeLabel(item.entity?.type) }}
                </div>
            </div>
        </template>

        <!-- Telefone Column -->
        <template #cell-phone="{ item }">
            <div v-if="item.phone">
                <a
                    :href="`tel:${item.phone}`"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
                >
                    <Phone class="h-3 w-3 mr-1" />
                    {{ formatPhone(item.phone) }}
                </a>
            </div>
            <div v-else class="text-sm text-muted-foreground">—</div>
        </template>

        <!-- Telemóvel Column -->
        <template #cell-mobile="{ item }">
            <div v-if="item.mobile">
                <a
                    :href="`tel:${item.mobile}`"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
                >
                    <Smartphone class="h-3 w-3 mr-1" />
                    {{ formatPhone(item.mobile) }}
                </a>
            </div>
            <div v-else class="text-sm text-muted-foreground">—</div>
        </template>

        <!-- Email Column -->
        <template #cell-email="{ item }">
            <div v-if="item.email">
                <a
                    :href="`mailto:${item.email}`"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center"
                >
                    <Mail class="h-3 w-3 mr-1" />
                    {{ item.email }}
                </a>
            </div>
            <div v-else class="text-sm text-muted-foreground">—</div>
        </template>

        <!-- Actions Column -->
        <template #cell-actions="{ item }">
            <div class="flex items-center space-x-1">
                <Button
                    variant="ghost"
                    size="sm"
                    @click="$emit('view', item.id)"
                    v-if="canView"
                >
                    <Eye class="h-4 w-4" />
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    @click="$emit('edit', item.id)"
                    v-if="canEdit"
                >
                    <Edit2 class="h-4 w-4" />
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    @click="$emit('delete', item.id)"
                    v-if="canDelete"
                    class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-950"
                >
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </template>

        <!-- Empty State -->
        <template #empty>
            <div class="text-center py-12">
                <Users class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                <h3
                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                    Nenhum contacto encontrado
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Comece por criar o primeiro contacto.
                </p>
                <div class="mt-6">
                    <Button @click="$emit('create')" v-if="canCreate">
                        <Plus class="h-4 w-4 mr-2" />
                        Novo Contacto
                    </Button>
                </div>
            </div>
        </template>
    </DataTable>
</template>

<script setup>
import { ref, computed, watchEffect } from "vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import DataTable from "./DataTable.vue";
import Button from "./Button.vue";
import Input from "./Input.vue";
import {
    Search,
    Filter,
    Plus,
    Eye,
    Edit2,
    Trash2,
    Users,
    Phone,
    Smartphone,
    Mail,
} from "lucide-vue-next";

const props = defineProps({
    contacts: {
        type: Object,
        required: true,
    },
    entities: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    canView: {
        type: Boolean,
        default: true,
    },
    canCreate: {
        type: Boolean,
        default: true,
    },
    canEdit: {
        type: Boolean,
        default: true,
    },
    canDelete: {
        type: Boolean,
        default: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["view", "edit", "delete", "create"]);

// Definição das colunas
const columns = computed(() => [
    {
        key: "first_name",
        label: "Nome",
        sortable: true,
        class: "min-w-0 w-1/6",
    },
    {
        key: "last_name",
        label: "Apelido",
        sortable: true,
        class: "min-w-0 w-1/6",
    },
    {
        key: "function",
        label: "Função",
        sortable: true,
        class: "min-w-0 w-1/6",
    },
    {
        key: "entity_name",
        label: "Entidade",
        sortable: false,
        class: "min-w-0 w-1/6",
    },
    {
        key: "phone",
        label: "Telefone",
        sortable: false,
        class: "min-w-0 w-1/6",
    },
    {
        key: "mobile",
        label: "Telemóvel",
        sortable: false,
        class: "min-w-0 w-1/6",
    },
    {
        key: "email",
        label: "Email",
        sortable: true,
        class: "min-w-0 w-1/6",
    },
    {
        key: "actions",
        label: "Ações",
        sortable: false,
        class: "w-24 text-right",
    },
]);

// Filtros
const searchQuery = ref(props.filters.search || "");
const statusFilter = ref(props.filters.status || "all");
const entityFilter = ref(props.filters.entity_id || "");

// Debounced search
const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

// Aplicar filtros
const applyFilters = () => {
    const params = {};

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (statusFilter.value && statusFilter.value !== "all") {
        params.status = statusFilter.value;
    }

    if (entityFilter.value) {
        params.entity_id = entityFilter.value;
    }

    router.get(route("contacts.index"), params, {
        preserveState: true,
        replace: true,
    });
};

// Limpar filtros
const clearFilters = () => {
    searchQuery.value = "";
    statusFilter.value = "all";
    entityFilter.value = "";

    router.get(
        route("contacts.index"),
        {},
        {
            preserveState: true,
            replace: true,
        }
    );
};

// Handle sorting
const handleSort = ({ column, direction }) => {
    const params = {
        ...props.filters,
        sort: column,
        direction: direction,
    };

    router.get(route("contacts.index"), params, {
        preserveState: true,
        replace: true,
    });
};

// Handle pagination
const handlePageChange = (page) => {
    const params = {
        ...props.filters,
        page: page,
    };

    router.get(route("contacts.index"), params, {
        preserveState: true,
        replace: true,
    });
};

// Formatação de telefone
const formatPhone = (phone) => {
    if (!phone) return "";

    // Remove todos os caracteres não numéricos
    const cleaned = phone.replace(/\D/g, "");

    // Formatar número português (+351)
    if (cleaned.startsWith("351") && cleaned.length === 12) {
        return `+351 ${cleaned.slice(3, 6)} ${cleaned.slice(
            6,
            9
        )} ${cleaned.slice(9)}`;
    }

    // Formato genérico
    if (cleaned.length === 9) {
        return `${cleaned.slice(0, 3)} ${cleaned.slice(3, 6)} ${cleaned.slice(
            6
        )}`;
    }

    return phone;
};

// Obter label do tipo de entidade
const getEntityTypeLabel = (type) => {
    const labels = {
        client: "Cliente",
        supplier: "Fornecedor",
        both: "Cliente/Fornecedor",
    };
    return labels[type] || "N/D";
};
</script>
