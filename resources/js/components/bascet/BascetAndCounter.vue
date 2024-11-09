
<template>
    <span class="bascet_counter_new">{{count}}</span>
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
