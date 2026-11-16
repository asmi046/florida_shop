<template>
    <h2>Получатель</h2>

    <form-field
        type="text"
        label="Имя получателя"
        :model-value="name"
        @update:model-value="$emit('update:name', $event)"
        name="polname"
        :required="required"
    ></form-field>
    <form-field
        type="phone"
        label="Телефон"
        :model-value="phone"
        @update:model-value="$emit('update:phone', $event)"
        name="polphone"
        :required="required"
    ></form-field>
</template>

<script>
import FormField from "../FormField.vue";

export default {
    components: {
        FormField,
    },

    props: {
        name: {
            type: String,
            default: "",
        },
        phone: {
            type: String,
            default: "",
        },
        required: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:name", "update:phone"],
};
</script>

<style></style>
