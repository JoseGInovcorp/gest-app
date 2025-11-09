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
import { Plus, Search, Pencil, Trash2, UserCog } from "lucide-vue-next";

// Inject permission checker with fallback
const hasPermission = inject("hasPermission", () => () => false);

const props = defineProps({
    functions: Array,
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

const filteredFunctions = computed(() => {
    if (!searchQuery.value) {
        return props.functions;
    }

    const query = searchQuery.value.toLowerCase();
    return props.functions.filter(
        (func) =>
            func.name.toLowerCase().includes(query) ||
            (func.description && func.description.toLowerCase().includes(query))
    );
});

const deleteFunction = (id) => {
    if (confirm("Tem certeza que deseja eliminar esta função?")) {
        router.delete(route("contact-functions.destroy", id));
    }
};
</script>

<template>
    <Head title="Funções de Contactos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <UserCog class="h-8 w-8 text-primary" />
                <div>
                    <h2
                        class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        Funções de Contactos
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Gerir funções disponíveis para contactos
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                        placeholder="Pesquisar por nome ou descrição..."
                                        class="pl-10"
                                    />
                                </div>
                            </div>

                            <Link
                                v-if="can.create"
                                :href="route('contact-functions.create')"
                            >
                                <Button
                                    class="ml-4 bg-blue-600 hover:bg-blue-700 text-white"
                                >
                                    <Plus class="h-4 w-4 mr-2" />
                                    Adicionar Função
                                </Button>
                            </Link>
                        </div>

                        <!-- Table -->
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome</TableHead>
                                        <TableHead>Descrição</TableHead>
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right"
                                            >Ações</TableHead
                                        >
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-if="filteredFunctions.length === 0"
                                    >
                                        <TableCell
                                            colspan="4"
                                            class="text-center py-8 text-gray-500"
                                        >
                                            Nenhuma função encontrada
                                        </TableCell>
                                    </TableRow>
                                    <TableRow
                                        v-for="func in filteredFunctions"
                                        :key="func.id"
                                    >
                                        <TableCell class="font-medium">
                                            {{ func.name }}
                                        </TableCell>
                                        <TableCell>
                                            <span
                                                class="text-sm text-gray-600 dark:text-gray-400"
                                            >
                                                {{
                                                    func.description ||
                                                    "Sem descrição"
                                                }}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                :variant="
                                                    func.active
                                                        ? 'default'
                                                        : 'destructive'
                                                "
                                            >
                                                {{
                                                    func.active
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
                                                            'contact-functions.edit',
                                                            func.id
                                                        )
                                                    "
                                                >
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
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
                                                    @click="
                                                        deleteFunction(func.id)
                                                    "
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
                        <div
                            class="mt-6 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Total: {{ filteredFunctions.length }} função(ões)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
