<template>
    <SimpleDataTable
        :data="filteredEntities"
        :columns="columns"
        :search="searchTerm"
        @search="searchTerm = $event"
        @sort="handleSort"
    >
        <!-- Toolbar slot -->
        <template #toolbar>
            <div class="flex items-center space-x-2">
                <Badge variant="outline" class="text-sm">
                    {{ filteredEntities.length }}
                    {{
                        entityType === "client"
                            ? "cliente(s)"
                            : "fornecedor(es)"
                    }}
                </Badge>
                <Button
                    @click="$emit('create')"
                    class="bg-blue-600 hover:bg-blue-700"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Novo
                    {{ entityType === "client" ? "Cliente" : "Fornecedor" }}
                </Button>
            </div>
        </template>

        <!-- Custom cell renderers -->
        <template #nif="{ row }">
            <div class="flex items-center space-x-2">
                <span class="font-mono">{{ row.nif || row.tax_number }}</span>
                <Badge
                    v-if="row.vies_valid !== null"
                    :variant="row.vies_valid ? 'success' : 'destructive'"
                    class="text-xs"
                >
                    {{ row.vies_valid ? "VIES ✓" : "VIES ✗" }}
                </Badge>
            </div>
        </template>

        <template #website="{ row }">
            <a
                v-if="row.website"
                :href="
                    row.website.startsWith('http')
                        ? row.website
                        : 'https://' + row.website
                "
                target="_blank"
                class="text-blue-600 hover:text-blue-800 underline"
            >
                {{ row.website }}
            </a>
            <span v-else class="text-gray-400">-</span>
        </template>

        <template #actions="{ row }">
            <div class="flex items-center space-x-2">
                <Button
                    v-if="canView"
                    variant="outline"
                    size="sm"
                    @click="$emit('view', row)"
                >
                    <Eye class="w-4 h-4" />
                </Button>
                <Button
                    v-if="canEdit"
                    variant="outline"
                    size="sm"
                    @click="$emit('edit', row)"
                >
                    <Edit class="w-4 h-4" />
                </Button>
                <Button
                    v-if="canDelete"
                    variant="destructive"
                    size="sm"
                    @click="$emit('delete', row)"
                >
                    <Trash2 class="w-4 h-4" />
                </Button>
            </div>
        </template>

        <!-- Pagination info -->
        <template #pagination-info>
            Mostrando {{ filteredEntities.length }} de
            {{ entities.length }} entidade(s)
        </template>
    </SimpleDataTable>
</template>

<script setup>
import { ref, computed } from "vue";
import { Plus, Eye, Edit, Trash2 } from "lucide-vue-next";
import SimpleDataTable from "@/Components/ui/SimpleDataTable.vue";
import Button from "@/Components/ui/Button.vue";
import Badge from "@/Components/ui/Badge.vue";

const props = defineProps({
    entities: {
        type: Array,
        default: () => [],
    },
    entityType: {
        type: String,
        required: true,
        validator: (value) => ["client", "supplier"].includes(value),
    },
    canView: {
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

const emit = defineEmits(["create", "view", "edit", "delete"]);

const searchTerm = ref("");
const sortColumn = ref("");
const sortDirection = ref("asc");

// Definição das colunas seguindo o padrão Shadcn/ui
// Colunas conforme especificação: NIF, Nome, Telefone, Telemóvel, Website, Email
const columns = [
    {
        key: "nif",
        title: "NIF",
        sortable: true,
        className: "w-[150px]",
    },
    {
        key: "name",
        title: "Nome",
        sortable: true,
    },
    {
        key: "phone",
        title: "Telefone",
        sortable: false,
        className: "w-[120px]",
    },
    {
        key: "mobile",
        title: "Telemóvel",
        sortable: false,
        className: "w-[120px]",
    },
    {
        key: "website",
        title: "Website",
        sortable: false,
        className: "w-[150px]",
    },
    {
        key: "email",
        title: "Email",
        sortable: true,
    },
    {
        key: "actions",
        title: "Ações",
        sortable: false,
        className: "w-[120px]",
    },
];

// Entidades filtradas e ordenadas
const filteredEntities = computed(() => {
    let filtered = props.entities;

    // Aplicar pesquisa
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter((entity) => {
            return (
                entity.nif?.toLowerCase().includes(term) ||
                entity.name?.toLowerCase().includes(term) ||
                entity.phone?.includes(term) ||
                entity.mobile?.includes(term) ||
                entity.website?.toLowerCase().includes(term) ||
                entity.email?.toLowerCase().includes(term)
            );
        });
    }

    // Aplicar ordenação
    if (sortColumn.value) {
        filtered = [...filtered].sort((a, b) => {
            let aVal, bVal;

            if (sortColumn.value === "country") {
                aVal = a.country?.name || "";
                bVal = b.country?.name || "";
            } else if (sortColumn.value === "status") {
                aVal = a.active ? "Ativo" : "Inativo";
                bVal = b.active ? "Ativo" : "Inativo";
            } else {
                aVal = a[sortColumn.value] || "";
                bVal = b[sortColumn.value] || "";
            }

            // Convert to string for comparison
            aVal = aVal.toString();
            bVal = bVal.toString();

            if (sortDirection.value === "asc") {
                return aVal.localeCompare(bVal);
            } else {
                return bVal.localeCompare(aVal);
            }
        });
    }

    return filtered;
});

// Funções
const handleSort = (column) => {
    if (sortColumn.value === column) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortColumn.value = column;
        sortDirection.value = "asc";
    }
};
</script>
