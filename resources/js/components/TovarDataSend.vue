<template>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import {DataLayer} from '../eCommerceYandex.js'

const props = defineProps({
  tovardata: Object
})

onMounted(() => {
    DataLayer.sendDetail( props.tovardata )
})


</script>
