<template>

    <a v-if="!inBascet" href="#" @click.prevent="addToBascet" class="button price_button">Купить</a>
    <a v-else :href="bascet" class="button price_button">В корзине {{ inBascetCount }} шт.</a>

</template>

<script>

import { ref, computed, watch } from 'vue'
import { useStore } from 'vuex'

import {DataLayer} from '../eCommerceYandex.js'

export default {

props: {
    sku:String,
    skuid:Number,
    bascet:String
},

setup(props){

    const store = useStore()

    let countToAdd = ref(1)
    let inBascet = ref(false)
    let inBascetCount = ref(1)


    watch(() => [store.getters.cartCount,props.skuid], function() {
        console.log(store.state.cart_tovars)
        let inBascetElem = store.state.cart_tovars.find((elem) => { return elem.product_sku === props.sku})
        inBascet.value = (inBascetElem != undefined)
        inBascetCount.value =  (inBascetElem != undefined)?inBascetElem.quentity:0
    });

    const addToBascet = () => {
        let tiken = document.querySelector('meta[name="_token"]').content;
        console.log(props.sku)
        axios.post('/bascet/add', {
            'product_id': props.sku,
            'addcount':1,
            '_token': tiken
        })
        .then((result) => {
            console.log(result)
            DataLayer.addToCatr( result.data[2], 1)
            store.dispatch('initialBascet')
            console.log(store.getters.cartCount)
        })
        .catch(error => console.log(error));
    }

    const upCounter = () => {
        countToAdd.value++
    }

    const downCounter = () => {
        if (countToAdd.value == 1) return;
        countToAdd.value--
    }

    return {
        inBascet,
        upCounter,
        downCounter,
        countToAdd,
        inBascetCount,
        addToBascet,
        bascet:props.bascet
    }
}

}
</script>

<style>

</style>
