<template>
    <h2>Адрес достаки</h2>

    <label class="check_label">
        <input
            type="checkbox"
            :checked="clarify"
            @change="$emit('update:clarify', $event.target.checked)"
        />
        <span>Уточнить адрес у получателя</span>
    </label>

    <template v-if="!clarify">
        <form-field
            type="select"
            label="Район доставки"
            :model-value="rayon"
            @update:model-value="$emit('update:rayon', $event)"
            :options="rayonOptions"
            placeholder="Выберите район доставки"
            :required="addressRequired"
        ></form-field>

        <div class="adr_wrapper">
            <div class="pds_select_wrapper">
                <form-field
                    type="text"
                    label="Улица"
                    :model-value="street"
                    @update:model-value="$emit('update:street', $event)"
                    name="street"
                    :required="addressRequired"
                ></form-field>
            </div>

            <form-field
                type="text"
                label="Дом"
                :model-value="home"
                @update:model-value="$emit('update:home', $event)"
                name="home"
                :required="addressRequired"
            ></form-field>
        </div>

        <div class="adr_wrapper_2">
            <form-field
                type="text"
                label="Подъезд"
                :model-value="podezd"
                @update:model-value="$emit('update:podezd', $event)"
                name="podezd"
            ></form-field>
            <form-field
                type="text"
                label="Этаж"
                :model-value="etazg"
                @update:model-value="$emit('update:etazg', $event)"
                name="etazg"
            ></form-field>
            <form-field
                type="text"
                label="Квартира"
                :model-value="kvartira"
                @update:model-value="$emit('update:kvartira', $event)"
                name="kvartira"
            ></form-field>
        </div>
    </template>
</template>

<script>
import FormField from "../FormField.vue";

export default {
    components: {
        FormField,
    },

    props: {
        rayon: {
            type: String,
            default: "",
        },
        street: {
            type: String,
            default: "",
        },
        home: {
            type: String,
            default: "",
        },
        podezd: {
            type: String,
            default: "",
        },
        etazg: {
            type: String,
            default: "",
        },
        kvartira: {
            type: String,
            default: "",
        },
        clarify: {
            type: Boolean,
            default: false,
        },
        addressRequired: {
            type: Boolean,
            default: false,
        },
    },

    emits: [
        "update:rayon",
        "update:street",
        "update:home",
        "update:podezd",
        "update:etazg",
        "update:kvartira",
        "update:clarify",
    ],

    data() {
        return {
            // значения должны совпадать с ключами карты зон в calcDelivery()
            rayonOptions: [
                {
                    value: "Центр + проспект Победы, Дериглазова, ул. Карла-Маркса",
                    label: "Центр + проспект Победы, Дериглазова, ул. Карла-Маркса — бесплатно",
                },
                {
                    value: "Сеймский район (Парковая, Магистральный, Волокно)",
                    label: "Сеймский район (Парковая, Магистральный, Волокно) — 350 р.",
                },
                {
                    value: "Северозападный район",
                    label: "Северозападный район — 250 р.",
                },
                { value: "п. Искра", label: "п. Искра — 250 р." },
                { value: "п. Татаренково", label: "п. Татаренково — 250 р." },
                { value: "п. Косиновский", label: "п. Косиновский — 250 р." },
                { value: "КЗТЗ", label: "КЗТЗ — 300 р." },
                {
                    value: "Железнодорожный район",
                    label: "Железнодорожный район — 300 р.",
                },
                {
                    value: "Жукова, Халино, Щетинка",
                    label: "Жукова, Халино, Щетинка — 600 р.",
                },
                {
                    value: "Духовец, Кислино, Зорино, Лебяжье, Кукуевка, Гремячка",
                    label: "Духовец, Кислино, Зорино, Лебяжье, Кукуевка, Гремячка — 600 р.",
                },
            ],
        };
    },
};
</script>

<style></style>
