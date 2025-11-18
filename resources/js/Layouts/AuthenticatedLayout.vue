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
    ListChecks,
    CheckSquare,
    List,
    ListTodo,
} from "lucide-vue-next";

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);

// Estados para controlar expansão dos submenus
const workOrdersExpanded = ref(false);
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
const toggleWorkOrders = () => {
    workOrdersExpanded.value = !workOrdersExpanded.value;
};

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
        href: "clients",
        icon: Users,
        permission: "clients", // Verifica se tem qualquer permissão de clients
    },
    {
        name: "Fornecedores",
        href: "suppliers",
        icon: UserCheck,
        permission: "suppliers",
    },
    {
        name: "Contactos",
        href: "contacts",
        icon: Phone,
        permission: "contacts",
    },
    {
        name: "Propostas",
        href: "proposals.index",
        icon: FileText,
        disabled: false,
        permission: "proposals",
    },
    {
        name: "Calendário",
        href: "calendar.index",
        icon: Calendar,
        disabled: false,
        permission: "calendar", // Requer permissões de calendar
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
        href: "customer-orders",
        icon: ShoppingCart,
        permission: "customer-orders",
    },
    {
        name: "Encomendas - Fornecedores",
        href: "supplier-orders",
        icon: Truck,
        permission: "supplier-orders",
    },
    {
        name: "Ordens de Trabalho",
        href: "work-orders",
        icon: Briefcase,
        permission: "work-orders",
        children: [
            {
                name: "Minhas Tarefas",
                href: "work-orders.my-tasks",
                icon: CheckSquare,
            },
            {
                name: "Todas as Ordens",
                href: "work-orders.index",
                icon: List,
            },
        ],
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
        href: "bank-accounts",
        icon: CreditCard,
        disabled: false,
        permission: "bank-accounts",
    },
    {
        name: "Conta Corrente Clientes",
        href: "client-accounts",
        icon: DollarSign,
        disabled: false,
        permission: "client-accounts",
    },
    {
        name: "Faturas Fornecedores",
        href: "supplier-invoices",
        icon: FileText,
        disabled: false,
        permission: "supplier-invoices",
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
        href: "digital-archive",
        icon: FolderOpen,
        disabled: false,
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
        href: "users",
        icon: UserCog,
        current: route().current("users.*"),
        disabled: false,
        permission: "users",
    },
    {
        name: "Permissões",
        href: "roles",
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
        href: "countries",
        icon: Globe,
        disabled: false,
        permission: "countries",
    },
    {
        name: "Contactos - Funções",
        href: "contact-functions",
        icon: UserCog,
        disabled: false,
        permission: "contact-functions",
    },
    {
        name: "Calendário - Tipos",
        href: "calendar-event-types",
        icon: Calendar,
        disabled: false,
        permission: "calendar-event-types",
    },
    {
        name: "Calendário - Ações",
        href: "calendar-event-actions",
        icon: ListChecks,
        disabled: false,
        permission: "calendar-event-actions",
    },
    {
        name: "Tarefas - Templates",
        href: "task-templates",
        icon: ListTodo,
        disabled: false,
        permission: "task-templates",
    },
    {
        name: "Artigos",
        href: "articles",
        icon: Package,
        disabled: false,
        permission: "articles",
    },
    {
        name: "Financeiro - IVA",
        href: "vat-rates",
        icon: DollarSign,
        disabled: false,
        permission: "vat-rates",
    },
    {
        name: "Logs",
        href: "logs",
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

// Get route for navigation (adds .index if needed)
const getNavRoute = (href) => {
    // Se for dashboard, não adiciona nada
    if (href === "dashboard") {
        return href;
    }
    // Se já tem .edit, .create, .show ou .index, não adiciona nada
    if (
        href.includes(".edit") ||
        href.includes(".create") ||
        href.includes(".show") ||
        href.includes(".index")
    ) {
        return href;
    }
    // Caso contrário, adiciona .index
    return href + ".index";
};

// Auto-expand menus based on current route
const currentRouteName = route().current();

// Expandir Ordens de Trabalho se rota atual está em work-orders
if (currentRouteName) {
    const workOrderRoutes = ["work-orders"];
    if (workOrderRoutes.some((r) => currentRouteName.includes(r))) {
        workOrdersExpanded.value = true;
    }

    // Expandir Financeiro se rota atual está em itens financeiros
    const financialRoutes = [
        "bank-accounts",
        "client-accounts",
        "supplier-invoices",
    ];
    if (financialRoutes.some((r) => currentRouteName.includes(r))) {
        financialExpanded.value = true;
    }

    // Expandir Gestão de Acessos se rota atual está em users ou roles
    const accessRoutes = ["users", "roles"];
    if (accessRoutes.some((r) => currentRouteName.includes(r))) {
        accessManagementExpanded.value = true;
    }

    // Expandir Configurações se rota atual está em itens de configuração
    const configRoutes = [
        "company",
        "countries",
        "contact-functions",
        "calendar-event-types",
        "calendar-event-actions",
        "articles",
        "vat-rates",
        "logs",
        "task-templates",
    ];
    if (configRoutes.some((r) => currentRouteName.includes(r))) {
        configurationExpanded.value = true;
    }
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
                                                :href="
                                                    route(
                                                        getNavRoute(item.href)
                                                    )
                                                "
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
                                            <!-- Item with children (expandable) -->
                                            <div v-if="item.children">
                                                <button
                                                    @click="toggleWorkOrders"
                                                    :class="[
                                                        isActive(item.href)
                                                            ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                                            : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                        'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                    ]"
                                                >
                                                    <div
                                                        class="flex items-center gap-x-3"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            :class="[
                                                                isActive(
                                                                    item.href
                                                                )
                                                                    ? 'text-blue-600 dark:text-blue-400'
                                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                                'h-5 w-5 shrink-0',
                                                            ]"
                                                        />
                                                        {{ item.name }}
                                                    </div>
                                                    <ChevronDown
                                                        :class="[
                                                            'h-4 w-4 transition-transform duration-200',
                                                            workOrdersExpanded
                                                                ? 'rotate-180'
                                                                : '',
                                                        ]"
                                                    />
                                                </button>
                                                <!-- Submenu items -->
                                                <ul
                                                    v-show="workOrdersExpanded"
                                                    class="mt-1 space-y-1 pl-9"
                                                >
                                                    <li
                                                        v-for="child in item.children"
                                                        :key="child.name"
                                                    >
                                                        <Link
                                                            :href="
                                                                route(
                                                                    child.href
                                                                )
                                                            "
                                                            :class="[
                                                                isActive(
                                                                    child.href
                                                                )
                                                                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                                    : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                                'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
                                                            ]"
                                                        >
                                                            <component
                                                                :is="child.icon"
                                                                :class="[
                                                                    isActive(
                                                                        child.href
                                                                    )
                                                                        ? 'text-blue-600 dark:text-blue-400'
                                                                        : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                                    'h-4 w-4 shrink-0',
                                                                ]"
                                                            />
                                                            {{ child.name }}
                                                        </Link>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- Item without children (regular link) -->
                                            <Link
                                                v-else
                                                :href="
                                                    route(
                                                        getNavRoute(item.href)
                                                    )
                                                "
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
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <li>
                                            <button
                                                @click="toggleFinancial"
                                                :class="[
                                                    'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <div
                                                    class="flex items-center gap-x-3"
                                                >
                                                    <Banknote
                                                        :class="[
                                                            'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                            'h-5 w-5 shrink-0',
                                                        ]"
                                                    />
                                                    Financeiro
                                                </div>
                                                <ChevronDown
                                                    :class="[
                                                        'h-4 w-4 transition-transform duration-200',
                                                        financialExpanded
                                                            ? 'rotate-180'
                                                            : '',
                                                    ]"
                                                />
                                            </button>
                                            <!-- Submenu items -->
                                            <ul
                                                v-show="financialExpanded"
                                                class="mt-1 space-y-1 pl-9"
                                            >
                                                <li
                                                    v-for="item in financialNavigationItems"
                                                    :key="item.name"
                                                >
                                                    <Link
                                                        :href="
                                                            route(
                                                                getNavRoute(
                                                                    item.href
                                                                )
                                                            )
                                                        "
                                                        :class="[
                                                            isActive(item.href)
                                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                                : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
                                                        ]"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            :class="[
                                                                isActive(
                                                                    item.href
                                                                )
                                                                    ? 'text-blue-600 dark:text-blue-400'
                                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                                'h-4 w-4 shrink-0',
                                                            ]"
                                                        />
                                                        {{ item.name }}
                                                    </Link>
                                                </li>
                                            </ul>
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
                                                :href="
                                                    route(
                                                        getNavRoute(item.href)
                                                    )
                                                "
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
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <li>
                                            <button
                                                @click="toggleAccessManagement"
                                                :class="[
                                                    'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <div
                                                    class="flex items-center gap-x-3"
                                                >
                                                    <Shield
                                                        :class="[
                                                            'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                            'h-5 w-5 shrink-0',
                                                        ]"
                                                    />
                                                    Gestão de Acessos
                                                </div>
                                                <ChevronDown
                                                    :class="[
                                                        'h-4 w-4 transition-transform duration-200',
                                                        accessManagementExpanded
                                                            ? 'rotate-180'
                                                            : '',
                                                    ]"
                                                />
                                            </button>
                                            <!-- Submenu items -->
                                            <ul
                                                v-show="
                                                    accessManagementExpanded
                                                "
                                                class="mt-1 space-y-1 pl-9"
                                            >
                                                <li
                                                    v-for="item in accessManagementItems"
                                                    :key="item.name"
                                                >
                                                    <Link
                                                        :href="
                                                            route(
                                                                getNavRoute(
                                                                    item.href
                                                                )
                                                            )
                                                        "
                                                        :class="[
                                                            isActive(item.href)
                                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                                : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
                                                        ]"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            :class="[
                                                                isActive(
                                                                    item.href
                                                                )
                                                                    ? 'text-blue-600 dark:text-blue-400'
                                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                                'h-4 w-4 shrink-0',
                                                            ]"
                                                        />
                                                        {{ item.name }}
                                                    </Link>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Configuration Section -->
                                <li v-if="configurationItems.length > 0">
                                    <ul role="list" class="-mx-2 space-y-1">
                                        <li>
                                            <button
                                                @click="toggleConfiguration"
                                                :class="[
                                                    'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                                ]"
                                            >
                                                <div
                                                    class="flex items-center gap-x-3"
                                                >
                                                    <Settings
                                                        :class="[
                                                            'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                            'h-5 w-5 shrink-0',
                                                        ]"
                                                    />
                                                    Configurações
                                                </div>
                                                <ChevronDown
                                                    :class="[
                                                        'h-4 w-4 transition-transform duration-200',
                                                        configurationExpanded
                                                            ? 'rotate-180'
                                                            : '',
                                                    ]"
                                                />
                                            </button>
                                            <!-- Submenu items -->
                                            <ul
                                                v-show="configurationExpanded"
                                                class="mt-1 space-y-1 pl-9"
                                            >
                                                <li
                                                    v-for="item in configurationItems"
                                                    :key="item.name"
                                                >
                                                    <Link
                                                        v-if="!item.disabled"
                                                        :href="
                                                            route(
                                                                getNavRoute(
                                                                    item.href
                                                                )
                                                            )
                                                        "
                                                        :class="[
                                                            isActive(item.href)
                                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                                : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
                                                        ]"
                                                    >
                                                        <component
                                                            :is="item.icon"
                                                            :class="[
                                                                isActive(
                                                                    item.href
                                                                )
                                                                    ? 'text-blue-600 dark:text-blue-400'
                                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                                'h-4 w-4 shrink-0',
                                                            ]"
                                                        />
                                                        {{ item.name }}
                                                    </Link>
                                                    <div
                                                        v-else
                                                        class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-50"
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
                                        :href="route(getNavRoute(item.href))"
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
                                    <!-- Item with children (expandable) -->
                                    <div v-if="item.children">
                                        <button
                                            @click="toggleWorkOrders"
                                            :class="[
                                                isActive(item.href)
                                                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                                    : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                            ]"
                                        >
                                            <div
                                                class="flex items-center gap-x-3"
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
                                            </div>
                                            <ChevronDown
                                                :class="[
                                                    'h-4 w-4 transition-transform duration-200',
                                                    workOrdersExpanded
                                                        ? 'rotate-180'
                                                        : '',
                                                ]"
                                            />
                                        </button>
                                        <!-- Submenu items -->
                                        <ul
                                            v-show="workOrdersExpanded"
                                            class="mt-1 space-y-1 pl-9"
                                        >
                                            <li
                                                v-for="child in item.children"
                                                :key="child.name"
                                            >
                                                <Link
                                                    :href="route(child.href)"
                                                    :class="[
                                                        isActive(child.href)
                                                            ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                            : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                        'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
                                                    ]"
                                                >
                                                    <component
                                                        :is="child.icon"
                                                        :class="[
                                                            isActive(child.href)
                                                                ? 'text-blue-600 dark:text-blue-400'
                                                                : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                            'h-4 w-4 shrink-0',
                                                        ]"
                                                    />
                                                    {{ child.name }}
                                                </Link>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- Item without children (regular link) -->
                                    <Link
                                        v-else
                                        :href="route(getNavRoute(item.href))"
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
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <button
                                        @click="toggleFinancial"
                                        :class="[
                                            'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <div class="flex items-center gap-x-3">
                                            <Banknote
                                                :class="[
                                                    'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                    'h-5 w-5 shrink-0',
                                                ]"
                                            />
                                            Financeiro
                                        </div>
                                        <ChevronDown
                                            :class="[
                                                'h-4 w-4 transition-transform duration-200',
                                                financialExpanded
                                                    ? 'rotate-180'
                                                    : '',
                                            ]"
                                        />
                                    </button>
                                    <!-- Submenu items -->
                                    <ul
                                        v-show="financialExpanded"
                                        class="mt-1 space-y-1 pl-9"
                                    >
                                        <li
                                            v-for="item in financialNavigationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        getNavRoute(item.href)
                                                    )
                                                "
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
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
                                        </li>
                                    </ul>
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
                                        :href="route(getNavRoute(item.href))"
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
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <button
                                        @click="toggleAccessManagement"
                                        :class="[
                                            'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <div class="flex items-center gap-x-3">
                                            <Shield
                                                :class="[
                                                    'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                    'h-5 w-5 shrink-0',
                                                ]"
                                            />
                                            Gestão de Acessos
                                        </div>
                                        <ChevronDown
                                            :class="[
                                                'h-4 w-4 transition-transform duration-200',
                                                accessManagementExpanded
                                                    ? 'rotate-180'
                                                    : '',
                                            ]"
                                        />
                                    </button>
                                    <!-- Submenu items -->
                                    <ul
                                        v-show="accessManagementExpanded"
                                        class="mt-1 space-y-1 pl-9"
                                    >
                                        <li
                                            v-for="item in accessManagementItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        getNavRoute(item.href)
                                                    )
                                                "
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
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
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>

                        <!-- Configuration Section -->
                        <li v-if="configurationItems.length > 0">
                            <ul role="list" class="-mx-2 space-y-1">
                                <li>
                                    <button
                                        @click="toggleConfiguration"
                                        :class="[
                                            'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex w-full items-center justify-between gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all',
                                        ]"
                                    >
                                        <div class="flex items-center gap-x-3">
                                            <Settings
                                                :class="[
                                                    'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                    'h-5 w-5 shrink-0',
                                                ]"
                                            />
                                            Configurações
                                        </div>
                                        <ChevronDown
                                            :class="[
                                                'h-4 w-4 transition-transform duration-200',
                                                configurationExpanded
                                                    ? 'rotate-180'
                                                    : '',
                                            ]"
                                        />
                                    </button>
                                    <!-- Submenu items -->
                                    <ul
                                        v-show="configurationExpanded"
                                        class="mt-1 space-y-1 pl-9"
                                    >
                                        <li
                                            v-for="item in configurationItems"
                                            :key="item.name"
                                        >
                                            <Link
                                                v-if="!item.disabled"
                                                :href="
                                                    route(
                                                        getNavRoute(item.href)
                                                    )
                                                "
                                                :class="[
                                                    isActive(item.href)
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 transition-all',
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
                                                class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-50"
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
