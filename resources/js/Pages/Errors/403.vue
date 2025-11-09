<template>
    <Head title="Acesso Negado" />
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <div
                    class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20"
                >
                    <svg
                        class="h-12 w-12 text-red-600 dark:text-red-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                </div>
                <h1
                    class="mt-6 text-4xl font-extrabold text-gray-900 dark:text-gray-100"
                >
                    403
                </h1>
                <h2
                    class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100"
                >
                    Acesso Negado
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    Não tem permissão para aceder a este recurso.
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">
                    Se acredita que isto é um erro, contacte o administrador do
                    sistema.
                </p>
            </div>

            <div class="mt-8 space-y-4">
                <Link
                    :href="route('dashboard')"
                    class="inline-flex items-center justify-center w-full px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg
                        class="mr-2 -ml-1 h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        />
                    </svg>
                    Voltar ao Dashboard
                </Link>

                <button
                    @click="goBack"
                    class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg
                        class="mr-2 -ml-1 h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    Voltar à Página Anterior
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";

const goBack = () => {
    window.history.back();
};
</script>
