<template>
    <div v-show="showDrawer" class="cart_drawer_wrapper" @click.self="closeCart">
        <aside class="cart_drawer">
            <div class="cart_drawer__head">
                <h2 class="cart_drawer__title">Корзина</h2>
                <button
                    class="cart_drawer__close"
                    aria-label="Закрыть корзину"
                    @click="closeCart"
                ></button>
            </div>

            <div v-if="items.length != 0" class="cart_drawer__body">
                <div
                    v-for="item in items"
                    :key="item.product_sku"
                    class="cart_drawer__item"
                >
                    <div class="item_info">
                        <div class="item_picture">
                            <img
                                v-if="item.tovar_data && item.tovar_data.img"
                                :src="item.tovar_data.img"
                                :alt="item.tovar_data.title"
                            />
                            <img v-else :src="'/img/noPhoto.jpg'" alt="" />
                        </div>
                        <div class="item_descr">
                            <h3 class="item_title">
                                {{ item.tovar_data ? item.tovar_data.title : "" }}
                            </h3>
                            <div class="item_price">
                                {{
                                    Number(parseFloat(item.price)).toLocaleString(
                                        "ru-RU",
                                    )
                                }}
                                ₽
                            </div>
                        </div>
                        <button
                            class="item_remove"
                            aria-label="Удалить товар"
                            @click="removeItem(item)"
                        ></button>
                    </div>

                    <div class="item_controls">
                        <div class="number_wrapper">
                            <span
                                class="number_btn val_down"
                                @click="downItem(item)"
                                >−</span
                            >
                            <input
                                type="number"
                                v-model.number="item.quentity"
                                min="1"
                                readonly
                            />
                            <span
                                class="number_btn val_upp"
                                @click="upItem(item)"
                                >+</span
                            >
                        </div>
                        <div class="item_summ">
                            <span class="item_summ__label">Сумма</span>
                            <span class="item_summ__value">
                                {{
                                    Number(
                                        parseFloat(item.quentity) *
                                            parseFloat(item.price),
                                    ).toLocaleString("ru-RU")
                                }}
                                ₽
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="cart_drawer__empty">
                <p>Ваша корзина пуста</p>
            </div>

            <div class="cart_drawer__footer">
                <div class="cart_drawer__total">
                    <span>К оплате</span>
                    <span class="total_value"
                        >{{ Number(total).toLocaleString("ru-RU") }} ₽</span
                    >
                </div>
                <a
                    v-if="items.length != 0"
                    href="/bascet"
                    class="button price_button cart_drawer__order_btn"
                    >Оформить заказ</a
                >
            </div>
        </aside>
    </div>
</template>

<script>
import { computed } from "vue";
import { useStore } from "vuex";

export default {
    setup() {
        const store = useStore();

        const items = computed(() =>
            Array.isArray(store.state.cart_tovars) ? store.state.cart_tovars : [],
        );

        const total = computed(() => {
            return items.value.reduce(
                (sum, item) =>
                    sum + parseFloat(item.quentity) * parseFloat(item.price),
                0,
            );
        });

        const closeCart = () => store.commit("closeCart");

        const upItem = (item) => {
            store.dispatch("updateCartItem", {
                sku: item.product_sku,
                count: parseInt(item.quentity) + 1,
            });
        };

        const downItem = (item) => {
            if (parseInt(item.quentity) <= 1) {
                removeItem(item);
                return;
            }
            store.dispatch("updateCartItem", {
                sku: item.product_sku,
                count: parseInt(item.quentity) - 1,
            });
        };

        const removeItem = (item) => {
            store.dispatch("deleteCartItem", item.product_sku);
        };

        return {
            showDrawer: computed(() => store.state.cart_open),
            items,
            total,
            closeCart,
            upItem,
            downItem,
            removeItem,
        };
    },
};
</script>

<style scoped>
.cart_drawer_wrapper {
    width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 11000;
    background-color: #000000aa;
    display: flex;
    justify-content: flex-end;
}

.cart_drawer {
    width: 480px;
    max-width: 100%;
    height: 100%;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.cart_drawer__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 25px 30px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
}

.cart_drawer__title {
    font-size: 28px;
    margin: 0;
}

.cart_drawer__close {
    width: 20px;
    height: 20px;
    border: none;
    padding: 0;
    cursor: pointer;
    background: url("../../../../public/img/icons/shop_icon/close.svg") 0 0
        no-repeat;
}

.cart_drawer__body {
    flex: 1 1 auto;
    overflow-y: auto;
    padding: 10px 30px;
}

.cart_drawer__item {
    padding: 20px 0;
    border-bottom: 1px solid #f0f0f0;
}

.item_info {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.item_picture {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 10px;
}

.item_picture img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item_descr {
    flex: 1 1 auto;
}

.item_title {
    font-size: 16px;
    margin: 0 0 8px 0;
}

.item_price {
    font-weight: 600;
}

.item_remove {
    width: 20px;
    height: 20px;
    border: none;
    padding: 0;
    flex-shrink: 0;
    cursor: pointer;
    background: url("../../../../public/img/icons/shop_icon/close.svg") 0 0
        no-repeat;
    opacity: 0.6;
}

.item_remove:hover {
    opacity: 1;
}

.item_controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 15px;
}

.number_wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}

.number_wrapper .number_btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    user-select: none;
    font-size: 18px;
}

.number_wrapper .number_btn:hover {
    background-color: #f5f5f5;
}

.number_wrapper input {
    width: 45px;
    height: 40px;
    border: none;
    text-align: center;
    font-size: 16px;
    -moz-appearance: textfield;
}

.number_wrapper input::-webkit-outer-spin-button,
.number_wrapper input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.item_summ {
    text-align: right;
}

.item_summ__label {
    display: block;
    font-size: 12px;
    color: #999;
}

.item_summ__value {
    font-weight: 700;
    font-size: 17px;
}

.cart_drawer__empty {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
}

.cart_drawer__footer {
    flex-shrink: 0;
    padding: 20px 30px 30px;
    border-top: 1px solid #f0f0f0;
}

.cart_drawer__total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 16px;
    color: #999;
}

.cart_drawer__total .total_value {
    color: #000;
    font-size: 22px;
    font-weight: 700;
}

.cart_drawer__order_btn {
    width: 100%;
    text-align: center;
}

/* Мобильная версия — окно на весь экран */
@media (max-width: 768px) {
    .cart_drawer {
        width: 100%;
        height: 100dvh;
    }

    .cart_drawer__head {
        padding: 20px;
    }

    .cart_drawer__body {
        padding: 10px 20px;
    }

    .cart_drawer__footer {
        padding: 15px 20px 25px;
    }
}
</style>
