<template>
    <p v-if="errorMessage" class="text-sm font-medium text-destructive">
        {{ errorMessage }}
    </p>
</template>

<script setup>
import { inject } from "vue";

const errorMessage = inject("fieldError", null);
</script>
