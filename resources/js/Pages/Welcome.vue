<script setup>
import { Head, Link } from "@inertiajs/vue3";
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
    Globe,
} from "lucide-vue-next";

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
        title: "Gestão de Clientes",
        description:
            "Sistema completo de CRM com validação automática de VAT europeu via VIES.",
    },
    {
        icon: Package,
        title: "Controlo de Stock",
        description:
            "Gestão avançada de produtos e serviços com categorização inteligente.",
    },
    {
        icon: FileText,
        title: "Faturação Eletrónica",
        description:
            "Propostas, orçamentos e faturas automáticas com conformidade fiscal portuguesa.",
    },
    {
        icon: Calendar,
        title: "Calendário Empresarial",
        description:
            "Agendamento integrado de reuniões, follow-ups e tarefas comerciais.",
    },
    {
        icon: CreditCard,
        title: "Financeiro Completo",
        description:
            "Reconciliação bancária, relatórios fiscais e análise de cash-flow.",
    },
    {
        icon: Shield,
        title: "Segurança 2FA",
        description:
            "Autenticação de dois fatores e sistema granular de permissões por role.",
    },
];

const stats = [
    {
        number: "8",
        label: "Módulos Core",
        sublabel: "Sistema integrado completo",
    },
    { number: "27", label: "Países UE", sublabel: "Validação VAT VIES" },
    { number: "70+", label: "Permissões", sublabel: "Controlo granular" },
    { number: "6", label: "Roles", sublabel: "Hierarquia empresarial" },
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
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg"
                        >
                            <Building2 class="h-6 w-6" />
                        </div>
                        <div>
                            <h1
                                class="text-xl font-bold text-gray-900 dark:text-white"
                            >
                                Gest-App
                            </h1>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                Sistema Empresarial
                            </p>
                        </div>
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
                        <div class="mb-8 flex justify-center">
                            <div
                                class="relative rounded-full px-4 py-2 text-sm leading-6 text-gray-600 dark:text-gray-400 ring-1 ring-gray-900/10 dark:ring-gray-100/10 hover:ring-gray-900/20 dark:hover:ring-gray-100/20"
                            >
                                🚀 Projeto Final de Estágio | Inovcorp
                            </div>
                        </div>

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
                            Solução completa de CRM/ERP para PMEs portuguesas.
                            Desenvolvido com
                            <strong>Laravel 12</strong>,
                            <strong>Vue.js 3</strong> e
                            <strong>Inertia.js</strong>
                            seguindo os mais altos padrões de segurança e
                            conformidade fiscal.
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
                                class="text-sm font-semibold leading-6 text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center space-x-2"
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

            <!-- Stats Section -->
            <div class="py-16 sm:py-24">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl"
                        >
                            Sistema em Números
                        </h2>
                        <p
                            class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400"
                        >
                            Arquitectura robusta e escalável desenvolvida para
                            performance empresarial
                        </p>
                    </div>

                    <div
                        class="mx-auto mt-16 grid max-w-4xl grid-cols-2 gap-8 sm:grid-cols-4"
                    >
                        <div
                            v-for="stat in stats"
                            :key="stat.label"
                            class="text-center"
                        >
                            <div
                                class="text-4xl font-bold text-blue-600 dark:text-blue-400 sm:text-5xl"
                            >
                                {{ stat.number }}
                            </div>
                            <div
                                class="mt-2 text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                {{ stat.label }}
                            </div>
                            <div
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ stat.sublabel }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tech Stack -->
            <div
                class="py-16 sm:py-24 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm"
            >
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-2xl text-center">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl"
                        >
                            Stack Tecnológico Moderno
                        </h2>
                        <p
                            class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400"
                        >
                            Construído com as melhores tecnologias e frameworks
                            do mercado
                        </p>
                    </div>

                    <div
                        class="mx-auto mt-16 flex max-w-4xl flex-wrap items-center justify-center gap-8"
                    >
                        <div class="flex flex-col items-center space-y-2">
                            <div
                                class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4"
                            >
                                <img
                                    src="https://laravel.com/img/logomark.min.svg"
                                    alt="Laravel"
                                    class="h-8 w-8"
                                />
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                                >Laravel 12</span
                            >
                        </div>

                        <div class="flex flex-col items-center space-y-2">
                            <div
                                class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4"
                            >
                                <img
                                    src="https://vuejs.org/images/logo.png"
                                    alt="Vue.js"
                                    class="h-8 w-8"
                                />
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                                >Vue.js 3</span
                            >
                        </div>

                        <div class="flex flex-col items-center space-y-2">
                            <div
                                class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-4"
                            >
                                <div
                                    class="h-8 w-8 rounded bg-purple-600 flex items-center justify-center text-white text-sm font-bold"
                                >
                                    I
                                </div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                                >Inertia.js</span
                            >
                        </div>

                        <div class="flex flex-col items-center space-y-2">
                            <div
                                class="rounded-lg bg-cyan-50 dark:bg-cyan-900/20 p-4"
                            >
                                <div
                                    class="h-8 w-8 rounded bg-cyan-500 flex items-center justify-center text-white text-sm font-bold"
                                >
                                    T
                                </div>
                            </div>
                            <span
                                class="text-sm font-medium text-gray-900 dark:text-white"
                                >Tailwind CSS</span
                            >
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
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-700 text-white"
                        >
                            <Building2 class="h-4 w-4" />
                        </div>
                        <div>
                            <p
                                class="text-sm font-semibold text-gray-900 dark:text-white"
                            >
                                Gest-App
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                v{{ laravelVersion }} | PHP {{ phpVersion }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center space-x-6 text-sm text-gray-600 dark:text-gray-400"
                    >
                        <a
                            href="mailto:jose.goncalves@inovcorp.com"
                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center space-x-1"
                        >
                            <Mail class="h-4 w-4" />
                            <span>Contacto</span>
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
                            >© 2025 José Gonçalves | Inovcorp</span
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
