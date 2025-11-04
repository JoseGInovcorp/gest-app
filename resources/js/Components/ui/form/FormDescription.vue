<template>
    <p
        v-if="$slots.default || description"
        class="text-sm text-muted-foreground"
    >
        <slot>{{ description }}</slot>
    </p>
</template>

<script setup>
defineProps({
    description: {
        type: String,
    },
});
</script>
