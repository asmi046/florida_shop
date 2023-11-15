<template>
    <span class="bascet_counter">{{count}}</span>
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
