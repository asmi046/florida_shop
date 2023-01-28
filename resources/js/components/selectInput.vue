<template>
    <div class="pds_select_wrapper">
        <input v-model="value" name="street" type="text" placeholder="Улица">
        <div v-show="showList" class="list_pds">
            <div class="list_scroller">
                <div @click="select_value(item)"  v-for="item in actualList" :key="item"  class="one_city">{{item}}</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
           showList:true,
           fixValue:false,
        }
    },

    props:['puncts', 'modelValue'],
    emit:['update:modelValue'],

    watch: {

        modelValue(value) {
            if (this.puncts.indexOf(value) > 0)
                this.fixValue=false
        }
    },

    computed: {

        value: {
            get() {
                return this.modelValue
            },
            set(value) {
                this.$emit('update:modelValue', value)
            }
        },

        actualList() {
            if ((this.puncts.length == 0) || this.fixValue) this.showList=false
            else this.showList=true

            return this.puncts

        }
    },

    methods:{

        select_value(value) {
            this.value = value
            this.showList = false
            this.fixValue = true
        }
    }
}
</script>

<style scoped>

.pds_select_wrapper{
    position:relative;
}

.pds_select_wrapper .list_pds .list_scroller
{
    width:100%;
    height: 170px;
    overflow: auto;
}

.pds_select_wrapper .list_pds .one_city:hover{
    color:#839F60;
}

.pds_select_wrapper .list_pds .one_city{
    padding:  5px 0;
    border-bottom: 1px dotted lightgray;
    margin-right: 5px;
    cursor:pointer;

}

.pds_select_wrapper .list_pds {
    position: absolute;
    left:0;
    top:65px;
    width:100%;
    min-height: 200px;
    max-height: 200px;
    border-radius: 12px;
    z-index: 222;
    background-color: white;
    border:2px solid #F4F4F4;
    padding: 15px;
    overflow: hidden;
}
</style>
