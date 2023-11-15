
<template>
   <a href="/bascet" v-bind:class="{ 'active': active }"  class="bascet_head pi florida_cart">Корзина | <span class="bascet_counter">{{count}}</span>
</a>

</template>

<script>

import { useStore } from 'vuex'
import { computed } from 'vue'

export default {

    setup() {
        const store = useStore()

        return {
            count:computed(() => store.getters.cartCount),
            active:computed(() => store.getters.cartCount > 0)
        }
    }
}
</script>

<style>

</style>
