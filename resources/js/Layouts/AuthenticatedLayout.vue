<script setup>
import { ref, computed, provide } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    Building2,
    Users,
    UserCheck,
    Package,
    FileText,
    ShoppingCart,
    CreditCard,
    Calendar,
    Settings,
    Shield,
    LogOut,
    Menu,
    X,
    ChevronDown,
    Home,
    Phone,
    ClipboardList,
    Truck,
    Briefcase,
    Banknote,
    Receipt,
    FolderOpen,
    UserCog,
    Lock,
    Globe,
    Zap,
    Activity,
    DollarSign,
    Archive,
    Building,
} from "lucide-vue-next";

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);

// Estados para controlar expansão dos submenus
const financialExpanded = ref(false);
const accessManagementExpanded = ref(false);
const configurationExpanded = ref(false);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const permissions = computed(() => {
    const perms = page.props.auth?.permissions;
    return Array.isArray(perms) ? perms : [];
});
const company = computed(() => page.props.company || null);
const companyLogo = computed(() =>
    company.value?.logo ? `/storage/${company.value.logo}` : null
);
const companyName = computed(() => company.value?.name || "Gest-App");

// Helper function para verificar permissões
const hasPermission = (permission) => {
    if (!permission || !Array.isArray(permissions.value)) return false;
    return permissions.value.includes(permission);
};

// Helper function para verificar se tem pelo menos uma permissão de um módulo
const hasAnyPermission = (module) => {
    if (!module || !Array.isArray(permissions.value)) return false;
    return ["create", "read", "update", "delete"].some((action) =>
        hasPermission(`${module}.${action}`)
    );
};

// Provide hasPermission para todos os componentes filhos
provide("hasPermission", hasPermission);

// Funções para toggle dos submenus
const toggleFinancial = () => {
    financialExpanded.value = !financialExpanded.value;
};

const toggleAccessManagement = () => {
    accessManagementExpanded.value = !accessManagementExpanded.value;
};

const toggleConfiguration = () => {
    configurationExpanded.value = !configurationExpanded.value;
};

// Navigation items  seguindo a estrutura do enunciado (usando rotas existentes)
const allMainNavigationItems = [
    {
        name: "Dashboard",
        href: "dashboard",
        icon: Home,
        permission: null, // Dashboard sempre visível
    },
    {
        name: "Clientes",
        href: "clients.index",
        icon: Users,
        permission: "clients", // Verifica se tem qualquer permissão de clients
    },
    {
        name: "Fornecedores",
        href: "suppliers.index",
        icon: UserCheck,
        permission: "suppliers",
    },
    {
        name: "Contactos",
        href: "contacts.index",
        icon: Phone,
        permission: "contacts",
    },
    {
        name: "Propostas",
        href: "dashboard", // Temporário até implementar
        icon: FileText,
        disabled: true,
        permission: "proposals",
    },
    {
        name: "Calendário",
        href: "calendar.index", // Temporário até implementar
        icon: Calendar,
        disabled: true,
        permission: "calendar", // Requer permissões de calendário
    },
];

// Filtrar itens baseado nas permissões
const mainNavigationItems = computed(() => {
    return allMainNavigationItems.filter((item) => {
        if (!item.permission) return true; // Sem permissão = sempre visível
        return hasAnyPermission(item.permission);
    });
});

const allOrdersNavigationItems = [
    {
        name: "Encomendas - Clientes",
        href: "customer-orders.index",
        icon: ShoppingCart,
        permission: "customer-orders",
    },
    {
        name: "Encomendas - Fornecedores",
        href: "supplier-orders.index",
        icon: Truck,
        permission: "supplier-orders",
    },
    {
        name: "Ordens de Trabalho",
        href: "dashboard", // Temporário até implementar
        icon: Briefcase,
        disabled: true,
        permission: "work-orders",
    },
];

const ordersNavigationItems = computed(() => {
    return allOrdersNavigationItems.filter((item) => {
        if (!item.permission) return true;
        return hasAnyPermission(item.permission);
    });
});

const allFinancialNavigationItems = [
    {
        name: "Contas Bancárias",
        href: "dashboard", // Temporário até implementar
        icon: Banknote,
        disabled: true,
        permission: "financial",
    },
    {
        name: "Conta Corrente Clientes",
        href: "dashboard", // Temporário até implementar
        icon: DollarSign,
        disabled: true,
        permission: "financial",
    },
    {
        name: "Faturas Fornecedores",
        href: "dashboard", // Temporário até implementar
        icon: Receipt,
        disabled: true,
        permission: "financial",
    },
];

const financialNavigationItems = computed(() => {
    return allFinancialNavigationItems.filter((item) => {
        if (!item.permission) return true;
        return hasAnyPermission(item.permission);
    });
});

