<template>
    <h2>Тест карты</h2>

    <div ref="mapInComponent" id="map" class="myMap"></div>
</template>

<script>

    import delivery from "../lib/delivery.js"

    export default {
        methods:{
            export() {
                return 0;
            }
        },

        mounted:function(){

            console.log(delivery.zones);
            ymaps.ready( () => {
                console.log(delivery.zones);
                delivery.renderMap(this.$refs.mapInComponent)
            })

        }
    }
</script>

<style>
    .myMap{
        width: 100%;
        height: 300px;
    }

</style>
