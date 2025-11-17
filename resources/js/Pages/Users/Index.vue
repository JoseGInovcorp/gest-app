<script setup>
import { ref, computed, inject } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Button from "@/Components/ui/Button.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
import Table from "@/Components/ui/table/Table.vue";
import TableBody from "@/Components/ui/table/TableBody.vue";
import TableCell from "@/Components/ui/table/TableCell.vue";
import TableHead from "@/Components/ui/table/TableHead.vue";
import TableHeader from "@/Components/ui/table/TableHeader.vue";
import TableRow from "@/Components/ui/table/TableRow.vue";
import Input from "@/Components/ui/Input.vue";
import Badge from "@/Components/ui/Badge.vue";
import { Plus, Search, Pencil, Trash2, Users } from "lucide-vue-next";

// Inject permission checker with fallback
const hasPermission = inject("hasPermission", () => () => false);

const props = defineProps({
    users: Array,
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

const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return props.users;
    }

    const query = searchQuery.value.toLowerCase();
    return props.users.filter(
        (user) =>
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query) ||
            (user.mobile && user.mobile.includes(query))
    );
});

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);

const confirmDelete = (id) => {
    itemToDelete.value = id;
    showDeleteDialog.value = true;
};

const deleteUser = () => {
    router.delete(route("users.destroy", itemToDelete.value), {
        onFinish: () => {
            showDeleteDialog.value = false;
            itemToDelete.value = null;
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    itemToDelete.value = null;
};
</script>

<template>
    <Head title="Utilizadores" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/20 rounded-lg">
                    <Users class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Utilizadores
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Gerir utilizadores do sistema
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
                        Gestão de Acessos
                    </span>
                </li>
                <li>/</li>
                <li class="text-gray-900 dark:text-white">Utilizadores</li>
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
                                placeholder="Pesquisar por nome, email ou telemóvel..."
                                class="pl-10"
                            />
                        </div>
                    </div>

                    <Link v-if="can.create" :href="route('users.create')">
                        <Button
                            class="ml-4 bg-blue-600 hover:bg-blue-700 text-white"
                        >
                            <Plus class="h-4 w-4 mr-2" />
                            Adicionar Utilizador
                        </Button>
                    </Link>
                </div>

                <!-- Table -->
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nome</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Telemóvel</TableHead>
                                <TableHead>Grupo</TableHead>
                                <TableHead>Estado</TableHead>
                                <TableHead class="text-right">Ações</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="filteredUsers.length === 0">
                                <TableCell
                                    colspan="6"
                                    class="text-center py-8 text-gray-500"
                                >
                                    Nenhum utilizador encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow
                                v-for="user in filteredUsers"
                                :key="user.id"
                            >
                                <TableCell class="font-medium">
                                    {{ user.name }}
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ user.email }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ user.mobile || "—" }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="default">
                                        {{ user.role || "Sem grupo" }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            user.active
                                                ? 'default'
                                                : 'destructive'
                                        "
                                    >
                                        {{ user.active ? "Ativo" : "Inativo" }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <Link
                                            v-if="can.edit"
                                            :href="route('users.edit', user.id)"
                                        >
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                class="h-8 w-8 p-0 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                                            >
                                                <Pencil class="h-4 w-4" />
                                            </Button>
                                        </Link>
                                        <Button
                                            v-if="can.delete"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50"
                                            @click="confirmDelete(user.id)"
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
                <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    <p>
                        Total:
                        <strong>{{ filteredUsers.length }}</strong>
                        utilizador(es)
                    </p>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            :show="showDeleteDialog"
            type="danger"
            title="Eliminar Utilizador"
            message="Tens a certeza que desejas eliminar este utilizador?"
            confirm-text="Eliminar"
            cancel-text="Cancelar"
            @confirm="deleteUser"
            @cancel="cancelDelete"
        />
    </AuthenticatedLayout>
</template>