const allSystemNavigationItems = [
    {
        name: "Arquivo Digital",
        href: "dashboard", // Temporário até implementar
        icon: FolderOpen,
        disabled: true,
        permission: "digital-archive", // Requer permissões de arquivo digital
    },
];

const systemNavigationItems = computed(() => {
    return allSystemNavigationItems.filter((item) => {
        if (!item.permission) return true;
        return hasAnyPermission(item.permission);
    });
});

const allAccessManagementItems = [
    {
        name: "Utilizadores",
        href: "users.index",
        icon: UserCog,
        current: route().current("users.*"),
        disabled: false,
        permission: "users",
    },
    {
        name: "Permissões",
        href: "roles.index",
        icon: Lock,
        current: route().current("roles.*"),
        disabled: false,
        permission: "roles",
    },
];

const accessManagementItems = computed(() => {
    return allAccessManagementItems.filter((item) => {
        if (!item.permission) return true;
        return hasAnyPermission(item.permission);
    });
});

const allConfigurationItems = [
    {
        name: "Empresa",
        href: "company.edit",
        icon: Building,
        disabled: false,
        permission: "company",
    },
    {
        name: "Entidades - Países",
        href: "countries.index",
        icon: Globe,
        disabled: false,
        permission: "countries",
    },
    {
        name: "Contactos - Funções",
        href: "contact-functions.index",
        icon: UserCog,
        disabled: false,
        permission: "contact-functions",
    },
    {
        name: "Calendário - Tipos",
        href: "dashboard", // Temporário até implementar
        icon: Calendar,
        disabled: true,
        permission: "calendar", // Requer permissões de calendário
    },
    {
        name: "Calendário - Acções",
        href: "dashboard", // Temporário até implementar
        icon: Zap,
        disabled: true,
        permission: "calendar", // Requer permissões de calendário
    },
    {
        name: "Artigos",
        href: "articles.index",
        icon: Package,
        disabled: false,
        permission: "articles",
    },
    {
        name: "Financeiro - IVA",
        href: "vat-rates.index",
        icon: DollarSign,
        disabled: false,
        permission: "vat-rates",
    },
    {
        name: "Logs",
        href: "logs.index",
        icon: Activity,
        current: route().current("logs.*"),
        disabled: false,
        permission: "logs", // Verificar permissões de logs
    },
];

const configurationItems = computed(() => {
    return allConfigurationItems.filter((item) => {
        if (!item.permission) return true;
        return hasAnyPermission(item.permission);
    });
});

// Check if route is current
const isActive = (routeName) => {
    return route().current(routeName) || route().current(routeName + ".*");
};

