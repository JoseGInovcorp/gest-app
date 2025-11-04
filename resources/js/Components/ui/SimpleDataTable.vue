<template>
    <div class="w-full">
        <!-- Toolbar -->
        <div class="flex items-center py-4">
            <Input
                placeholder="Filtrar contactos..."
                :model-value="search"
                @update:model-value="$emit('search', $event)"
                class="max-w-sm"
            />

            <div class="ml-auto flex items-center space-x-2">
                <slot name="toolbar" />
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            v-for="column in columns"
                            :key="column.key"
                            :class="column.className"
                        >
                            <Button
                                v-if="column.sortable !== false"
                                variant="ghost"
                                class="h-auto p-0 font-medium hover:bg-transparent"
                                @click="$emit('sort', column.key)"
                            >
                                {{ column.title }}
                                <ArrowUpDown class="ml-2 h-4 w-4" />
                            </Button>
                            <span v-else class="font-medium">{{
                                column.title
                            }}</span>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="data?.length">
                        <TableRow
                            v-for="row in data"
                            :key="row.id"
                            class="data-[state=selected]:bg-muted"
                        >
                            <TableCell
                                v-for="column in columns"
                                :key="`${row.id}-${column.key}`"
                                :class="column.className"
                            >
                                <slot
                                    :name="column.key"
                                    :row="row"
                                    :value="getNestedValue(row, column.key)"
                                >
                                    {{ getNestedValue(row, column.key) }}
                                </slot>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell
                            :colspan="columns.length"
                            class="h-24 text-center"
                        >
                            <slot name="empty">
                                Nenhum resultado encontrado.
                            </slot>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between space-x-2 py-4">
            <div class="flex-1 text-sm text-muted-foreground">
                <slot name="pagination-info" />
            </div>
            <div class="space-x-2">
                <slot name="pagination-controls" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ArrowUpDown } from "lucide-vue-next";
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Table from "@/Components/ui/table/Table.vue";
import TableBody from "@/Components/ui/table/TableBody.vue";
import TableCell from "@/Components/ui/table/TableCell.vue";
import TableHead from "@/Components/ui/table/TableHead.vue";
import TableHeader from "@/Components/ui/table/TableHeader.vue";
import TableRow from "@/Components/ui/table/TableRow.vue";

defineProps({
    data: {
        type: Array,
        default: () => [],
    },
    columns: {
        type: Array,
        required: true,
    },
    search: {
        type: String,
        default: "",
    },
});

defineEmits(["search", "sort"]);

// Helper function to get nested values from objects
function getNestedValue(obj, path) {
    if (!path) return obj;
    return path.split(".").reduce((current, key) => current?.[key], obj);
}
</script>
