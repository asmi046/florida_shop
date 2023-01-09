<template>

    <section id="delivery_zones" >
        <div class="_wrapper delivery_zones">
            <div class="all_side left">
                <h2>Зоны достаки</h2>
                <p>Доставка осуществляется по городу в течение 1,5 часа нашей собственной службой доставки, открытка с пожеланием при вручении - в подарок. В праздничные дни, временной интервал доставки 3 часа!</p>
                <div class="zones_history">
                    <div v-for="(item, zones) in zones" :key="zones" class="zh_blk">
                        <span :style="{ backgroundColor: item.properties.fill }" class="text"></span>
                        <span class="descr">{{item.properties.description}}</span>
                    </div>
                </div>
            </div>
            <div class="all_side right">
                <div @change="test" ref="mapInComponent" id="map" class="myMap"></div>
            </div>
        </div>
    </section>


</template>

<script>
    import Delivery from '../lib/delivery.js';

    export default {
        data() {
            return {
                mapClass:null,
                zones:null
            }
        },

        methods:{
            test() {
                console.log(this.mapClass.getZoneCoord());
            },
            export() {
                return 0;
            }
        },

        mounted:function(){
            this.mapClass = new Delivery(this.$refs.mapInComponent, true)
            this.mapClass.getZones().then(async response => {
                this.zones = response.features
                console.log(response.features);
            })

        },

    }
</script>

<style>

    .delivery_zones {
        display: flex;
        justify-content: space-between;
    }

    .delivery_zones .all_side{
        width:48%;
    }

    .myMap{
        width: 100%;
        height: 400px;
        border-radius: 11px;
        overflow: hidden;
        border: 1px solid #D2D1D1;
    }

    .zones_history .zh_blk .text{
        width:15px;
        height:15px;
        border-radius: 10px;
        margin: auto 10px auto 0;
    }

    .zones_history .zh_blk{
        width:48%;
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;

    }

    .zones_history {
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
    }

    @media (max-width: 912px){
        .delivery_zones {
            flex-direction: column;
        }

        .delivery_zones .all_side{
            width:100%;
        }

        .delivery_zones .right {
            margin-top: 30px;
        }
    }

</style>
