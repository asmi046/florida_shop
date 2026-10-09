import './bootstrap';

import { createApp } from 'vue/dist/vue.esm-bundler';

import Splide from '@splidejs/splide';
import { Video } from '@splidejs/splide-extension-video';

window.Splide = Splide;
window.SplideVideo = Video;

import Review from './components/Reviews/Review.vue'
import BascetCounter from "./components/bascet/BascetCounter.vue"
import BascetAndCounter from "./components/bascet/BascetAndCounter.vue"
import MapInPage from "./components/MapInPage.vue"
import ModalWindow from "./components/ModalWindow.vue"
import Bascet from "./components/bascet/Bascet.vue"
import OneClickBuyWindow from "./components/OneClickBuyWindow.vue"

import ToBascetBtnPage from './components/ToBascetBtnPage.vue'
import TovarDataSend from './components/TovarDataSend.vue'
import CookiesWarning from "./components/CookiesWarning.vue"

import axios from 'axios'

import VueAxios from 'vue-axios'

import './sliders.js'
import './scroll.js'

import Glightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.css'

const glightboxOptions = {
    touchNavigation: true,
    loop: false,
    draggable: true,
    autoplay: true,
    openEffect: 'zoom',
    closeEffect: 'zoom',
    cssText: {
        '.gslide': {
            'background-color': '#000',
        }
    }
}

let videoLightbox = null

// Vue компилирует содержимое #global_app и пересоздаёт узлы при обновлениях,
// поэтому не полагаемся на привязку glightbox к элементам при инициализации.
// Клик обрабатывается делегированием на document, а в lightbox попадает
// только один триггер — так каждая карточка открывает свой ролик.
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('.glightbox')
    if (!trigger) return

    event.preventDefault()

    if (!videoLightbox) {
        videoLightbox = Glightbox({ ...glightboxOptions, elements: [] })
    }

    videoLightbox.settings.elements = [trigger]
    videoLightbox.elements = []
    videoLightbox.reload()
    videoLightbox.open(trigger)
})

import { VMaskDirective } from 'v-slim-mask'

import { store } from "./storage"
import { useStore } from 'vuex'

// Импорт Maskito
import { Maskito } from '@maskito/core';
import phoneMaskOptions from './mask';

const global_app = createApp({
    components: {
        Review,
        TovarDataSend,
        MapInPage,
        ModalWindow,
        Bascet,
        OneClickBuyWindow,
        BascetCounter,
        BascetAndCounter,
        ToBascetBtnPage,
        CookiesWarning
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
// Директива для маски телефона
global_app.directive('phone-mask', {
    mounted(el) {
        // Создаем экземпляр Maskito для элемента
        new Maskito(el, phoneMaskOptions);
    }
})
global_app.mount("#global_app");

