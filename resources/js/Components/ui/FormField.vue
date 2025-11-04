<template>
    <div class="space-y-2">
        <Label
            :for="id"
            v-if="label"
            :class="
                cn(
                    'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
                    required
                        ? 'after:content-[\'*\'] after:ml-0.5 after:text-destructive'
                        : ''
                )
            "
        >
            {{ label }}
        </Label>
        <slot />
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
        <p v-if="error" class="text-sm font-medium text-destructive">
            {{ error }}
        </p>
    </div>
</template>

<script setup>
import { cn } from "@/lib/utils";
import Label from "./Label.vue";

defineProps({
    id: {
        type: String,
        default: () => `field-${Math.random().toString(36).substring(2, 15)}`,
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
