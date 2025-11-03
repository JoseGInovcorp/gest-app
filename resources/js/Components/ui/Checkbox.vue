<template>
    <div class="flex items-center space-x-2">
        <input
            type="checkbox"
            :id="id"
            :checked="checked"
            @change="$emit('update:checked', $event.target.checked)"
            :class="
                cn(
                    'peer h-4 w-4 shrink-0 rounded-sm border border-gray-300 dark:border-gray-600 ring-offset-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-blue-600 data-[state=checked]:text-white',
                    props.class
                )
            "
            v-bind="$attrs"
        />
        <Label
            :for="id"
            v-if="label"
            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
        >
            {{ label }}
        </Label>
        <slot />
    </div>
</template>

<script setup>
import { computed } from "vue";
import { cn } from "@/lib/utils";
import Label from "./Label.vue";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    checked: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: "",
    },
    class: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:checked"]);
</script>
