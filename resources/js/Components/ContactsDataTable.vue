<template>
    <div class="w-full">
        <!-- Toolbar -->
        <div class="flex items-center justify-between py-4">
            <div class="flex items-center space-x-2">
                <!-- Search -->
                <div class="relative">
                    <Search
                        class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground"
                    />
                    <Input
                        :model-value="filters.search"
                        @update:model-value="updateFilter('search', $event)"
                        placeholder="Pesquisar contactos..."
                        class="pl-8 w-64"
                    />
                </div>

                <!-- Status Filter -->
                <Select
                    :model-value="filters.status || 'all'"
                    @update:model-value="updateFilter('status', $event)"
                >
                    <option value="all">Todos os estados</option>
                    <option value="active">Ativos</option>
                    <option value="inactive">Inativos</option>
                </Select>

                <!-- Entity Filter -->
                <Select
                    :model-value="filters.entity_id || ''"
                    @update:model-value="updateFilter('entity_id', $event)"
                >
                    <option value="">Todas as entidades</option>
                    <option
                        v-for="entity in entities"
                        :key="entity.id"
                        :value="entity.id"
                    >
                        {{ entity.name }}
                    </option>
                </Select>

                <!-- Clear Filters -->
                <Button
                    v-if="hasFilters"
                    variant="outline"
                    size="sm"
                    @click="clearFilters"
                >
                    <X class="h-4 w-4 mr-1" />
                    Limpar
                </Button>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
                <Button v-if="canCreate" @click="$emit('create')">
                    <Plus class="h-4 w-4 mr-2" />
                    Novo Contacto
                </Button>
            </div>
        </div>

        <!-- Data Table -->
        <DataTable
            :data="contacts.data"
            :columns="columns"
            :loading="loading"
            :sort-by="sortBy"
            :sort-direction="sortDirection"
            @sort="handleSort"
        >
            <!-- Nome column -->
            <template #first_name="{ row }">
                <div class="flex items-center space-x-2">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-green-600 text-xs font-medium text-white"
                    >
                        {{ getInitials(row.first_name, row.last_name) }}
                    </div>
                    <div>
                        <div class="font-medium">{{ row.first_name }}</div>
                        <div class="text-xs text-muted-foreground">
                            #{{ row.number }}
                        </div>
                    </div>
                </div>
            </template>

            <!-- Apelido column -->
            <template #last_name="{ row }">
                <div class="font-medium">{{ row.last_name }}</div>
            </template>

            <!-- Função column -->
            <template #function="{ row }">
                <Badge v-if="row.function" variant="secondary" class="text-xs">
                    {{ row.function }}
                </Badge>
                <span v-else class="text-muted-foreground text-sm">-</span>
            </template>

            <!-- Entidade column -->
            <template #entity="{ row }">
                <div v-if="row.entity">
                    <div class="font-medium text-sm">{{ row.entity.name }}</div>
                    <Badge
                        :variant="getEntityBadgeVariant(row.entity.type)"
                        class="text-xs mt-1"
                    >
                        {{ getEntityTypeLabel(row.entity.type) }}
                    </Badge>
                </div>
                <span v-else class="text-muted-foreground">-</span>
            </template>

            <!-- Telefone column -->
            <template #phone="{ row }">
                <a
                    v-if="row.phone"
                    :href="`tel:${row.phone}`"
                    class="flex items-center text-blue-600 hover:text-blue-800 text-sm"
                >
                    <Phone class="h-3 w-3 mr-1" />
                    {{ formatPhone(row.phone) }}
                </a>
                <span v-else class="text-muted-foreground text-sm">-</span>
            </template>

            <!-- Telemóvel column -->
            <template #mobile="{ row }">
                <a
                    v-if="row.mobile"
                    :href="`tel:${row.mobile}`"
                    class="flex items-center text-green-600 hover:text-green-800 text-sm"
                >
                    <Smartphone class="h-3 w-3 mr-1" />
                    {{ formatPhone(row.mobile) }}
                </a>
                <span v-else class="text-muted-foreground text-sm">-</span>
            </template>

            <!-- Email column -->
            <template #email="{ row }">
                <a
                    v-if="row.email"
                    :href="`mailto:${row.email}`"
                    class="flex items-center text-purple-600 hover:text-purple-800 text-sm"
                >
                    <Mail class="h-3 w-3 mr-1" />
                    {{ row.email }}
                </a>
                <span v-else class="text-muted-foreground text-sm">-</span>
            </template>

            <!-- Status column (hidden, just for reference) -->
            <template #status="{ row }">
                <Badge
                    :variant="row.status === 'active' ? 'default' : 'secondary'"
                >
                    {{ row.status === "active" ? "Ativo" : "Inativo" }}
                </Badge>
            </template>

            <!-- Ações column -->
            <template #actions="{ row }">
                <div class="flex items-center space-x-1">
                    <Button
                        v-if="canView"
                        variant="ghost"
                        size="sm"
                        @click="$emit('view', row)"
                        class="h-8 w-8 p-0"
                    >
                        <Eye class="h-4 w-4" />
                    </Button>
                    <Button
                        v-if="canEdit"
                        variant="ghost"
                        size="sm"
                        @click="$emit('edit', row)"
                        class="h-8 w-8 p-0"
                    >
                        <Pencil class="h-4 w-4" />
                    </Button>
                    <Button
                        v-if="canDelete"
                        variant="ghost"
                        size="sm"
                        @click="$emit('delete', row)"
                        class="h-8 w-8 p-0 text-destructive hover:text-destructive"
                    >
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </template>
        </DataTable>

        <!-- Pagination -->
        <div class="flex items-center justify-between space-x-2 py-4">
            <div class="text-sm text-muted-foreground">
                Mostrando {{ contacts.from || 0 }} a {{ contacts.to || 0 }} de
                {{ contacts.total || 0 }} resultados
            </div>
            <div class="flex items-center space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!contacts.prev_page_url"
                    @click="$emit('page', contacts.current_page - 1)"
                >
                    Anterior
                </Button>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!contacts.next_page_url"
                    @click="$emit('page', contacts.current_page + 1)"
                >
                    Próxima
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import {
    Search,
    Plus,
    X,
    Eye,
    Pencil,
    Trash2,
    Phone,
    Smartphone,
    Mail,
} from "lucide-vue-next";
import DataTable from "@/Components/ui/DataTable.vue";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Badge from "@/Components/ui/Badge.vue";

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
    loading: {
        type: Boolean,
        default: false,
    },
    sortBy: {
        type: String,
        default: "first_name",
    },
    sortDirection: {
        type: String,
        default: "asc",
    },
    canCreate: {
        type: Boolean,
        default: false,
    },
    canView: {
        type: Boolean,
        default: true,
    },
    canEdit: {
        type: Boolean,
        default: false,
    },
    canDelete: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    "filter",
    "sort",
    "page",
    "create",
    "view",
    "edit",
    "delete",
]);

