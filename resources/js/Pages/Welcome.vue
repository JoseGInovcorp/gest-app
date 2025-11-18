<script setup>
import { computed } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import {
    Building2,
    Users,
    Package,
    FileText,
    Calendar,
    CreditCard,
    Shield,
    ChevronRight,
    Github,
    Mail,
    ClipboardList,
    ShoppingCart,
    UserCheck,
} from "lucide-vue-next";

const page = usePage();
const company = computed(() => page.props.company || null);
const companyLogo = computed(() =>
    company.value?.logo ? `/storage/${company.value.logo}` : null
);
const companyName = computed(() => company.value?.name || "Gest-App");

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const features = [
    {
        icon: Users,
        title: "Gestão de Entidades",
        description:
            "CRM completo com clientes e fornecedores, validação automática de VAT europeu via VIES e gestão de contactos com RGPD.",
    },
    {
        icon: ClipboardList,
        title: "Ordens de Trabalho",
        description:
            "Sistema de workflow com templates configuráveis, atribuição de tarefas por grupos e sincronização automática.",
    },
    {
        icon: ShoppingCart,
        title: "Encomendas & Propostas",
        description:
            "Gestão completa de propostas comerciais, encomendas de clientes e fornecedores com controlo de estado.",
    },
    {
        icon: Package,
        title: "Controlo de Stock",
        description:
            "Gestão avançada de artigos (produtos e serviços) com categorização, stock em tempo real e indicadores visuais.",
    },
    {
        icon: Calendar,
        title: "Calendário Empresarial",
        description:
            "Agendamento integrado de eventos, reuniões e follow-ups com sistema de permissões granular por utilizador.",
    },
    {
        icon: CreditCard,
        title: "Gestão Financeira",
        description:
            "Contas bancárias, movimentos financeiros, reconciliação e relatórios com análise de cash-flow.",
    },
    {
        icon: Shield,
        title: "Segurança Avançada",
        description:
            "Autenticação 2FA, sistema de roles e permissões granulares, auditoria completa com activity log.",
    },
    {
        icon: UserCheck,
        title: "Gestão de Utilizadores",
        description:
            "Controlo de acessos com 6 roles hierárquicos, permissões personalizadas e gestão de equipas por departamento.",
    },
];
</script>

