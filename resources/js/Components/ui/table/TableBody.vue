<template>
    <tbody class="[&_tr:last-child]:border-0" v-bind="$attrs">
        <slot />
    </tbody>
</template>

<script setup>
defineOptions({
    inheritAttrs: false,
});
</script>
