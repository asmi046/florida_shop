<template>
    <div v-show="!show_bascet" class="bascet_lader">
        <span></span>
        <p>Загружаем корзину...</p>
    </div>
    <div v-show="bascetList.length != 0" class="bascet">
        <div class="bascet_tovar">
            <div class="control">
                <a
                    @click.prevent="clearBascet()"
                    class="clear_bascet_btn"
                    href="#"
                >
                    <svg
                        viewBox="0 0 32 32"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        width="21"
                        height="21"
                    >
                        <path
                            d="M25,10H7v17c0,1.105,0.895,2,2,2h14c1.105,0,2-0.895,2-2V10z"
                        />
                        <path
                            d="M20,7h-8V5c0-1.105,0.895-2,2-2h4c1.105,0,2,0.895,2,2V7z"
                        />
                        <path
                            d="M28,10H4V8c0-0.552,0.448-1,1-1h22c0.552,0,1,0.448,1,1V10z"
                        />
                        <line x1="16" x2="16" y1="15" y2="24" />
                        <line x1="12" x2="12" y1="15" y2="24" />
                        <line x1="20" x2="20" y1="15" y2="24" />
                    </svg>
                    <span>Очистить корзину</span></a
                >
            </div>

            <div class="tovar_list">
                <div
                    v-for="(item, index) in bascetList"
                    :key="item.product_sku"
                    class="tovar"
                >
                    <div class="item_info">
                        <div class="tovar_all_blk picture_blk">
                            <img
                                v-if="item.tovar_data.img != ''"
                                :src="item.tovar_data.img"
                                alt=""
                            />
                            <img v-else :src="noPhotoUrl" alt="" />
                        </div>
                        <div class="tovar_all_blk name_blk">
                            <h2>{{ item.tovar_data.title }}</h2>
                            <span class="item_summ__label">Цена</span>
                            <div class="item_price">
                                {{
                                    Number(
                                        parseFloat(item.price),
                                    ).toLocaleString("ru-RU")
                                }}
                                ₽
                            </div>
                        </div>
                        <div class="tovar_all_blk dll_blk">
                            <span
                                @click.prevent="deleteElement(item, index)"
                                title="Удалить товар"
                            ></span>
                        </div>
                    </div>

                    <div class="item_controls">
                        <div class="tovar_all_blk couint_blk">
                            <div class="number_wrapper">
                                <span
                                    @click="
                                        item.quentity--;
                                        updateBascet();
                                        updateItem(item);
                                    "
                                    class="number_btn val_down"
                                    >−</span
                                >
                                <input type="number" :value="item.quentity" />
                                <span
                                    @click="
                                        item.quentity++;
                                        updateBascet();
                                        updateItem(item);
                                    "
                                    class="number_btn val_upp"
                                    >+</span
                                >
                            </div>
                        </div>
                        <div class="tovar_all_blk summ_blk">
                            <span class="item_summ__label">Сумма</span>
                            <span class="rub price_formator item_summ__value"
                                >{{
                                    Number(
                                        parseFloat(item.quentity) *
                                            parseFloat(item.price),
                                    ).toLocaleString("ru-RU")
                                }}
                                <span class="rub_symbol">₽</span></span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bascet_form">
            <form action="GET">
                <h2>Заказчик</h2>

                <form-field
                    type="text"
                    label="Фамилия, Имя"
                    v-model="bascetInfo.fio"
                    name="fio"
                    required
                ></form-field>
                <form-field
                    type="email"
                    label="e-mail"
                    v-model="bascetInfo.email"
                    name="email"
                ></form-field>
                <form-field
                    type="phone"
                    label="Телефон"
                    v-model="bascetInfo.phone"
                    name="phone"
                    required
                ></form-field>

                <h2>Тип заказа</h2>
                <radio-blocks
                    v-model="deliveryType"
                    :options="deliveryOptions"
                ></radio-blocks>

                <div v-if="deliveryType == 'Самовывоз'">
                    <br />
                    <p>
                        Адрес самовывоза:
                        <strong>г. Курск, пр. Победы, 14, работаем 24/7</strong>
                    </p>

                    <h2>Кто заберет заказ?</h2>

                    <radio-blocks
                        v-model="receiverType"
                        :options="samovivozReceiverOptions"
                    ></radio-blocks>

                    <div v-if="receiverType">
                        <receiver-fields
                            v-model:name="bascetInfo.polname"
                            v-model:phone="bascetInfo.polphone"
                            :required="receiverType"
                        ></receiver-fields>
                    </div>

                    <date-time-select
                        title="Время и дата самовывоза"
                        :intervals="timeIntervals"
                        v-model:date="bascetInfo.data"
                        v-model:time="bascetInfo.time"
                        v-model:asap="orderAsap"
                        v-model:clarify="orderClarify"
                        :date-required="true"
                        :time-required="timeRequired"
                    ></date-time-select>
                </div>

                <div v-if="deliveryType == 'Доставка'">
                    <h2>Кому доставить заказ?</h2>

                    <radio-blocks
                        v-model="receiverType"
                        :options="dostavkaReceiverOptions"
                    ></radio-blocks>

                    <div v-if="receiverType">
                        <receiver-fields
                            v-model:name="bascetInfo.polname"
                            v-model:phone="bascetInfo.polphone"
                            :required="receiverType"
                        ></receiver-fields>
                        <label class="check_label">
                            <input type="checkbox" v-model="anonymousOrder" />
                            <span>Доставить анонимно</span>
                        </label>
                    </div>

                    <delivery-address
                        v-model:rayon="bascetInfo.rayon"
                        v-model:street="bascetInfo.street"
                        v-model:home="bascetInfo.home"
                        v-model:podezd="bascetInfo.podezd"
                        v-model:etazg="bascetInfo.etazg"
                        v-model:kvartira="bascetInfo.kvartira"
                        v-model:clarify="addressClarify"
                        :address-required="addressRequired"
                        @update:rayon="calcDelivery()"
                    ></delivery-address>

                    <date-time-select
                        title="Время и дата достаки"
                        :intervals="timeIntervals"
                        v-model:date="bascetInfo.data"
                        v-model:time="bascetInfo.time"
                        v-model:asap="orderAsap"
                        v-model:clarify="orderClarify"
                        :date-required="true"
                        :time-required="timeRequired"
                    ></date-time-select>
                </div>

                <form-field
                    type="textarea"
                    label="Комментарий"
                    v-model="bascetInfo.comment"
                    name="comment"
                ></form-field>

                <h2>Способ оплаты</h2>

                <p class="pay_type_intro">On-line на сайте</p>

                <ul class="pay_icons" aria-label="Доступные способы оплаты">
                    <li>
                        <img
                            :src="'/img/pay_icons/sber-color-logo.svg'")
                            alt="Оплата банковской картой Сбербанк — СберPay"
                            width="40"
                            height="20"
                            loading="lazy"
                        />
                    </li>
                    <li>
                        <img
                            :src="'/img/pay_icons/mir-color-logo.svg'")
                            alt="Оплата банковской картой МИР"
                            width="40"
                            height="20"
                            loading="lazy"
                        />
                    </li>
                    <li>
                        <img
                            :src="'/img/pay_icons/alpha-color.svg'")
                            alt="Оплата через Альфа-Банк"
                            width="40"
                            height="20"
                            loading="lazy"
                        />
                    </li>
                    <li>
                        <img
                            :src="'/img/pay_icons/t-pat-color-logo.svg'")
                            alt="Оплата через T-Pay Тинькофф"
                            width="40"
                            height="20"
                            loading="lazy"
                        />
                    </li>
                    <li>
                        <img
                            :src="'/img/pay_icons/sbp-color-logo.svg'")
                            alt="Оплата через Систему быстрых платежей СБП"
                            width="40"
                            height="20"
                            loading="lazy"
                        />
                    </li>
                </ul>
                <ul v-show="errorList.length != 0" class="errors_list">
                    <li v-for="item in errorList" :key="item">{{ item }}</li>
                </ul>
            </form>

            <aside class="itogo">
                <div class="itogo_price_count">
                    <div class="itogo_row">
                        <span class="text"
                            >Товары (<span>{{ count }}</span
                            >)</span
                        >
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >{{ Number(subtotal).toLocaleString("ru-RU") }}
                            <span class="rub_symbol">₽</span></span
                        >
                    </div>

                    <div v-show="deliveryPrice > 0" class="itogo_row">
                        <span class="text"
                            >Доставка (Зона {{ deliveryZone }})</span
                        >
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >{{ Number(deliveryPrice).toLocaleString("ru-RU") }}
                            <span class="rub_symbol">₽</span></span
                        >
                    </div>

                    <div class="itogo_row itogo_row_final">
                        <span class="text">Итого</span>
                        <span class="razd"></span>
                        <span class="p_price rub price_formator"
                            >{{
                                Number(subtotal + deliveryPrice).toLocaleString(
                                    "ru-RU",
                                )
                            }}
                            <span class="rub_symbol">₽</span></span
                        >
                    </div>
                </div>
            </aside>
        </div>

        <div class="bascet_form_footer">
            <label class="policy_descr" for="policy_accept">
                <input
                    v-model="policy"
                    type="checkbox"
                    checked
                    id="policy_accept"
                />
                <p>
                    Я принимаю условия
                    <a href="/policy">политики в области персональных данных</a>
                    и даю согдасие
                    <a href="/policy_accept"
                        >на обработку персональных данных</a
                    >
                </p>
            </label>

            <div class="form_btn_wrapper">
                <button
                    @click.prevent="sendBascet()"
                    class="button button_green bascetSendBtn"
                    type="submit"
                >
                    Оформить заказ
                </button>
                <span
                    :class="{ active: loadet }"
                    class="btnLoaderCart shoved"
                ></span>
            </div>
        </div>
    </div>
    <div class="empty_bascet" v-show="show_bascet && bascetList.length == 0">
        <div class="bascet_icon">
            <svg viewBox="4532 51 30 31" fill="currentColor" width="170" height="170">
                <g id="cart">
                    <path d="M4538.76,60.014a7.3,7.3,0,0,1,6.688-8.428,7.013,7.013,0,0,1,5.687,2.42,7.662,7.662,0,0,1,1.715,6.008h.557q2.449,0,4.9,0a1,1,0,0,1,1.139,1.115c.005,5.217.03,10.435-.009,15.652a5.28,5.28,0,0,1-5.4,5.286q-8.171.007-16.342,0a5.291,5.291,0,0,1-5.475-5.4c-.032-5.172-.01-10.344-.006-15.515,0-.779.378-1.139,1.16-1.14C4535.154,60.013,4536.937,60.014,4538.76,60.014Zm-4.635,1.917c-.008.113-.017.187-.017.26,0,4.85-.013,9.7.006,14.549a3.376,3.376,0,0,0,3.476,3.418q8.237.006,16.474,0a3.517,3.517,0,0,0,.681-.048,3.415,3.415,0,0,0,2.8-3.5q.009-7.137,0-14.275c0-.13-.016-.261-.025-.384h-4.649c0,.752,0,1.48,0,2.208a.96.96,0,0,1-.537.935.943.943,0,0,1-1.367-.891c-.021-.743,0-1.486,0-2.237h-10.275v1.914c0,.859-.357,1.341-.977,1.324s-.932-.474-.932-1.317V61.931Zm16.8-1.935c.25-2.421-.291-4.495-2.486-5.786a4.876,4.876,0,0,0-5.556.212c-1.955,1.345-2.392,3.331-2.149,5.574Z"/>
                </g>
            </svg>
        </div>
        <h3>Ваша корзина пуста</h3>
        <p>Жмите на значек корзиныи добавляйте товар!</p>
    </div>
    <div ref="mapInComponent"></div>
