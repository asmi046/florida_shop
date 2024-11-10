<template>
    <div class="reviews_card">
        <div class="top_blk">
            <div class="photo">
                <img :src="(props.item.img)?props.item.img:asset_path+'img/person_no_photo.jpg'" alt="">
            </div>
            <div class="person">
                <vue3-star-ratings v-model="props.item.score" star-size="18" star-color="#FFDE00" inactive-color="lightgray" :disable-click="false" />
                <p>{{ props.item.name }}</p>
            </div>
        </div>
        <div class="text">{{ props.item.description.slice(0,150) }} {{ props.item.description.length > 150?"...":"" }}</div>
        <a target="_blank" :href="props.item.platform_lnk" class="platform">Смотреть отзыв в {{ props.item.platform }}</a>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import vue3StarRatings from "vue3-star-ratings";

const base_path = window.storage || '';
const asset_path = window.asset || '';

const props = defineProps({
  item: Object
})

</script>
