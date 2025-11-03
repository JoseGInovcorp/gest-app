<template>
    <form class="space-y-6" @submit="$emit('submit', $event)">
        <slot />
    </form>
</template>

<script setup>
// Form wrapper component that emits submit event
defineEmits(["submit"]);
</script>
