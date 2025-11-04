<template>
    <Label
        :for="labelFor"
        :class="
            cn(
                'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
                className
            )
        "
    >
        <slot />
    </Label>
</template>

<script setup>
import { inject, computed } from "vue";
import { cn } from "@/lib/utils";
import Label from "../Label.vue";

const props = defineProps({
    for: {
        type: String,
    },
    class: {
        type: String,
    },
});

const fieldName = inject("fieldName", null);

const labelFor = computed(() => props.for || fieldName);
const className = computed(() => props.class);
</script>
