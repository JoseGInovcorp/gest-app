<template>
    <form @submit="onSubmit">
        <slot />
    </form>
</template>

<script setup>
import { provide } from "vue";
import { useForm } from "vee-validate";

const props = defineProps({
    validationSchema: {
        type: Object,
        default: () => ({}),
    },
    initialValues: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["submit"]);

const form = useForm({
    validationSchema: props.validationSchema,
    initialValues: props.initialValues,
});

const onSubmit = form.handleSubmit((values) => {
    emit("submit", values);
});

// Provide form context to child components
provide("form", form);
</script>
