<script setup>
import {
    AlertTriangle,
    Info,
    Trash2,
    Mail,
    CheckCircle,
} from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "warning", // warning, danger, info, success
    },
    confirmText: {
        type: String,
        default: "Confirmar",
    },
    cancelText: {
        type: String,
        default: "Cancelar",
    },
    confirmButtonClass: {
        type: String,
        default: null,
    },
    isProcessing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["confirm", "cancel", "close"]);

const icon = computed(() => {
    switch (props.type) {
        case "danger":
            return Trash2;
        case "info":
            return Info;
        case "success":
            return CheckCircle;
        case "warning":
        default:
            return AlertTriangle;
    }
});

const iconColor = computed(() => {
    switch (props.type) {
        case "danger":
            return "text-red-600";
        case "info":
            return "text-blue-600";
        case "success":
            return "text-green-600";
        case "warning":
        default:
            return "text-yellow-600";
    }
});

const defaultConfirmButtonClass = computed(() => {
    switch (props.type) {
        case "danger":
            return "bg-red-600 hover:bg-red-700 focus:ring-red-500";
        case "info":
            return "bg-blue-600 hover:bg-blue-700 focus:ring-blue-500";
        case "success":
            return "bg-green-600 hover:bg-green-700 focus:ring-green-500";
        case "warning":
        default:
            return "bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500";
    }
});

const handleConfirm = () => {
    emit("confirm");
};

const handleCancel = () => {
    emit("cancel");
    emit("close");
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="handleCancel"
    >
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4"
        >
            <!-- Header -->
            <div
                class="flex items-center space-x-3 p-6 border-b border-gray-200 dark:border-gray-700"
            >
                <component :is="icon" class="h-5 w-5" :class="iconColor" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ title }}
                </h3>
            </div>

            <!-- Body -->
            <div class="p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ message }}
                </p>
                <slot></slot>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700"
            >
                <button
                    @click="handleCancel"
                    :disabled="isProcessing"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ cancelText }}
                </button>
                <button
                    @click="handleConfirm"
                    :disabled="isProcessing"
                    :class="[
                        'px-4 py-2 text-sm font-medium text-white border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
                        confirmButtonClass || defaultConfirmButtonClass,
                    ]"
                >
                    <span v-if="isProcessing">A processar...</span>
                    <span v-else>{{ confirmText }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
