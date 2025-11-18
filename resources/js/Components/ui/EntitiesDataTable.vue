<template>
    <DataTable
        :columns="columns"
        :data="entities.data"
        :pagination="entities"
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
                            placeholder="Pesquisar entidades..."
                            class="pl-8 w-64"
                            @input="debouncedSearch"
                        />
                    </div>
                    <select
                        v-model="activeFilter"
                        @change="applyFilters"
                        class="h-10 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-6 py-2 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Todos</option>
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <Button variant="outline" size="sm" @click="clearFilters">
                        <Filter class="h-4 w-4 mr-2" />
                        Limpar Filtros
                    </Button>
                    <Button @click="$emit('create')" v-if="canCreate">
                        <Plus class="h-4 w-4 mr-2" />
                        Novo {{ entityLabel }}
                    </Button>
                </div>
            </div>
        </template>

        <!-- NIF Column -->
        <template #cell-tax_number="{ item }">
            <div class="font-mono text-sm">
                {{ formatNif(item.tax_number) }}
            </div>
            <div
                class="flex items-center space-x-1 mt-1"
                v-if="item.vies_valid !== null"
            >
                <CheckCircle
                    v-if="item.vies_valid"
                    class="h-3 w-3 text-green-500"
                />
                <XCircle v-else class="h-3 w-3 text-red-500" />
                <span class="text-xs text-muted-foreground">
                    {{ item.vies_valid ? "VIES válido" : "VIES inválido" }}
                </span>
            </div>
        </template>

        <!-- Nome Column -->
        <template #cell-name="{ item }">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-3">
                    <div
                        class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center"
                    >
                        <span class="text-xs font-medium text-white">
                            {{ item.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="font-medium text-sm">{{ item.name }}</div>
                    <div class="text-xs text-muted-foreground">
                        #{{ item.number }}
                        <span
                            v-if="
                                item.country_code && item.country_code !== 'PT'
                            "
                            class="ml-2"
                        >
                            {{ item.country_code }}
                        </span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Telefone Column -->
        <template #cell-phone="{ item }">
            <div v-if="item.phone" class="text-sm">
                <a
                    :href="`tel:${item.phone}`"
                    class="text-blue-600 hover:text-blue-800"
                >
                    {{ formatPhone(item.phone) }}
                </a>
            </div>
            <div v-else class="text-muted-foreground text-sm">—</div>
        </template>

        <!-- Telemóvel Column -->
        <template #cell-mobile="{ item }">
            <div v-if="item.mobile" class="text-sm">
                <a
                    :href="`tel:${item.mobile}`"
                    class="text-blue-600 hover:text-blue-800"
                >
                    {{ formatPhone(item.mobile) }}
                </a>
            </div>
            <div v-else class="text-muted-foreground text-sm">—</div>
        </template>

        <!-- Website Column -->
        <template #cell-website="{ item }">
            <div v-if="item.website" class="text-sm">
                <a
                    :href="formatWebsiteUrl(item.website)"
                    target="_blank"
                    class="text-blue-600 hover:text-blue-800 flex items-center"
                >
                    <Globe class="h-3 w-3 mr-1" />
                    {{ formatWebsiteDisplay(item.website) }}
                </a>
            </div>
            <div v-else class="text-muted-foreground text-sm">—</div>
        </template>

        <!-- Email Column -->
        <template #cell-email="{ item }">
            <div v-if="item.email" class="text-sm">
                <a
                    :href="`mailto:${item.email}`"
                    class="text-blue-600 hover:text-blue-800"
                >
                    {{ item.email }}
                </a>
            </div>
            <div v-else class="text-muted-foreground text-sm">—</div>
        </template>

        <!-- Actions Column -->
        <template #cell-actions="{ item }">
            <div class="flex items-center space-x-2">
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0 text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-600"
                    @click="$emit('view', item)"
                    v-if="canView"
                >
                    <Eye class="h-4 w-4" />
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                    @click="$emit('edit', item)"
                    v-if="canEdit"
                >
                    <Edit class="h-4 w-4" />
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                    @click="$emit('delete', item)"
                    v-if="canDelete"
                >
                    <Trash2 class="h-4 w-4" />
                </Button>
            </div>
        </template>

        <!-- Empty State -->
        <template #empty>
            <div class="text-center py-8">
                <Users class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                <h3 class="text-lg font-medium text-foreground mb-2">
                    Nenhum {{ entityLabel.toLowerCase() }} encontrado
                </h3>
                <p class="text-muted-foreground mb-4">
                    Comece criando o primeiro {{ entityLabel.toLowerCase() }}.
                </p>
                <Button @click="$emit('create')" v-if="canCreate">
                    <Plus class="h-4 w-4 mr-2" />
                    Novo {{ entityLabel }}
                </Button>
            </div>
        </template>
    </DataTable>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import DataTable from "./DataTable.vue";
import Button from "./Button.vue";
import Input from "./Input.vue";
import {
    Search,
    Filter,
    Plus,
    Eye,
    Edit,
    Trash2,
    Users,
    CheckCircle,
    XCircle,
    Globe,
} from "lucide-vue-next";

const props = defineProps({
    entities: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    entityType: {
        type: String,
        required: true,
    },
    routePrefix: {
        type: String,
        required: true,
    },
    can: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["create", "view", "edit", "delete"]);

// Local state
const searchQuery = ref(props.filters?.search || "");
const activeFilter = ref(
    props.filters?.active !== undefined ? props.filters.active : ""
);

// Computed
const entityLabel = computed(() => {
    return props.entityType === "client" ? "Cliente" : "Fornecedor";
});

const canCreate = computed(() => props.can.create);
const canView = computed(() => props.can.view);
const canEdit = computed(() => props.can.edit);
const canDelete = computed(() => props.can.delete);

// Columns definition
const columns = computed(() => [
    {
        key: "tax_number",
        title: "NIF",
        sortable: true,
        class: "w-32",
    },
    {
        key: "name",
        title: "Nome",
        sortable: true,
        class: "min-w-48",
    },
    {
        key: "phone",
        title: "Telefone",
        sortable: false,
        class: "w-44",
    },
    {
        key: "mobile",
        title: "Telemóvel",
        sortable: false,
        class: "w-44",
    },
    {
        key: "website",
        title: "Website",
        sortable: false,
        class: "w-40",
    },
    {
        key: "email",
        title: "Email",
        sortable: true,
        class: "min-w-48",
    },
    {
        key: "actions",
        title: "Ações",
        sortable: false,
        class: "w-32",
    },
]);

// Debounced search
let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 500);
};

// Methods
const applyFilters = () => {
    const params = {};

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    if (activeFilter.value !== "") {
        params.active = activeFilter.value;
    }

    router.get(route(`${props.routePrefix}.index`), params, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    searchQuery.value = "";
    activeFilter.value = "";
    router.get(route(`${props.routePrefix}.index`));
};

const handleSort = (column, order) => {
    const params = { ...props.filters };
    params.sort = column;
    params.order = order;

    router.get(route(`${props.routePrefix}.index`), params, {
        preserveState: true,
        replace: true,
    });
};

const handlePageChange = (page) => {
    const params = { ...props.filters };
    params.page = page;

    router.get(route(`${props.routePrefix}.index`), params, {
        preserveState: true,
        replace: true,
    });
};

// Formatters
const formatNif = (nif) => {
    if (!nif) return "—";
    // Format NIF with spaces (e.g., 123 456 789)
    return nif.replace(/(\d{3})(\d{3})(\d{3})/, "$1 $2 $3");
};

const formatPhone = (phone) => {
    if (!phone) return "—";
    // Basic phone formatting
    return phone.replace(/(\+351)(\d{3})(\d{3})(\d{3})/, "$1 $2 $3 $4");
};

const formatWebsiteUrl = (website) => {
    if (!website) return "#";
    return website.startsWith("http") ? website : `https://${website}`;
};

const formatWebsiteDisplay = (website) => {
    if (!website) return "";
    return website.replace(/^https?:\/\//, "").replace(/\/$/, "");
};

// Watchers for auto-apply filters
watch(activeFilter, applyFilters);
</script>
