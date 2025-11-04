<template>
    <div class="w-full">
        <!-- Header com filtros -->
        <div
            class="flex items-center justify-between py-4"
            v-if="$slots.toolbar"
        >
            <slot name="toolbar" />
        </div>

        <!-- Tabela -->
        <div class="rounded-md border">
            <table class="w-full">
                <!-- Header -->
                <thead>
                    <tr class="border-b bg-muted/50 hover:bg-muted/50">
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0',
                                column.class || '',
                            ]"
                        >
                            <div class="flex items-center space-x-2">
                                <span>{{ column.title }}</span>
                                <!-- Sorting indicator -->
                                <button
                                    v-if="column.sortable"
                                    @click="toggleSort(column.key)"
                                    class="ml-2 h-4 w-4"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </th>
                    </tr>
                </thead>

                <!-- Body -->
                <tbody>
                    <!-- Loading state -->
                    <tr v-if="loading" v-for="n in 5" :key="n">
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="p-4 align-middle"
                        >
                            <div
                                class="h-4 bg-muted animate-pulse rounded"
                            ></div>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-else-if="!data.length">
                        <td :colspan="columns.length" class="h-24 text-center">
                            <div
                                class="flex flex-col items-center justify-center space-y-2"
                            >
                                <slot name="empty">
                                    <div class="text-muted-foreground">
                                        Nenhum resultado encontrado.
                                    </div>
                                </slot>
                            </div>
                        </td>
                    </tr>

                    <!-- Data rows -->
                    <tr
                        v-else
                        v-for="(item, index) in data"
                        :key="getRowKey(item, index)"
                        class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            :class="[
                                'p-4 align-middle [&:has([role=checkbox])]:pr-0',
                                column.cellClass || '',
                            ]"
                        >
                            <!-- Custom slot for column -->
                            <slot
                                :name="`cell-${column.key}`"
                                :item="item"
                                :value="getCellValue(item, column.key)"
                            >
                                {{ getCellValue(item, column.key) }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            class="flex items-center justify-between px-2 py-4"
            v-if="pagination"
        >
            <div class="flex-1 text-sm text-muted-foreground">
                {{ getPaginationText() }}
            </div>
            <div class="flex items-center space-x-2">
                <slot name="pagination" :pagination="pagination">
                    <!-- Pagination controls -->
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!pagination.prev_page_url"
                        @click="
                            $emit('page-change', pagination.current_page - 1)
                        "
                    >
                        Anterior
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!pagination.next_page_url"
                        @click="
                            $emit('page-change', pagination.current_page + 1)
                        "
                    >
                        Próxima
                    </Button>
                </slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import Button from "./Button.vue";

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    pagination: {
        type: Object,
        default: null,
    },
    rowKey: {
        type: [String, Function],
        default: "id",
    },
    sortBy: {
        type: String,
        default: null,
    },
    sortOrder: {
        type: String,
        default: "asc",
    },
});

const emit = defineEmits(["sort-change", "page-change"]);

const getRowKey = (item, index) => {
    if (typeof props.rowKey === "function") {
        return props.rowKey(item, index);
    }
    return item[props.rowKey] || index;
};

const getCellValue = (item, key) => {
    return key.split(".").reduce((obj, prop) => obj?.[prop], item);
};

const toggleSort = (column) => {
    if (props.sortBy === column) {
        const newOrder = props.sortOrder === "asc" ? "desc" : "asc";
        emit("sort-change", column, newOrder);
    } else {
        emit("sort-change", column, "asc");
    }
};

const getPaginationText = () => {
    if (!props.pagination) return "";

    const { from, to, total } = props.pagination;
    return `Mostrando ${from} a ${to} de ${total} resultados`;
};
</script>
