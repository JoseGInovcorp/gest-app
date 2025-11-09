<script setup>
import { ref, computed, inject } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import Table from "@/Components/ui/table/Table.vue";
import TableBody from "@/Components/ui/table/TableBody.vue";
import TableCell from "@/Components/ui/table/TableCell.vue";
import TableHead from "@/Components/ui/table/TableHead.vue";
import TableHeader from "@/Components/ui/table/TableHeader.vue";
import TableRow from "@/Components/ui/table/TableRow.vue";
import Input from "@/Components/ui/Input.vue";
import Badge from "@/Components/ui/Badge.vue";
import { Plus, Search, Pencil, Trash2, Percent } from "lucide-vue-next";

// Inject permission checker with fallback
const hasPermission = inject("hasPermission", () => () => false);

const props = defineProps({
    vatRates: Array,
    can: {
        type: Object,
        default: () => ({
            create: false,
            view: true,
            edit: false,
            delete: false,
        }),
    },
});

const searchQuery = ref("");

const filteredVatRates = computed(() => {
    if (!searchQuery.value) {
        return props.vatRates;
    }

    const query = searchQuery.value.toLowerCase();
    return props.vatRates.filter(
        (rate) =>
            rate.name.toLowerCase().includes(query) ||
            rate.rate.toString().includes(query)
    );
});

const deleteVatRate = (id) => {
    if (confirm("Tem certeza que deseja eliminar esta taxa de IVA?")) {
        router.delete(route("vat-rates.destroy", id));
    }
};
</script>

<template>
    <Head title="Taxas de IVA" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <Percent class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Taxas de IVA
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir taxas de IVA disponíveis no sistema
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
                    <span class="hover:text-gray-700 dark:hover:text-gray-200">
                        Configurações
                    </span>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Taxas de IVA</li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <!-- Toolbar -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between gap-4">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Pesquisar por nome ou taxa..."
                                class="pl-10"
                            />
                        </div>
                    </div>

                    <Link
                        v-if="can.create"
                        :href="route('vat-rates.create')"
                    >
                        <Button
                            class="bg-blue-600 hover:bg-blue-700 text-white"
                                >
                                    <Plus class="h-4 w-4 mr-2" />
                                    Adicionar Taxa IVA
                                </Button>
                            </Link>
                        </div>

                        <!-- Table -->
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Taxa (%)</TableHead>
                                        <TableHead>Padrão</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right"
                                            >Ações</TableHead
                                        >
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-if="filteredVatRates.length === 0"
                                    >
                                        <TableCell
                                            colspan="5"
                                            class="text-center py-8 text-gray-500"
                                        >
                                            Nenhuma taxa de IVA encontrada
                                        </TableCell>
                                    </TableRow>
                                    <TableRow
                                        v-for="rate in filteredVatRates"
                                        :key="rate.id"
                                    >
                                        <TableCell class="font-medium">
                                            {{ rate.name }}
                                        </TableCell>
                                        <TableCell>
                                            <span
                                                class="text-lg font-semibold text-blue-600 dark:text-blue-400"
                                            >
                                                {{ rate.rate }}%
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                v-if="rate.is_default"
                                                variant="default"
                                                class="bg-green-600"
                                            >
                                                Padrão
                                            </Badge>
                                            <span
                                                v-else
                                                class="text-gray-400 text-sm"
                                                >—</span
                                            >
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                :variant="
                                                    rate.active
                                                        ? 'default'
                                                        : 'destructive'
                                                "
                                            >
                                                {{
                                                    rate.active
                                                        ? "Ativo"
                                                        : "Inativo"
                                                }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div
                                                class="flex items-center justify-end gap-2"
                                            >
                                                <Link
                                                    v-if="can.edit"
                                                    :href="
                                                        route(
                                                            'vat-rates.edit',
                                                            rate.id
                                                        )
                                                    "
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        class="h-8 w-8 p-0"
                                                    >
                                                        <Pencil
                                                            class="h-4 w-4"
                                                        />
                                                    </Button>
                                                </Link>
                                                <Button
                                                    v-if="can.delete"
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50"
                                                    @click="
                                                        deleteVatRate(rate.id)
                                                    "
                                                >
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <!-- Info -->
                        <div
                            class="mt-4 text-sm text-gray-600 dark:text-gray-400"
                        >
                            <p>
                                Total:
                                <strong>{{ filteredVatRates.length }}</strong>
                                taxa(s) de IVA
                            </p>
                        </div>
                    </div>
                </div>
    </AuthenticatedLayout>
</template>
