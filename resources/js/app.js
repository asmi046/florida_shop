import './bootstrap';

import {createApp} from 'vue/dist/vue.esm-bundler';

import BascetCounter from "./components/bascet/BascetCounter.vue"
import BascetAndCounter from "./components/bascet/BascetAndCounter.vue"
import MapInPage from "./components/MapInPage.vue"
import ModalWindow from "./components/ModalWindow.vue"
import Bascet from "./components/bascet/Bascet.vue"
import OneClickBuyWindow from "./components/OneClickBuyWindow.vue"

import ToBascetBtnPage from './components/ToBascetBtnPage.vue'

import axios from 'axios'

import VueAxios from 'vue-axios'

import './sliders.js'

import { VMaskDirective } from 'v-slim-mask'

import { store } from "./storage"
import { useStore } from 'vuex'

const global_app = createApp({
    components:{
        MapInPage,
        ModalWindow,
        Bascet,
        OneClickBuyWindow,
        BascetCounter,
        BascetAndCounter,
        ToBascetBtnPage
    },

    setup() {
        const store = useStore()

        store.dispatch('initialBascet');
        store.dispatch('initialFavorites');
    }
})

global_app.use(VueAxios, axios)
global_app.use(store)
global_app.directive('mask', VMaskDirective)
global_app.mount("#global_app");

