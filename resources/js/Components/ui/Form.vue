<template>
    <form :class="cn('space-y-6', className)" @submit="handleSubmit">
        <slot />
    </form>
</template>

<script setup>
import { cn } from "@/lib/utils";

const props = defineProps({
    class: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["submit"]);

const className = props.class;

const handleSubmit = (event) => {
    event.preventDefault();
    emit("submit", event);
};
</script>