// Auto-expand menus based on current route
const currentRouteName = route().current();
if (currentRouteName && currentRouteName.includes("articles")) {
    configurationExpanded.value = true;
}

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Mobile sidebar -->
        <div v-show="sidebarOpen" class="relative z-50 lg:hidden">
            <!-- Background overlay -->
            <div
                class="fixed inset-0 bg-gray-900/80"
                @click="sidebarOpen = false"
            />

            <!-- Sidebar panel -->
            <div class="fixed inset-0 flex">
                <div class="relative mr-16 flex w-full max-w-xs flex-1">
                    <!-- Close button -->
                    <div
                        class="absolute left-full top-0 flex w-16 justify-center pt-5"
                    >
                        <button
                            type="button"
                            class="-m-2.5 p-2.5"
                            @click="sidebarOpen = false"
                        >
                            <X class="h-6 w-6 text-white" />
                        </button>
                    </div>

                    <!-- Sidebar content -->
                    <div
                        class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-900 px-6 pb-2 ring-1 ring-white/10"
                    >
                        <div class="flex h-16 shrink-0 items-center">
                            <div class="flex items-center space-x-3">
                                <!-- Company Logo if available -->
                                <img
                                    v-if="companyLogo"
                                    :src="companyLogo"
                                    :alt="companyName"
                                    class="h-12 w-auto max-w-[180px] object-contain"
                                />
                                <!-- Fallback icon -->
                                <div
                                    v-else
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 text-white"
                                >
                                    <Building2 class="h-5 w-5" />
                                </div>
                                <div>
                                    <h1
                                        class="text-sm font-semibold text-gray-900 dark:text-white"
                                    >
                                        {{ companyName }}
                                    </h1>
                                    <p
                                        class="text-xs text-gray-600 dark:text-gray-400"
                                    >
                                        Sistema Empresarial powered by Inovcorp
                                    </p>
                                </div>
                            </div>
                        </div>

                        <nav class="flex flex-1 flex-col">
                            <ul
                                role="list"
                                class="flex flex-1 flex-col gap-y-2"
                            >
                                <!-- Main Navigation -->
                                <li>
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <li
                                            v-for="item in mainNavigationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'text-blue-600 dark:text-blue-400'
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0',
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Orders Section -->
                                <li v-if="ordersNavigationItems.length > 0">
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <li
                                            v-for="item in ordersNavigationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'text-blue-600 dark:text-blue-400'
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0',
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Financial Section -->
                                <li v-if="financialNavigationItems.length > 0">
                                    <button
                                        @click="toggleFinancial"
                                        class="w-full text-left flex items-center justify-between text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-1 hover:text-gray-300 transition-colors"
                                    >
                                        <span>Financeiro</span>
                                        <ChevronDown
                                            :class="[
                                                'h-4 w-4 transition-transform duration-200',
                                                financialExpanded
                                                    ? 'rotate-180'
                                                    : '',
                                            ]"
                                        />
                                    </button>
                                    <ul
                                        v-show="financialExpanded"
                                        role="list"
                                        class="-mx-2 space-y-1 transition-all duration-300"
                                    >
                                        <li
                                            v-for="item in financialNavigationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'text-blue-600 dark:text-blue-400'
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0',
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>

                                <!-- System Section -->
                                <li v-if="systemNavigationItems.length > 0">
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <li
                                            v-for="item in systemNavigationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'text-blue-600 dark:text-blue-400'
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0',
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Access Management Section -->
                                <li v-if="accessManagementItems.length > 0">
                                    <button
                                        @click="toggleAccessManagement"
                                        class="w-full text-left flex items-center justify-between text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-1 hover:text-gray-300 transition-colors"
                                    >
                                        <span>Gestão de Acessos</span>
                                        <ChevronDown
                                            :class="[
                                                'h-4 w-4 transition-transform duration-200',
                                                accessManagementExpanded
                                                    ? 'rotate-180'
                                                    : '',
                                            ]"
                                        />
                                    </button>
                                    <ul
                                        v-show="accessManagementExpanded"
                                        role="list"
                                        class="-mx-2 space-y-1 transition-all duration-300"
                                    >
                                        <li
                                            v-for="item in accessManagementItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'text-blue-600 dark:text-blue-400'
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0',
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Configuration Section -->
                                <li v-if="configurationItems.length > 0">
                                    <button
                                        @click="toggleConfiguration"
                                        class="w-full text-left flex items-center justify-between text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-1 hover:text-gray-300 transition-colors"
                                    >
                                        <span>Configurações</span>
                                        <ChevronDown
                                            :class="[
                                                'h-4 w-4 transition-transform duration-200',
                                                configurationExpanded
                                                    ? 'rotate-180'
                                                    : '',
                                            ]"
                                        />
                                    </button>
                                    <ul
                                        v-show="configurationExpanded"
                                        role="list"
                                        class="-mx-2 space-y-1 transition-all duration-300"
                                    >
                                        <li
                                            v-for="item in configurationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                v-if="!item.disabled"
                                                :href="route(item.href)"
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-xs leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'text-blue-600 dark:text-blue-400'
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-4 w-4 shrink-0',
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                            <div
                                                v-else
                                                :class="[
                                                    'group flex gap-x-3 rounded-md p-2 text-xs leading-6 font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-50',
                                                ]"
                                            >
                                                <component
                                                    :is="item.icon"
                                                    class="h-4 w-4 shrink-0 text-gray-400"
                                                />
                                                {{ item.name }}
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop sidebar -->
        <div
            class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col"
        >
            <div
                class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6"
            >
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center space-x-3"
                    >
                        <!-- Company Logo if available -->
                        <img
                            v-if="companyLogo"
                            :src="companyLogo"
                            :alt="companyName"
                            class="h-12 w-auto max-w-[180px] object-contain"
                        />
                        <!-- Fallback icon -->
                        <div
                            v-else
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-sm"
                        >
                            <Building2 class="h-5 w-5" />
                        </div>
                        <div>
                            <h1
                                class="text-sm font-semibold text-gray-900 dark:text-white"
                            >
                                {{ companyName }}
                            </h1>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                Sistema Empresarial powered by Inovcorp
                            </p>
                        </div>
                    </Link>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-2">
                        <!-- Main Navigation -->
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li
                                    v-for="item in mainNavigationItems"
                                    :key="item.name"
                                >
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0',
                                            ]"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- Orders Section -->
                        <li v-if="ordersNavigationItems.length > 0">
                            <ul role="list" class="-mx-2 space-y-1">
                                <li
                                    v-for="item in ordersNavigationItems"
                                    :key="item.name"
                                >
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0',
                                            ]"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- Financial Section -->
                        <li v-if="financialNavigationItems.length > 0">
                            <button
                                @click="toggleFinancial"
                                class="w-full text-left flex items-center justify-between text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-2 hover:text-gray-300 transition-colors"
                            >
                                <span>Financeiro</span>
                                <ChevronDown
                                    :class="[
                                        'h-4 w-4 transition-transform duration-200',
                                        financialExpanded ? 'rotate-180' : '',
                                    ]"
                                />
                            </button>
                            <ul
                                v-show="financialExpanded"
                                role="list"
                                class="-mx-2 space-y-1 transition-all duration-300"
                            >
                                <li
                                    v-for="item in financialNavigationItems"
                                    :key="item.name"
                                >
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0',
                                            ]"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- System Section -->
                        <li v-if="systemNavigationItems.length > 0">
                            <ul role="list" class="-mx-2 space-y-1">
                                <li
                                    v-for="item in systemNavigationItems"
                                    :key="item.name"
                                >
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0',
                                            ]"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- Access Management Section -->
                        <li v-if="accessManagementItems.length > 0">
                            <button
                                @click="toggleAccessManagement"
                                class="w-full text-left flex items-center justify-between text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-2 hover:text-gray-300 transition-colors"
                            >
                                <span>Gestão de Acessos</span>
                                <ChevronDown
                                    :class="[
                                        'h-4 w-4 transition-transform duration-200',
                                        accessManagementExpanded
                                            ? 'rotate-180'
                                            : '',
                                    ]"
                                />
                            </button>
                            <ul
                                v-show="accessManagementExpanded"
                                role="list"
                                class="-mx-2 space-y-1 transition-all duration-300"
                            >
                                <li
                                    v-for="item in accessManagementItems"
                                    :key="item.name"
                                >
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0',
                                            ]"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- Configuration Section -->
                        <li v-if="configurationItems.length > 0">
                            <button
                                @click="toggleConfiguration"
                                class="w-full text-left flex items-center justify-between text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-2 hover:text-gray-300 transition-colors"
                            >
                                <span>Configurações</span>
                                <ChevronDown
                                    :class="[
                                        'h-4 w-4 transition-transform duration-200',
                                        configurationExpanded
                                            ? 'rotate-180'
                                            : '',
                                    ]"
                                />
                            </button>
                            <ul
                                v-show="configurationExpanded"
                                role="list"
                                class="-mx-2 space-y-1 transition-all duration-300"
                            >
                                <li
                                    v-for="item in configurationItems"
                                    :key="item.name"
                                >
                                    <Link
                                        v-if="!item.disabled"
                                        :href="route(item.href)"
                                        :class="[
                                            isActive(item.href)
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <component
                                            :is="item.icon"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-4 w-4 shrink-0',
                                            ]"
                                        />
                                        <span class="text-xs">{{
                                            item.name
                                        }}</span>
                                    </Link>
                                    <div
                                        v-else
                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-50"
                                    >
                                        <component
                                            :is="item.icon"
                                            class="h-4 w-4 shrink-0 text-gray-400"
                                        />
                                        <span class="text-xs">{{
                                            item.name
                                        }}</span>
                                    </div>
                                </li>
                            </ul>
                        </li>

                        <!-- User Menu at Bottom -->
                        <li class="mt-auto">
                            <div
                                class="border-t border-gray-200 dark:border-gray-800 pt-4"
                            >
                                <div
                                    class="flex items-center gap-x-3 p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                                >
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium"
                                    >
                                        {{ user?.name?.charAt(0) || "U" }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                        >
                                            {{ user?.name }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-600 dark:text-gray-400 truncate"
                                        >
                                            {{ user?.email }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-2 space-y-1">
                                    <Link
                                        :href="route('profile.edit')"
                                        class="group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-medium text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all"
                                    >
                                        <Settings
                                            class="h-4 w-4 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400"
                                        />
                                        Perfil
                                    </Link>

                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-medium text-gray-700 dark:text-gray-300 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all"
                                    >
                                        <LogOut
                                            class="h-4 w-4 text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400"
                                        />
                                        Sair
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Mobile header -->
        <div
            class="sticky top-0 z-40 flex items-center gap-x-6 bg-white dark:bg-gray-900 px-4 py-4 shadow-sm sm:px-6 lg:hidden border-b border-gray-200 dark:border-gray-800"
        >
            <button
                type="button"
                class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden"
                @click="sidebarOpen = true"
            >
                <Menu class="h-6 w-6" />
            </button>

            <div
                class="flex-1 text-sm font-semibold leading-6 text-gray-900 dark:text-white"
            >
                Gest-App
            </div>

            <!-- Mobile user menu -->
            <div
                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium"
            >
                {{ user?.name?.charAt(0) || "U" }}
            </div>
        </div>

        <!-- Main content -->
        <main class="lg:pl-72">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Custom styles if needed */
</style>
