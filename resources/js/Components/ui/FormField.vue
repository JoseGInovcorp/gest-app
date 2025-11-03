<template>
    <div class="space-y-2">
        <Label
            :for="id"
            v-if="label"
            :class="
                required
                    ? 'after:content-[\'*\'] after:ml-0.5 after:text-red-500'
                    : ''
            "
        >
            {{ label }}
        </Label>
        <slot />
        <p v-if="description" class="text-sm text-gray-600 dark:text-gray-400">
            {{ description }}
        </p>
        <p v-if="error" class="text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import Label from "./Label.vue";

defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    description: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>
