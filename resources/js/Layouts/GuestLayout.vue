<script setup>
import { computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { Building2 } from "lucide-vue-next";

const page = usePage();
const company = computed(() => page.props.company || null);
const companyLogo = computed(() =>
    company.value?.logo ? `/storage/${company.value.logo}` : null
);
const companyName = computed(() => company.value?.name || "Gest-App");
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 dark:bg-gray-900 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/" class="flex flex-col items-center">
                <!-- Company Logo if available -->
                <img
                    v-if="companyLogo"
                    :src="companyLogo"
                    :alt="companyName"
                    class="h-40 w-auto max-w-md object-contain"
                />
                <!-- Fallback to styled logo with company name -->
                <div v-else class="flex flex-col items-center space-y-3">
                    <div
                        class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 shadow-xl"
                    >
                        <Building2 class="h-14 w-14 text-white" />
                    </div>
                    <h1
                        class="text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        {{ companyName }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Sistema de Gestão Empresarial
                    </p>
                </div>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
