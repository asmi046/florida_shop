<template>
    <platform-select :list="allPlatforms" v-model="reviewFilter"></platform-select>


    <swiper-container
        slides-per-view="1.4"
        space-between="40"
        ref="swiper"
    >
            <swiper-slide v-for="item in allRevews[reviewFilter]" :key="item.id">
                <review-card :item="item"></review-card>
            </swiper-slide>
    </swiper-container>


    <div class="slider_btn_wrapper">
        <button @click.prevent="prevSlide" id="main_cat_slider_btn_prev" class="slider_btn slider_prev">
            <svg class="sprite_icon">
                <use xlink:href="#arrow"></use>
            </svg>
        </button>

        <button @click.prevent="nextSlide" id="main_cat_slider_btn_next" class="slider_btn slider_next">
            <svg class="sprite_icon">
                <use xlink:href="#arrow"></use>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import PlatformSelect from './PlatformSelect.vue';
import ReviewCard from './ReviewCard.vue';

const swiper = ref()






onMounted(() => {
      swiper.value.breakpoints = {
            768: {
                slidesPerView: 2.4,

            },
            1024: {
                slidesPerView: 3.4,
            },

            1480: {
                slidesPerView: 3.4,
            },

            1920: {
                slidesPerView: 4,
            }
        }
})

const prevSlide = () => {
    swiper.value.swiper.slidePrev()
}

const nextSlide = () => {
    swiper.value.swiper.slideNext()
}

let reviewFilter = ref('all')
let allPlatforms = ref([])
let allRevews = ref([])

const getData = () => {
    axios.get('/all_rewiews')
        .then((response) => {
            allPlatforms.value = response.data.platforms;
            allRevews.value = response.data.reviews;
            console.log(response)
        })
        .catch( (error) => {
            console.log(error)
        });
}

getData();

</script>
