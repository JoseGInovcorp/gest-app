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

const deleteUser = (id) => {
    if (confirm("Tem certeza que deseja eliminar este utilizador?")) {
        router.delete(route("users.destroy", id));
    }
};
</script>

<template>
    <Head title="Utilizadores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Users class="h-8 w-8 text-primary" />
                <div>
                    <h2
                        class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        Utilizadores
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Gerir utilizadores do sistema
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
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
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

                            <Link
                                v-if="can.create"
                                :href="route('users.create')"
                            >
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
                                        <TableHead class="text-right"
                                            >Ações</TableHead
                                        >
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
                                                {{
                                                    user.active
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
                                                            'users.edit',
                                                            user.id
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
                                                    @click="deleteUser(user.id)"
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
                                <strong>{{ filteredUsers.length }}</strong>
                                utilizador(es)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
