<template>
    <div class="radio_blocks" role="radiogroup">
        <button
            v-for="option in options"
            :key="option.value"
            type="button"
            class="radio_blocks__item"
            :class="{ active: option.value === modelValue }"
            :aria-checked="option.value === modelValue"
            role="radio"
            @click="selectOption(option.value)"
        >
            {{ option.label }}
        </button>
    </div>
</template>

<script>
export default {
    props: {
        // Список элементов: [{ value: 'значение', label: 'Подпись' }]
        options: {
            type: Array,
            required: true,
        },
        modelValue: {
            type: [String, Number, Boolean],
            default: "",
        },
    },

    emits: ["update:modelValue"],

    setup(props, { emit }) {
        const selectOption = (value) => {
            emit("update:modelValue", value);
        };

        return { selectOption };
    },
};
</script>

<style scoped>
.radio_blocks {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.radio_blocks__item {
    padding: 12px 20px;
    border: 1px solid #004F44;
    border-radius: 10px;
    background-color: #fff;
    color: #004F44;
    font-size: 15px;
    cursor: pointer;
    transition:
        background-color 0.2s ease,
        color 0.2s ease;
}

.radio_blocks__item:hover {
    background-color: rgb(0 0 0 / 5%);
}

.radio_blocks__item.active {
    background-color: #004F44;
    color: #fff;
}
</style>
