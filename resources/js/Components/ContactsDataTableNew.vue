<template>
    <SimpleDataTable
        :data="filteredContacts"
        :columns="columns"
        :search="searchTerm"
        @search="searchTerm = $event"
        @sort="handleSort"
    >
        <!-- Toolbar slot -->
        <template #toolbar>
            <Badge variant="outline" class="text-sm">
                {{ filteredContacts.length }} contacto(s)
            </Badge>
            <Button
                v-if="canCreate"
                @click="$emit('create')"
                class="bg-blue-600 hover:bg-blue-700"
            >
                <Plus class="w-4 h-4 mr-2" />
                Novo Contacto
            </Button>
        </template>

        <!-- Custom cell renderers -->
        <template #entity="{ row }">
            {{ row.entity?.name || "-" }}
        </template>

        <template #actions="{ row }">
            <div class="flex items-center space-x-2">
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
            Mostrando {{ filteredContacts.length }} de
            {{ contacts.length }} contacto(s)
        </template>
    </SimpleDataTable>
</template>

<script setup>
import { ref, computed } from "vue";
import { Plus, Edit, Trash2 } from "lucide-vue-next";
import SimpleDataTable from "@/Components/ui/SimpleDataTable.vue";
import Button from "@/Components/ui/Button.vue";
import Badge from "@/Components/ui/Badge.vue";

const props = defineProps({
    contacts: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
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

const emit = defineEmits(["create", "edit", "delete"]);

const searchTerm = ref("");
const sortColumn = ref("");
const sortDirection = ref("asc");

// Definição das colunas seguindo o padrão Shadcn/ui
const columns = [
    {
        key: "nome",
        title: "Nome",
        sortable: true,
    },
    {
        key: "apelido",
        title: "Apelido",
        sortable: true,
    },
    {
        key: "funcao",
        title: "Função",
        sortable: true,
    },
    {
        key: "entity",
        title: "Entidade",
        sortable: true,
    },
    {
        key: "telefone",
        title: "Telefone",
        sortable: false,
    },
    {
        key: "telemovel",
        title: "Telemóvel",
        sortable: false,
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

// Contactos filtrados e ordenados
const filteredContacts = computed(() => {
    let filtered = props.contacts;

    // Aplicar pesquisa
    if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter((contact) => {
            return (
                contact.nome?.toLowerCase().includes(term) ||
                contact.apelido?.toLowerCase().includes(term) ||
                contact.funcao?.toLowerCase().includes(term) ||
                contact.entity?.name?.toLowerCase().includes(term) ||
                contact.email?.toLowerCase().includes(term) ||
                contact.telefone?.includes(term) ||
                contact.telemovel?.includes(term)
            );
        });
    }

    // Aplicar ordenação
    if (sortColumn.value) {
        filtered = [...filtered].sort((a, b) => {
            let aVal, bVal;

            if (sortColumn.value === "entity") {
                aVal = a.entity?.name || "";
                bVal = b.entity?.name || "";
            } else {
                aVal = a[sortColumn.value] || "";
                bVal = b[sortColumn.value] || "";
            }

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
