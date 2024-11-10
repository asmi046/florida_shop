<template>
    <div class="review_selector_wrapper">
        <div class="review_selector">
            <a href="#" @click.prevent="selecReviewPlatform('all')" :class="{active: selectedElement == 'all'}" class="selector all_reviews ">Все отзывы</a>
            <a href="#" @click.prevent="selecReviewPlatform('Yandex')"  :class="{active: selectedElement == 'Yandex'}" class="selector ya_reviews">Яндекс</a>
            <a href="#" @click.prevent="selecReviewPlatform('2gis')" :class="{active: selectedElement == '2gis'}" class="selector gis_reviews ">2Gis</a>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    modelValue: String
})

let selectedElement = ref("")
selectedElement.value = props.modelValue?props.modelValue:"all"

const emit = defineEmits(['update:modelValue'])

const selecReviewPlatform = (element) => {
    selectedElement.value = element
    emit('update:modelValue', element)
}


</script>
