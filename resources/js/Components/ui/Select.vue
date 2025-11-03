<template>
    <select
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        :class="
            cn(
                'flex h-10 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm ring-offset-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:ring-offset-gray-950',
                props.class
            )
        "
        v-bind="$attrs"
    >
        <slot />
    </select>
</template>

<script setup>
import { cn } from "@/lib/utils";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    class: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);
</script>
