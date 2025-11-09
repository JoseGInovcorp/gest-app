<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Table from "@/Components/ui/table/Table.vue";
import TableHeader from "@/Components/ui/table/TableHeader.vue";
import TableBody from "@/Components/ui/table/TableBody.vue";
import TableRow from "@/Components/ui/table/TableRow.vue";
import TableHead from "@/Components/ui/table/TableHead.vue";
import TableCell from "@/Components/ui/table/TableCell.vue";
import Badge from "@/Components/ui/Badge.vue";
import Input from "@/Components/ui/Input.vue";
import { FileText, Search } from "lucide-vue-next";

const props = defineProps({
    activities: Object,
    filters: Object,
});

// Search
const search = ref(props.filters.search || "");

const performSearch = () => {
    router.get(
        route("logs.index"),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        }
    );
};

// Format date and time
const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("pt-PT");
};

const formatTime = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleTimeString("pt-PT", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Get module label
const getModuleLabel = (subjectType) => {
    const labels = {
        Entity: "Entidades",
        Contact: "Contactos",
        Article: "Artigos",
        Country: "Países",
        ContactFunction: "Funções Contacto",
        VatRate: "Taxas IVA",
        User: "Utilizadores",
        Role: "Permissões",
    };
    return labels[subjectType] || subjectType || "-";
};

// Get action label
const getActionLabel = (event) => {
    const labels = {
        created: "Criado",
        updated: "Atualizado",
        deleted: "Eliminado",
        login: "Login",
        logout: "Logout",
        test: "Teste",
    };
    return labels[event] || event || "-";
};

// Get action variant
const getActionVariant = (event) => {
    const variants = {
        created: "success",
        updated: "default",
        deleted: "destructive",
        login: "default",
        logout: "secondary",
        test: "secondary",
    };
    return variants[event] || "secondary";
};

// Get device info
const getDeviceInfo = (userAgent) => {
    if (!userAgent) return "-";

    if (
        userAgent.includes("Mobile") ||
        userAgent.includes("Android") ||
        userAgent.includes("iPhone")
    ) {
        return "Mobile";
    } else if (userAgent.includes("Tablet") || userAgent.includes("iPad")) {
        return "Tablet";
    }
    return "Desktop";
};
</script>

<template>
    <Head title="Logs de Atividade" />

    <AuthenticatedLayout>
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <FileText
                        class="h-6 w-6 text-purple-600 dark:text-purple-400"
                    />
                </div>
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Logs de Atividade
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Histórico de ações realizadas no sistema
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
                <li class="text-gray-900 dark:text-white">Logs de Atividade</li>
            </ol>
        </nav>

        <div class="space-y-6">
            <!-- Filtros -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6"
            >
                <div class="flex items-center space-x-4">
                    <div class="relative flex-1">
                        <Search
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                        />
                        <Input
                            v-model="search"
                            type="text"
                            placeholder="Pesquisar por utilizador, ação ou módulo..."
                            class="pl-10"
                            @keyup.enter="performSearch"
                        />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div
                class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900"
            >
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Data</TableHead>
                                <TableHead>Hora</TableHead>
                                <TableHead>Utilizador</TableHead>
                                <TableHead>Menu</TableHead>
                                <TableHead>Ação</TableHead>
                                <TableHead>Dispositivo</TableHead>
                                <TableHead>IP</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="activities.data.length === 0">
                                <TableCell
                                    colspan="7"
                                    class="text-center py-8 text-gray-500"
                                >
                                    Nenhum log de atividade encontrado
                                </TableCell>
                            </TableRow>
                            <TableRow
                                v-for="activity in activities.data"
                                :key="activity.id"
                            >
                                <TableCell class="font-medium">
                                    {{ formatDate(activity.created_at) }}
                                </TableCell>
                                <TableCell>
                                    {{ formatTime(activity.created_at) }}
                                </TableCell>
                                <TableCell>
                                    <div
                                        v-if="activity.causer"
                                        class="flex flex-col"
                                    >
                                        <span
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ activity.causer.name }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ activity.causer.email }}
                                        </span>
                                    </div>
                                    <span v-else class="text-gray-500"
                                        >Sistema</span
                                    >
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="text-gray-700 dark:text-gray-300"
                                    >
                                        {{
                                            getModuleLabel(
                                                activity.subject_type
                                            )
                                        }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <Badge
                                        :variant="
                                            getActionVariant(activity.event)
                                        "
                                    >
                                        {{ getActionLabel(activity.event) }}
                                    </Badge>
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ getDeviceInfo(activity.user_agent) }}
                                    </span>
                                </TableCell>
                                <TableCell>
                                    <span
                                        class="text-sm text-gray-600 dark:text-gray-400 font-mono"
                                    >
                                        {{ activity.ip_address || "-" }}
                                    </span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="activities.links.length > 3"
                    class="flex items-center justify-between border-t border-gray-200 dark:border-gray-800 px-6 py-4"
                >
                    <div class="flex items-center space-x-2">
                        <component
                            v-for="link in activities.links"
                            :key="link.label"
                            :is="link.url ? Link : 'span'"
                            :href="link.url || undefined"
                            :class="[
                                'px-3 py-1 rounded-md text-sm',
                                link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700',
                                !link.url && 'opacity-50 cursor-not-allowed',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Mostrando {{ activities.from }} a {{ activities.to }} de
                        {{ activities.total }} registos
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
