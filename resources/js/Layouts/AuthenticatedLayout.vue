<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
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
    Home
} from 'lucide-vue-next'

const showingNavigationDropdown = ref(false)
const sidebarOpen = ref(false)

const page = usePage()
const user = computed(() => page.props.auth?.user)

// Navigation items seguindo a estrutura do enunciado
const navigationItems = [
    {
        name: 'Dashboard',
        href: 'dashboard',
        icon: Home,
        current: false
    },
    {
        name: 'Clientes',
        href: 'clients.index',
        icon: Users,
        current: false,
        description: 'Gestão de clientes e prospects'
    },
    {
        name: 'Fornecedores', 
        href: 'suppliers.index',
        icon: UserCheck,
        current: false,
        description: 'Gestão de fornecedores'
    },
    {
        name: 'Contactos',
        href: 'contacts.index', 
        icon: Users,
        current: false,
        description: 'Lista de contactos'
    },
    {
        name: 'Artigos',
        href: 'articles.index',
        icon: Package,
        current: false,
        description: 'Produtos e serviços'
    },
    {
        name: 'Propostas',
        href: 'proposals.index',
        icon: FileText, 
        current: false,
        description: 'Orçamentos e propostas'
    },
    {
        name: 'Encomendas',
        href: 'orders.index',
        icon: ShoppingCart,
        current: false,
        description: 'Pedidos e encomendas'
    },
    {
        name: 'Financeiro',
        href: 'financial.index',
        icon: CreditCard,
        current: false,
        description: 'Faturação e pagamentos'
    },
    {
        name: 'Calendário',
        href: 'calendar.index',
        icon: Calendar,
        current: false,
        description: 'Agenda empresarial'
    }
]

const configurationItems = [
    {
        name: 'Configurações',
        href: 'settings.index',
        icon: Settings,
        current: false
    },
    {
        name: 'Gestão de Acessos',
        href: 'access.index', 
        icon: Shield,
        current: false
    }
]

// Check if route is current
const isCurrentRoute = (routeName) => {
    return route().current(routeName) || route().current(routeName + '.*')
}

// Update current status for navigation items
navigationItems.forEach(item => {
    item.current = isCurrentRoute(item.href)
})

configurationItems.forEach(item => {
    item.current = isCurrentRoute(item.href)
})

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Mobile sidebar -->
        <div 
            v-show="sidebarOpen"
            class="relative z-50 lg:hidden"
        >
            <!-- Background overlay -->
            <div 
                class="fixed inset-0 bg-gray-900/80"
                @click="sidebarOpen = false"
            />
            
            <!-- Sidebar panel -->
            <div class="fixed inset-0 flex">
                <div class="relative mr-16 flex w-full max-w-xs flex-1">
                    <!-- Close button -->
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button 
                            type="button" 
                            class="-m-2.5 p-2.5"
                            @click="sidebarOpen = false"
                        >
                            <X class="h-6 w-6 text-white" />
                        </button>
                    </div>
                    
                    <!-- Sidebar content -->
                    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-900 px-6 pb-2 ring-1 ring-white/10">
                        <div class="flex h-16 shrink-0 items-center">
                            <div class="flex items-center space-x-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
                                    <Building2 class="h-4 w-4" />
                                </div>
                                <div>
                                    <h1 class="text-sm font-semibold text-gray-900 dark:text-white">Gest-App</h1>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">Sistema Empresarial</p>
                                </div>
                            </div>
                        </div>
                        
                        <nav class="flex flex-1 flex-col">
                            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                <!-- Main Navigation -->
                                <li>
                                    <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">
                                        Módulos Principais
                                    </div>
                                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                                        <li v-for="item in navigationItems" :key="item.name">
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    item.current
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all'
                                                ]"
                                            >
                                                <component 
                                                    :is="item.icon" 
                                                    :class="[
                                                        item.current 
                                                            ? 'text-blue-600 dark:text-blue-400' 
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0'
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </li>
                                
                                <!-- Configuration -->
                                <li>
                                    <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">
                                        Configuração
                                    </div>
                                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                                        <li v-for="item in configurationItems" :key="item.name">
                                            <Link
                                                :href="route(item.href)"
                                                :class="[
                                                    item.current
                                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                        : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                                    'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all'
                                                ]"
                                            >
                                                <component 
                                                    :is="item.icon" 
                                                    :class="[
                                                        item.current 
                                                            ? 'text-blue-600 dark:text-blue-400' 
                                                            : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                        'h-5 w-5 shrink-0'
                                                    ]"
                                                />
                                                {{ item.name }}
                                            </Link>
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
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6">
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center">
                    <Link :href="route('dashboard')" class="flex items-center space-x-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-sm">
                            <Building2 class="h-4 w-4" />
                        </div>
                        <div>
                            <h1 class="text-sm font-semibold text-gray-900 dark:text-white">Gest-App</h1>
                            <p class="text-xs text-gray-600 dark:text-gray-400">Sistema Empresarial</p>
                        </div>
                    </Link>
                </div>
                
                <!-- Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Main Navigation -->
                        <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-2">
                                Módulos Principais
                            </div>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li v-for="item in navigationItems" :key="item.name">
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            item.current
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all'
                                        ]"
                                    >
                                        <component 
                                            :is="item.icon" 
                                            :class="[
                                                item.current 
                                                    ? 'text-blue-600 dark:text-blue-400' 
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0'
                                            ]"
                                        />
                                        <div class="flex flex-col">
                                            <span>{{ item.name }}</span>
                                            <span v-if="item.description" class="text-xs text-gray-500 dark:text-gray-400 font-normal">
                                                {{ item.description }}
                                            </span>
                                        </div>
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Configuration -->
                        <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide mb-2">
                                Configuração
                            </div>
                            <ul role="list" class="-mx-2 space-y-1">
                                <li v-for="item in configurationItems" :key="item.name">
                                    <Link
                                        :href="route(item.href)"
                                        :class="[
                                            item.current
                                                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-r-2 border-blue-600'
                                                : 'text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10',
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-medium transition-all'
                                        ]"
                                    >
                                        <component 
                                            :is="item.icon" 
                                            :class="[
                                                item.current 
                                                    ? 'text-blue-600 dark:text-blue-400' 
                                                    : 'text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400',
                                                'h-5 w-5 shrink-0'
                                            ]"
                                        />
                                        {{ item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- User Menu at Bottom -->
                        <li class="mt-auto">
                            <div class="border-t border-gray-200 dark:border-gray-800 pt-4">
                                <div class="flex items-center gap-x-3 p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium">
                                        {{ user?.name?.charAt(0) || 'U' }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ user?.name }}
                                        </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate">
                                            {{ user?.email }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-2 space-y-1">
                                    <Link 
                                        :href="route('profile.edit')" 
                                        class="group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-medium text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all"
                                    >
                                        <Settings class="h-4 w-4 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400" />
                                        Perfil
                                    </Link>
                                    
                                    <Link 
                                        :href="route('logout')" 
                                        method="post" 
                                        as="button"
                                        class="w-full group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-medium text-gray-700 dark:text-gray-300 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all"
                                    >
                                        <LogOut class="h-4 w-4 text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400" />
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
        <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white dark:bg-gray-900 px-4 py-4 shadow-sm sm:px-6 lg:hidden border-b border-gray-200 dark:border-gray-800">
            <button 
                type="button" 
                class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden"
                @click="sidebarOpen = true"
            >
                <Menu class="h-6 w-6" />
            </button>
            
            <div class="flex-1 text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                Gest-App
            </div>
            
            <!-- Mobile user menu -->
            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium">
                {{ user?.name?.charAt(0) || 'U' }}
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