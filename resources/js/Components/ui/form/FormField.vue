<template>
    <div class="space-y-2">
        <FormItem>
            <FormLabel v-if="label">{{ label }}</FormLabel>
            <FormControl>
                <slot />
            </FormControl>
            <FormDescription v-if="description">{{
                description
            }}</FormDescription>
            <FormMessage />
        </FormItem>
    </div>
</template>

<script setup>
import { inject } from "vue";
import { useField } from "vee-validate";
import FormItem from "./FormItem.vue";
import FormLabel from "./FormLabel.vue";
import FormControl from "./FormControl.vue";
import FormDescription from "./FormDescription.vue";
import FormMessage from "./FormMessage.vue";

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
    },
    description: {
        type: String,
    },
});

const form = inject("form", null);

// Use vee-validate field
const { errorMessage, meta } = useField(() => props.name, undefined, {
    form,
});

// Provide field context
provide("fieldName", props.name);
provide("fieldError", errorMessage);
provide("fieldMeta", meta);
</script>