// Colunas da tabela conforme especificação
const columns = [
    {
        key: "first_name",
        label: "Nome",
        sortable: true,
        width: "15%",
    },
    {
        key: "last_name",
        label: "Apelido",
        sortable: true,
        width: "15%",
    },
    {
        key: "function",
        label: "Função",
        sortable: true,
        width: "12%",
    },
    {
        key: "entity",
        label: "Entidade",
        sortable: false,
        width: "20%",
    },
    {
        key: "phone",
        label: "Telefone",
        sortable: false,
        width: "12%",
    },
    {
        key: "mobile",
        label: "Telemóvel",
        sortable: false,
        width: "12%",
    },
    {
        key: "email",
        label: "Email",
        sortable: true,
        width: "14%",
    },
    {
        key: "actions",
        label: "Ações",
        sortable: false,
        width: "8%",
    },
];

// Computed properties
const hasFilters = computed(() => {
    return (
        props.filters.search ||
        (props.filters.status && props.filters.status !== "all") ||
        props.filters.entity_id
    );
});

// Methods
const updateFilter = (key, value) => {
    emit("filter", { ...props.filters, [key]: value });
};

const clearFilters = () => {
    emit("filter", {});
};

const handleSort = (column) => {
    if (!column.sortable) return;

    const direction =
        props.sortBy === column.key && props.sortDirection === "asc"
            ? "desc"
            : "asc";
    emit("sort", { column: column.key, direction });
};

const getInitials = (firstName, lastName) => {
    return `${firstName?.[0] || ""}${lastName?.[0] || ""}`.toUpperCase();
};

const formatPhone = (phone) => {
    if (!phone) return "";

    // Formato português: +351 XXX XXX XXX
    const cleaned = phone.replace(/\D/g, "");
    if (cleaned.startsWith("351")) {
        return `+351 ${cleaned.slice(3, 6)} ${cleaned.slice(
            6,
            9
        )} ${cleaned.slice(9)}`;
    }
    return phone;
};

const getEntityTypeLabel = (type) => {
    const labels = {
        client: "Cliente",
        supplier: "Fornecedor",
        both: "Cliente/Fornecedor",
    };
    return labels[type] || "N/D";
};

const getEntityBadgeVariant = (type) => {
    const variants = {
        client: "default",
        supplier: "secondary",
        both: "outline",
    };
    return variants[type] || "secondary";
};
</script>
