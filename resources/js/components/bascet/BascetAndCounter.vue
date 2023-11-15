
<template>
   <a href="/bascet" :class="{ active: count>0 }"  class="bascet_head pi florida_cart">Корзина | <span class="bascet_counter">{{count}}</span>
</a>

</template>

<script>

import { useStore } from 'vuex'
import { computed } from 'vue'

export default {

    setup() {
        const store = useStore()

        return {
            count:computed(() => store.getters.cartCount)
        }
    }
}
</script>

<style>

</style>
