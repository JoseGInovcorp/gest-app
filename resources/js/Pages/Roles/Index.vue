<script setup>
import { ref, computed } from "vue";
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
import { Plus, Search, Pencil, Trash2, Shield } from "lucide-vue-next";

const props = defineProps({
    roles: Array,
});

const searchQuery = ref("");

const filteredRoles = computed(() => {
    if (!searchQuery.value) {
        return props.roles;
    }

    const query = searchQuery.value.toLowerCase();
    return props.roles.filter((role) =>
        role.name.toLowerCase().includes(query)
    );
});

const deleteRole = (id) => {
    if (confirm("Tem certeza que deseja eliminar este grupo de permissões?")) {
        router.delete(route("roles.destroy", id));
    }
};
</script>

<template>
    <Head title="Grupos de Permissões" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Shield class="h-8 w-8 text-primary" />
                <div>
                    <h2
                        class="text-2xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        Grupos de Permissões
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Gerir grupos de permissões e acessos
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
                                        placeholder="Pesquisar por nome..."
                                        class="pl-10"
                                    />
                                </div>
                            </div>

                            <Link :href="route('roles.create')">
                                <Button
                                    class="ml-4 bg-blue-600 hover:bg-blue-700 text-white"
                                >
                                    <Plus class="h-4 w-4 mr-2" />
                                    Adicionar Grupo
                                </Button>
                            </Link>
                        </div>

                        <!-- Table -->
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nome do Grupo</TableHead>
                                        <TableHead
                                            >Utilizadores
                                            Relacionados</TableHead
                                        >
                                        <TableHead>Estado</TableHead>
                                        <TableHead class="text-right"
                                            >Ações</TableHead
                                        >
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-if="filteredRoles.length === 0">
                                        <TableCell
                                            colspan="4"
                                            class="text-center py-8 text-gray-500"
                                        >
                                            Nenhum grupo de permissões
                                            encontrado
                                        </TableCell>
                                    </TableRow>
                                    <TableRow
                                        v-for="role in filteredRoles"
                                        :key="role.id"
                                    >
                                        <TableCell class="font-medium">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <Shield
                                                    class="h-4 w-4 text-blue-600"
                                                />
                                                {{ role.name }}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="default">
                                                {{ role.users_count }}
                                                utilizador(es)
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                :variant="
                                                    role.active
                                                        ? 'success'
                                                        : 'secondary'
                                                "
                                            >
                                                {{
                                                    role.active
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
                                                    :href="
                                                        route(
                                                            'roles.edit',
                                                            role.id
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
                                                    variant="ghost"
                                                    size="sm"
                                                    class="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50"
                                                    @click="deleteRole(role.id)"
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
                                <strong>{{ filteredRoles.length }}</strong>
                                grupo(s) de permissões
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
