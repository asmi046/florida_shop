<template>
    <h2>Тест карты</h2>

    <div ref="mapInComponent" id="map" class="myMap"></div>
</template>

<script>
    import Delivery from '../lib/delivery.js';

    export default {
        data() {
            return {
                mapClass:null
            }
        },

        methods:{
            export() {
                return 0;
            }
        },

        mounted:function(){
            this.mapClass = new Delivery(this.$refs.mapInComponent, true)
        }
    }
</script>

<style>
    .myMap{
        width: 100%;
        height: 300px;
    }

</style>
