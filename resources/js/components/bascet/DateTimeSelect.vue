<template>
    <h2>{{ title }}</h2>

    <template v-if="!asap && !clarify">
        <form-field
            type="date"
            label="Дата"
            :model-value="date"
            @update:model-value="$emit('update:date', $event)"
            :required="dateRequired"
        ></form-field>

        <form-field
            type="select"
            label="Время"
            :model-value="time"
            @update:model-value="$emit('update:time', $event)"
            :options="intervals"
            placeholder="Выберите интервал"
            :required="timeRequired"
        ></form-field>
    </template>

    <div class="delivery_check_wrap">
        <label class="check_label">
            <input
                type="checkbox"
                :checked="asap"
                @change="setAsap($event.target.checked)"
            />
            <span>В ближайшее время</span>
        </label>

        <label class="check_label">
            <input
                type="checkbox"
                :checked="clarify"
                @change="setClarify($event.target.checked)"
            />
            <span>Уточнить у получателя</span>
        </label>
    </div>
</template>

<script>
import FormField from "../FormField.vue";

export default {
    components: {
        FormField,
    },

    props: {
        // Текст заголовка блока
        title: {
            type: String,
            default: "Время и дата доставки",
        },
        // Интервалы времени: { ключ: 'подпись' }
        intervals: {
            type: Object,
            required: true,
        },
        date: {
            type: String,
            default: "",
        },
        time: {
            type: String,
            default: "",
        },
        asap: {
            type: Boolean,
            default: false,
        },
        clarify: {
            type: Boolean,
            default: false,
        },
        dateRequired: {
            type: Boolean,
            default: false,
        },
        timeRequired: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["update:date", "update:time", "update:asap", "update:clarify"],

    methods: {
        // Чекбоксы взаимоисключающие: включение одного сбрасывает другой
        setAsap(value) {
            this.$emit("update:asap", value);
            if (value) this.$emit("update:clarify", false);
        },

        setClarify(value) {
            this.$emit("update:clarify", value);
            if (value) this.$emit("update:asap", false);
        },
    },
};
</script>

<style scoped>
.delivery_check_wrap {
    margin: 10px 0;
}

.check_label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 10px 0;
    cursor: pointer;
}

.check_label input {
    width: unset!important;
    min-height: unset;
    margin: 0;
    accent-color: #004F44;
}
</style>
