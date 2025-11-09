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
import { Plus, Search, Pencil, Trash2, Globe } from "lucide-vue-next";

// Inject permission checker with fallback
const hasPermission = inject("hasPermission", () => () => false);

const props = defineProps({
    countries: Array,
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

const filteredCountries = computed(() => {
    if (!searchQuery.value) {
        return props.countries;
    }

    const query = searchQuery.value.toLowerCase();
    return props.countries.filter(
        (country) =>
            country.code.toLowerCase().includes(query) ||
            country.name.toLowerCase().includes(query) ||
            (country.name_en &&
                country.name_en.toLowerCase().includes(query)) ||
            (country.phone_prefix && country.phone_prefix.includes(query))
    );
});

const deleteCountry = (code) => {
    if (confirm("Tem certeza que deseja eliminar este país?")) {
        router.delete(route("countries.destroy", code));
    }
};
</script>

<template>
    <Head title="Países" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg">
                    <Globe
                        class="h-6 w-6 text-indigo-600 dark:text-indigo-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Países
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir países do sistema
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
                <li class="text-gray-900 dark:text-white">Países</li>
            </ol>
        </nav>

        <!-- Main Card -->
        <div
            class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
        >
            <div class="p-6">
                <!-- Header com Search e Botão Adicionar -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <Search
                                class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            />
                            <Input
                                v-model="searchQuery"
                                placeholder="Pesquisar por código, nome ou prefixo telefone..."
                                class="pl-10"
                            />
                        </div>
                    </div>

                    <Link v-if="can.create" :href="route('countries.create')">
                        <Button
                            class="ml-4 bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            <Plus class="h-4 w-4 mr-2" />
                            Adicionar País
                        </Button>
                    </Link>
                </div>

                <!-- Table -->
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Código</TableHead>
                                <TableHead>Nome</TableHead>
                                <TableHead>Nome (EN)</TableHead>
                                <TableHead>ISO3</TableHead>
                                <TableHead>Prefixo Tel.</TableHead>
                                <TableHead>VIES</TableHead>
                                <TableHead>Moeda</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="filteredCountries.length === 0">
                                <TableCell
                                    colspan="9"
                                    class="text-center py-8 text-gray-500"
                                >
                                    Nenhum país encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow
                                v-for="country in filteredCountries"
                                :key="country.code"
                            >
                                <TableCell class="font-medium">
                                    {{ country.code }}
                                </TableCell>
                                <TableCell>
                                    {{ country.name }}
                                </TableCell>
                                <TableCell>
                                    {{ country.name_en || "-" }}
                                </TableCell>
                                <TableCell>
                                    {{ country.iso3 || "-" }}
                                </TableCell>
                                <TableCell>
                                    {{
                                        country.phone_prefix
                                            ? `+${country.phone_prefix}`
                                            : "-"
                                    }}
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            country.vies_enabled
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{
                                            country.vies_enabled ? "Sim" : "Não"
                                        }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    {{ country.currency_code || "-" }}
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            country.active
                                                ? 'default'
                                                : 'destructive'
                                        "
                                    >
                                        {{
                                            country.active ? "Ativo" : "Inativo"
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
                                                    'countries.edit',
                                                    country.code
                                                )
                                            "
                                        >
                                            <Button variant="ghost" size="sm">
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="can.delete"
                                            variant="ghost"
                                            size="sm"
                                            @click="deleteCountry(country.code)"
                                        >
                                            <Trash2
                                                class="h-4 w-4 text-destructive"
                                            />
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Stats -->
                <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
                    Total: {{ filteredCountries.length }} país(es)
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