<template>
    <Head title="Gest-App - Sistema de Gestão Empresarial" />

    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800"
    >
        <!-- Header -->
        <header class="relative">
            <nav
                class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8"
                aria-label="Global"
            >
                <div class="flex lg:flex-1">
                    <div class="flex items-center space-x-4">
                        <!-- Company Logo if available -->
                        <template v-if="companyLogo">
                            <img
                                :src="companyLogo"
                                :alt="companyName"
                                class="h-20 w-auto max-w-[280px] object-contain rounded-lg"
                            />
                        </template>
                        <!-- Fallback: icon + company name when no logo -->
                        <template v-else>
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg"
                            >
                                <Building2 class="h-9 w-9" />
                            </div>
                            <div>
                                <h1
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ companyName }}
                                </h1>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Sistema de Gestão Empresarial
                                </p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex lg:flex-1 lg:justify-end">
                    <div class="flex items-center space-x-4 relative z-50">
                        <Link
                            :href="route('login')"
                            class="cursor-pointer text-sm font-semibold leading-6 text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors relative z-50 pointer-events-auto"
                        >
                            Entrar
                        </Link>
                        <Link
                            :href="route('register')"
                            class="cursor-pointer rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all relative z-50 pointer-events-auto"
                        >
                            Registar
                        </Link>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main>
            <div class="relative isolate px-6 pt-14 lg:px-8">
                <!-- Background decorative elements -->
                <div
                    class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80"
                    aria-hidden="true"
                >
                    <div
                        class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-blue-600 to-indigo-700 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                        style="
                            clip-path: polygon(
                                74.1% 44.1%,
                                100% 61.6%,
                                97.5% 26.9%,
                                85.5% 0.1%,
                                80.7% 2%,
                                72.5% 32.5%,
                                60.2% 62.4%,
                                52.4% 68.1%,
                                47.5% 58.3%,
                                45.2% 34.5%,
                                27.5% 76.7%,
                                0.1% 64.9%,
                                17.9% 100%,
                                27.6% 76.8%,
                                76.1% 97.7%,
                                74.1% 44.1%
                            );
                        "
                    />
                </div>

                <div class="mx-auto max-w-4xl py-16 sm:py-24 lg:py-32">
                    <!-- Main Title -->
                    <div class="text-center">
                        <h1
                            class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl"
                        >
                            Sistema de
                            <span
                                class="bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent"
                            >
                                Gestão Empresarial
                            </span>
                        </h1>

                        <p
                            class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto"
                        >
                            Solução completa de gestão empresarial para PMEs
                            portuguesas. Gestão integrada de clientes, produtos,
                            faturação e finanças com conformidade fiscal e
                            segurança avançada.
                        </p>

                        <div
                            class="mt-10 flex items-center justify-center gap-x-6"
                        >
                            <Link
                                :href="route('login')"
                                v-if="canLogin"
                                class="rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all flex items-center space-x-2"
                            >
                                <span>Aceder ao Sistema</span>
                                <ChevronRight class="h-4 w-4" />
                            </Link>
                            <a
                                href="https://github.com/JoseGInovcorp/gest-app"
                                target="_blank"
                                class="rounded-lg border border-gray-300 dark:border-gray-600 px-6 py-3 text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition-all flex items-center space-x-2"
                            >
                                <Github class="h-4 w-4" />
                                <span>Ver Código</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Background decorative bottom -->
                <div
                    class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
                    aria-hidden="true"
                >
                    <div
                        class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-indigo-600 to-purple-700 opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                        style="
                            clip-path: polygon(
                                74.1% 44.1%,
                                100% 61.6%,
                                97.5% 26.9%,
                                85.5% 0.1%,
                                80.7% 2%,
                                72.5% 32.5%,
                                60.2% 62.4%,
                                52.4% 68.1%,
                                47.5% 58.3%,
                                45.2% 34.5%,
                                27.5% 76.7%,
                                0.1% 64.9%,
                                17.9% 100%,
                                27.6% 76.8%,
                                76.1% 97.7%,
                                74.1% 44.1%
                            );
                        "
                    />
                </div>
            </div>

            <!-- Features Section -->
            <div
                class="py-16 sm:py-24 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm"
            >
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl"
                        >
                            Funcionalidades Principais
                        </h2>
                        <p
                            class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400"
                        >
                            Sistema modular com 8 módulos core integrados para
                            gestão empresarial completa
                        </p>
                    </div>

                    <div
                        class="mx-auto mt-16 grid max-w-6xl grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="feature in features"
                            :key="feature.title"
                            class="relative group cursor-pointer"
                        >
                            <div
                                class="relative overflow-hidden rounded-2xl bg-white dark:bg-slate-800 p-8 shadow-sm ring-1 ring-gray-200 dark:ring-slate-700 hover:shadow-lg hover:ring-blue-500/20 dark:hover:ring-blue-400/20 transition-all duration-300 hover:-translate-y-1"
                            >
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-600/10 dark:bg-blue-400/10"
                                    >
                                        <component
                                            :is="feature.icon"
                                            class="h-6 w-6 text-blue-600 dark:text-blue-400"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <h3
                                            class="text-lg font-semibold text-gray-900 dark:text-white"
                                        >
                                            {{ feature.title }}
                                        </h3>
                                    </div>
                                </div>
                                <p
                                    class="mt-4 text-sm text-gray-600 dark:text-gray-400 leading-relaxed"
                                >
                                    {{ feature.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="border-t border-gray-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm"
        >
            <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
                <div
                    class="flex flex-col items-center justify-between space-y-4 sm:flex-row sm:space-y-0"
                >
                    <div class="flex items-center space-x-3">
                        <!-- Company Logo if available -->
                        <img
                            v-if="companyLogo"
                            :src="companyLogo"
                            :alt="companyName"
                            class="h-8 w-auto max-w-[120px] object-contain rounded"
                        />
                        <!-- Fallback icon when no logo -->
                        <div
                            v-else
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 text-white"
                        >
                            <Building2 class="h-4 w-4" />
                        </div>
                        <p
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            {{ companyName }}
                        </p>
                    </div>

                    <div
                        class="flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400"
                    >
                        <a
                            href="mailto:jose.goncalves@inovcorp.com"
                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center space-x-1"
                        >
                            <Mail class="h-4 w-4" />
                            <span>Suporte</span>
                        </a>
                        <a
                            href="https://github.com/JoseGInovcorp/gest-app"
                            target="_blank"
                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center space-x-1"
                        >
                            <Github class="h-4 w-4" />
                            <span>GitHub</span>
                        </a>
                        <span class="text-xs"
                            >© 2025 Inovcorp. Todos os direitos
                            reservados.</span
                        >
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Custom animations and styles if needed */
</style>
