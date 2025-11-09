<script setup>
import { computed } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link, usePage } from "@inertiajs/vue3";

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
            <Link href="/">
                <!-- Company Logo if available -->
                <img
                    v-if="companyLogo"
                    :src="companyLogo"
                    :alt="companyName"
                    class="h-40 w-auto max-w-md object-contain"
                />
                <!-- Fallback to default logo -->
                <ApplicationLogo
                    v-else
                    class="h-20 w-20 fill-current text-gray-500"
                />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