</template>

<script>
import Delivery from "../../lib/delivery.js";
import SelectInput from "../selectInput.vue";
import RadioBlocks from "../RadioBlocks.vue";
import DateTimeSelect from "./DateTimeSelect.vue";
import ReceiverFields from "./ReceiverFields.vue";
import DeliveryAddress from "./DeliveryAddress.vue";
import FormField from "../FormField.vue";

import { DataLayer } from "../../eCommerceYandex.js";

export default {
    data() {
        return {
            timeIntervals: {
                500317: "08:00-10:00",
                369615: "09:00-11:00",
                369619: "10:00-12:00",
                500325: "11:00-13:00",
                500329: "12:00-14:00",
                500333: "13:00-15:00",
                500337: "14:00-16:00",
                500341: "15:00-17:00",
                500345: "16:00-18:00",
                500349: "17:00-19:00",
                500353: "18:00-20:00",
                500357: "19:00-21:00",
                500361: "20:00-22:00",
                500365: "21:00-23:00",
                500369: "22:00-00:00",
                500373: "23:00-01:00",
                500377: "00:00-02:00",
                500381: "01:00-03:00",
                500385: "02:00-04:00",
                500389: "03:00-05:00",
                500393: "04:00-06:00",
                500397: "05:00-07:00",
                500401: "06:00-08:00",
                500405: "07:00-09:00",
            },
            noPhotoUrl: "img/noPhoto.jpg",
            bascetList: [],
            loadet: false,
            policy: true,
            count: 0,
            subtotal: 0,
            deliveryPrice: 0,
            deliveryZone: "",
            deliveryType: "Самовывоз",
            receiverType: false,
            orderAsap: false,
            orderClarify: false,
            anonymousOrder: false,
            addressClarify: false,
            deliveryOptions: [
                {
                    value: "Самовывоз",
                    label: "Самовывоз",
                },
                { value: "Доставка", label: "Доставка" },
            ],
            dostavkaReceiverOptions: [
                { value: false, label: "Мне" },
                { value: true, label: "Другому человеку" },
            ],
            samovivozReceiverOptions: [
                { value: false, label: "Я сам" },
                { value: true, label: "Другой человек" },
            ],
            show_bascet: false,
            errorList: [],
            cityFindetList: [],
            bascetInfo: {
                fio: "",
                email: "",
                phone: "",
                street: "",
                rayon: "",
                adress: "",
                home: "",
                podezd: "",
                etazg: "",
                kvartira: "",
                data: new Date(
                    Date.now() - new Date().getTimezoneOffset() * 60000,
                )
                    .toISOString()
                    .slice(0, 10),
                time: "",
                polname: "",
                polphone: "",
                comment: "",
            },
        };
    },

    components: {
        SelectInput,
        RadioBlocks,
        DateTimeSelect,
        ReceiverFields,
        DeliveryAddress,
        FormField,
    },

    watch: {
        "bascetInfo.street"(value) {
            if (value.length > 15) return;
            if (value.length < 5) return;
            if (value.length % 2 == 0) {
                this.searchCity(value);
            }
        },
    },

    computed: {
        // Время обязательно, если не выбраны "В ближайшее время" и "Уточнить у получателя"
        timeRequired() {
            return !this.orderAsap && !this.orderClarify;
        },

        // Адрес обязателен для доставки без галочки "Уточнить адрес у получателя"
        addressRequired() {
            return this.deliveryType == "Доставка" && !this.addressClarify;
        },

        "bascetInfo.adress"() {
            console.log(this.bascetInfo.street + ", " + this.bascetInfo.home);
            return this.bascetInfo.street + ", " + this.bascetInfo.home;
        },
    },

    mounted: function () {
        this.show_bascet = false;
        axios
            .get("/bascet/get")
            .then((response) => {
                console.log(response.data.user_info);
                if (response.data.user_info != null) {
                    this.bascetInfo.fio = response.data.user_info.name;
                    this.bascetInfo.phone = response.data.user_info.phone;
                    this.bascetInfo.email = response.data.user_info.email;
                }

                this.bascetList = response.data.position;
                this.updateBascet();
                this.show_bascet = true;
            })
            .catch((error) => console.log(error));
    },
    methods: {
        searchCity(value) {
            if (!value) return;
            console.log("do Query");
            const myGeocoder = ymaps.geocode(value, {
                boundedBy: [
                    [35.60239966406238, 51.28389916951991],
                    [36.94273169531238, 52.77458894685576],
                ],
                strictBounds: true,
                json: true,
            });

            myGeocoder.then((response) => {
                console.log(response.GeoObjectCollection.featureMember);
                this.cityFindetList = [];
                response.GeoObjectCollection.featureMember.forEach((obj) => {
                    // console.log(obj.GeoObject.metaDataProperty.GeocoderMetaData.Address.Components['name'])
                    this.cityFindetList.push(
                        obj.GeoObject.description + ", " + obj.GeoObject.name,
                    );
                });
            });
        },

        calcDelivery() {
            const map = {
                "Центр + проспект Победы, Дериглазова, ул. Карла-Маркса": {
                    price: 0,
                    zone: "Центр",
                },
                "Сеймский район (Парковая, Магистральный, Волокно)": {
                    price: 350,
                    zone: "Сеймский район",
                },
                "Северозападный район": {
                    price: 250,
                    zone: "Северозападный район",
                },
                "п. Искра": { price: 250, zone: "п. Искра" },
                "п. Татаренково": { price: 250, zone: "п. Татаренково" },
                "п. Косиновский": { price: 250, zone: "п. Косиновский" },
                КЗТЗ: { price: 300, zone: "КЗТЗ" },
                "Железнодорожный район": {
                    price: 300,
                    zone: "Железнодорожный район",
                },
                "Жукова, Халино, Щетинка": {
                    price: 600,
                    zone: "Жукова, Халино, Щетинка",
                },
                "Духовец, Кислино, Зорино, Лебяжье, Кукуевка, Гремячка": {
                    price: 600,
                    zone: "Духовец, Кислино, Зорино, Лебяжье, Кукуевка, Гремячка",
                },
            };

            const key = this.bascetInfo.rayon || "";
            const cfg = map[key];

            this.deliveryPrice = cfg ? cfg.price : 0;
            this.deliveryZone = cfg ? cfg.zone : "";
        },

        sendBascet() {
            this.errorList = [];

            if (!this.policy)
                this.errorList.push("Вы должны согласиться с политикой");

            if (this.bascetInfo.fio == "")
                this.errorList.push("Поле 'Имя' не заполнено");

            if (this.bascetInfo.phone == "")
                this.errorList.push("Поле 'Телефон' не заполнено");

            if (this.bascetInfo.data == "")
                this.errorList.push("Поле 'Дата' не заполнено");

            if (this.timeRequired && this.bascetInfo.time == "")
                this.errorList.push("Поле 'Время' не заполнено");

            // Поля получателя обязательны, если заказ забирает/получает другой человек
            if (this.receiverType) {
                if (this.bascetInfo.polname == "")
                    this.errorList.push("Поле 'Имя получателя' не заполнено");
                if (this.bascetInfo.polphone == "")
                    this.errorList.push(
                        "Поле 'Телефон получателя' не заполнено",
                    );
            }

            // Адрес обязателен для доставки без уточнения у получателя
            if (this.addressRequired) {
                if (this.bascetInfo.rayon == "")
                    this.errorList.push("Не выбран район доставки");
                if (this.bascetInfo.street == "")
                    this.errorList.push("Поле 'Улица' не заполнено");
                if (this.bascetInfo.home == "")
                    this.errorList.push("Поле 'Дом' не заполнено");
            }

            if (this.errorList.length != 0) return;

            this.loadet = true;
            axios
                .post("/bascet/send", {
                    _token: document.querySelector('meta[name="_token"]')
                        .content,
                    fio: this.bascetInfo.fio,
                    email: this.bascetInfo.email,
                    phone: this.bascetInfo.phone,
                    adress:
                        this.bascetInfo.street + ", " + this.bascetInfo.home,
                    comment: this.bascetInfo.comment,
                    tovars: this.bascetList,
                    rayon: this.bascetInfo.rayon,
                    deliveryprice: this.deliveryPrice,
                    amount:
                        parseFloat(this.subtotal) +
                        parseFloat(this.deliveryPrice),
                    count: this.count,
                    deliverytype: this.deliveryType,
                    delivery:
                        this.bascetInfo.street + " " + this.bascetInfo.home,
                    street: this.bascetInfo.street,
                    home: this.bascetInfo.home,
                    podezd: this.bascetInfo.podezd,
                    etazg: this.bascetInfo.etazg,
                    kvartira: this.bascetInfo.kvartira,
                    data: this.bascetInfo.data,
                    time: this.bascetInfo.time,
                    polname: this.bascetInfo.polname,
                    polphone: this.bascetInfo.polphone,
                    recipient_type:
                        this.deliveryType == "Доставка"
                            ? this.receiverType
                                ? "Другой человек"
                                : "Мне"
                            : this.receiverType
                              ? "Другой человек"
                              : "Я сам",
                    confirm_address_with_recipient: this.addressClarify,
                    anonymous_delivery: this.anonymousOrder,
                    deliver_asap: this.orderAsap,
                    agree_delivery_time_with_recipient: this.orderClarify,
                })
                .then((response) => {
                    this.loadet = false;
                    console.log(response);
                    DataLayer.sendCart(this.bascetList, response.data.order_id);
                    if (
                        response.data.pay_info != null &&
                        response.data.pay_info.confirmation.confirmation_url !==
                            undefined
                    ) {
                        console.log(response.data.pay_info);

                        document.location.href =
                            response.data.pay_info.confirmation.confirmation_url;
                        // document.location.href = "/bascet/thencs";
                    } else {
                        console.log(response.data.pay_info);
                        document.location.href = "/bascet/thencs";
                    }
                })
                .catch((error) => console.log(error));
        },

        updateItem(item) {
            axios
                .post("/bascet/update", {
                    _token: document.querySelector('meta[name="_token"]')
                        .content,
                    product_id: item.product_sku,
                    count: item.quentity,
                })
                .then(() => {
                    let bascet_counter =
                        document.querySelectorAll(".bascet_counter");
                    for (let elem of bascet_counter) {
                        elem.innerHTML = this.count;
                    }
                })
                .catch((error) => console.log(error));
        },

        updateBascet() {
            if (this.bascetList.length == 0) return;

            this.count = 0;
            this.subtotal = 0;
            for (let i = 0; i < this.bascetList.length; i++) {
                this.count += this.bascetList[i].quentity;
                this.subtotal +=
                    parseFloat(this.bascetList[i].quentity) *
                    parseFloat(this.bascetList[i].price);
            }

            this.$store.dispatch("initialBascet");
        },

        clearBascet() {
            axios
                .delete("/bascet/clear", {
                    _token: document.querySelector('meta[name="_token"]')
                        .content,
                })
                .then(() => {
                    this.count = 0;
                    this.subtotal = 0;
                    this.bascetList = [];
                    this.show_bascet = true;
                })
                .catch((error) => console.log(error));
            this.$store.dispatch("initialBascet");
        },

        deleteElement(item, index) {
            axios
                .delete("/bascet/delete", {
                    data: {
                        _token: document.querySelector('meta[name="_token"]')
                            .content,
                        product_id: item.product_sku,
                    },
                })
                .then(() => {
                    item.quentity = 0;
                    this.bascetList.splice(index, 1);
                    this.updateBascet();
                })
                .catch((error) => console.log(error));
        },
    },
};
</script>

<style>
.check_label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 10px 0;
    cursor: pointer;
}

.check_label input {
    width: unset !important;
    min-height: unset;
    margin: 0;
    accent-color: #004f44;
}

.pay_type_intro {
    font-size: 12px;
    margin: 0;
}

.pay_icons {
    list-style: none;
    padding: 0;
    margin: 10px 0 20px;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    gap: 15px;
    max-width: 320px;
}

.pay_icons li {
    display: flex;
    align-items: center;
    justify-content: center;
}

.pay_icons li img {
    display: block;
    max-height: 20px;
    height: 20px;
    width: auto;
}
</style>
