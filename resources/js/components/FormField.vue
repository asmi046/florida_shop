<template>
    <div class="form_field">
        <label class="form_field__label" :for="fieldId">
            {{ label }}<span v-if="required" class="form_field__req">*</span>
        </label>

        <textarea
            v-if="type == 'textarea'"
            :id="fieldId"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :name="name"
            :placeholder="placeholderText"
            :rows="rows"
        ></textarea>

        <select
            v-else-if="type == 'select'"
            :id="fieldId"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :name="name"
        >
            <option value="" disabled>{{ placeholderText }}</option>
            <option
                v-for="option in normalizedOptions"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>

        <input
            v-else-if="type == 'phone'"
            v-phone-mask
            :id="fieldId"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :name="name"
            type="text"
            :placeholder="placeholderText"
        />

        <input
            v-else
            :id="fieldId"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :name="name"
            :type="type"
            :placeholder="placeholderText"
        />
    </div>
</template>

<script>
let instanceCounter = 0;

export default {
    props: {
        // text | email | phone | date | select | textarea
        type: {
            type: String,
            default: "text",
        },
        label: {
            type: String,
            required: true,
        },
        modelValue: {
            type: [String, Number],
            default: "",
        },
        name: {
            type: String,
            default: "",
        },
        required: {
            type: Boolean,
            default: false,
        },
        // Для select: массив [{ value, label }] или объект { ключ: 'подпись' }
        options: {
            type: [Array, Object],
            default: () => [],
        },
        placeholder: {
            type: String,
            default: "",
        },
        rows: {
            type: Number,
            default: 4,
        },
    },

    emits: ["update:modelValue"],

    data() {
        instanceCounter++;
        return {
            fieldId: `form_field_${instanceCounter}`,
        };
    },

    computed: {
        placeholderText() {
            return this.placeholder || this.label;
        },

        normalizedOptions() {
            if (Array.isArray(this.options)) return this.options;
            return Object.entries(this.options).map(([value, label]) => ({
                value: label,
                label,
            }));
        },
    },
};
</script>

<style scoped>
.form_field {
    margin: 15px 0;
}

.form_field__label {
    display: block;
    font-size: 13px;
    color: #999;
    margin-bottom: 10px;
}

.form_field__req {
    color: #a2182d;
    margin-left: 3px;
}

.form_field input,
.form_field select,
.form_field textarea {
    width: 100%;
}

.form_field textarea {
    min-height: 80px;
}
</style>
