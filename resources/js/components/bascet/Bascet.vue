<template>
    <div v-show="!show_bascet" class="bascet_lader">
        <span></span>
        <p>Загружаем корзину...</p>
    </div>
    <div  v-show="bascetList.length != 0" class="bascet">
        <div class="bascet_tovar">
            <div class="control">
                <a @click.prevent="clearBascet()" class="clear_bascet_btn" href="#"><span>Очистить корзину</span></a>
            </div>

            <div class="tovar_list">

                <div v-for="(item, index) in bascetList" :key="item.product_sku" class="tovar">

                    <div class="tl-side left-side">
                        <div class="tovar_all_blk picture_blk">
                            <img v-if="item.tovar_data.img != ''" :src="item.tovar_data.img" alt="">
                            <img v-else else :src="noPhotoUrl" alt="">

                        </div>
                        <div class="tovar_all_blk name_blk">
                            <h2>{{item.tovar_data.title}}</h2>
                            <p> Артикул: {{item.product_sku}}</p>
                        </div>
                    </div>

                    <div class="tl-side right-side">
                        <div class="tovar_all_blk price_blk">
                            <span class="rub price_formator">{{Number(item.price).toLocaleString('ru-RU')}} <span class="rub_symbol">₽</span></span>
                        </div>
                        <div class="tovar_all_blk couint_blk">
                            <div class="number_wrapper">
                                <span @click="item.quentity--; updateBascet(); updateItem(item)" class="number_btn val_down">-</span>
                                <input type="number"  :value="item.quentity">
                                <span @click="item.quentity++; updateBascet(); updateItem(item)" class="number_btn val_upp">+</span>
                            </div>
                        </div>
                        <div class="tovar_all_blk summ_blk">
                            <span class="rub price_formator">{{Number(parseFloat(item.quentity)*parseFloat(item.price)).toLocaleString('ru-RU')}} <span class="rub_symbol">₽</span></span>
                        </div>
                        <div class="tovar_all_blk dll_blk">
                            <span @click.prevent="deleteElement(item, index)" title="Удалить товар"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="itogo">
                <div class="itogo_price_count">
                    <div class="itogo_row">
                        <span class="text">Товары (<span>{{count}}</span>)</span>
                        <span class="razd"></span>
                        <span class="p_price rub price_formator">{{Number(subtotal).toLocaleString('ru-RU')}} <span class="rub_symbol">₽</span></span>
                    </div>

                    <div v-show="deliveryPrice>0" class="itogo_row">
                        <span class="text">Доставка (Зона {{deliveryZone}})</span>
                        <span class="razd"></span>
                        <span class="p_price rub price_formator">{{Number(deliveryPrice).toLocaleString('ru-RU')}} <span class="rub_symbol">₽</span></span>
                    </div>

                    <div class="itogo_row itogo_row_final">
                        <span class="text">Итого</span>
                        <span class="razd"></span>
                        <span class="p_price rub price_formator">{{Number(subtotal + deliveryPrice).toLocaleString('ru-RU')}} <span class="rub_symbol">₽</span></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="bascet_form">
            <h2>Контактные данные</h2>
            <form action="GET">
                <input v-model="bascetInfo.fio" name="fio" type="text" placeholder="Фамилия, Имя*">
                <input v-model="bascetInfo.email" name="email" type="email" placeholder="e-mail">
                <input v-model="bascetInfo.phone" v-mask="{mask: '+N (NNN) NNN-NN-NN', model: 'cpf' }" name="phone" type="text" placeholder="Телефон*">
                <div class="adr_wrapper">
                    <select-input v-model="bascetInfo.street" :puncts="cityFindetList"></select-input>

                    <input v-model="bascetInfo.home" @keydown="calcDelivery" name="home" type="text" placeholder="Дом">
                </div>
                <textarea v-model="bascetInfo.comment" name="comment" placeholder="Комментарий"></textarea>
                <ul v-show="errorList.length != 0" class ="errors_list">
                    <li v-for="item in errorList" :key="item">{{item}}</li>
                </ul>

                <button @click.prevent="sendBascet()" class="btn bascetSendBtn" type="submit">Оформить заказ</button> <span :class="{active: loadet }" class="btnLoaderCart shoved"></span>
                <p class="policy">Заполняя данную форму и отправляя заказ вы соглашаетесь с <a href="#">политикой конфиденциальности</a></p>
            </form>
        </div>
    </div>
    <div class="empty_bascet" v-show="show_bascet && bascetList.length == 0">
        <img src="../../../../public/img/icons/cart.svg" alt="">
        <h3>Ваша корзина пуста</h3>
        <p>Жмите на значек корзиныи добавляйте товар!</p>
    </div>
    <div ref="mapInComponent"> </div>
</template>

<script>
import Delivery from '../../lib/delivery.js'
import SelectInput from '../selectInput.vue'
export default {
    data() {
        return {
            noPhotoUrl:"img/noPhoto.jpg",
            bascetList:[],
            loadet:false,
            count:0,
            subtotal:0,
            deliveryPrice:0,
            deliveryZone:'',
            show_bascet:false,
            errorList:[],
            cityFindetList:[ ],
            bascetInfo:{
                fio:"",
                email:"",
                phone:"",
                street:"",
                adress:"",
                home:"",
                comment:"",
            }
        }
    },

    components:{
        SelectInput
    },

    watch: {
        'bascetInfo.street'(value) {
            if (value.length > 15) return;
            if (value.length < 5) return;
            if ((value.length % 2) == 0) {
                this.searchCity(value)
            }

        }
    },

    computed: {
        'bascetInfo.adress'() {
            console.log(this.bascetInfo.street+", "+this.bascetInfo.home)
            return this.bascetInfo.street+", "+this.bascetInfo.home;
        }
    },

    mounted: function() {
        this.show_bascet = false;
        axios.get('/bascet/get')
            .then((response) => {
                console.log(response.data.user_info)
                if (response.data.user_info != null) {
                    this.bascetInfo.fio = response.data.user_info.name
                    this.bascetInfo.phone = response.data.user_info.phone
                    this.bascetInfo.email = response.data.user_info.email
                }

                this.bascetList = response.data.position
                this.updateBascet()
                this.show_bascet = true
            })
            .catch(error => console.log(error));
    },
    methods: {

        searchCity(value) {
                if (!value) return;
                console.log('do Query')
                const myGeocoder = ymaps.geocode(value, {
                    boundedBy:[[35.60239966406238,51.28389916951991],[36.94273169531238,52.77458894685576]],
                    strictBounds: true,
                    json:true,
                });

                myGeocoder.then( (response) => {
                    console.log(response.GeoObjectCollection.featureMember)
                    this.cityFindetList = []
                    response.GeoObjectCollection.featureMember.forEach(obj => {
                                    // console.log(obj.GeoObject.metaDataProperty.GeocoderMetaData.Address.Components['name'])
                                    this.cityFindetList.push(obj.GeoObject.description+", "+obj.GeoObject.name)
                                })
                })

        },

        calcDelivery() {
            let mapClass = new Delivery(this.$refs.mapInComponent, false)

            mapClass.getDeliveryPrice(this.bascetInfo.street+", "+this.bascetInfo.home).then((data)=>{
                console.log(data)
                this.deliveryPrice=data.price
                this.deliveryZone=data.description


            })
        },

        sendBascet() {

            this.errorList = []

            if (this.bascetInfo.fio == "")
                this.errorList.push("Поле 'Имя' не заполнено");

            if (this.bascetInfo.phone == "")
                this.errorList.push("Поле 'Телефон' не заполнено");

            if (this.errorList.length != 0 ) return;

            this.loadet = true;
            axios.post('/bascet/send', {
                _token: document.querySelector('meta[name="_token"]').content,
                fio: this.bascetInfo.fio,
                email: this.bascetInfo.email,
                phone: this.bascetInfo.phone,
                adress:  this.bascetInfo.street+", "+this.bascetInfo.home,
                comment: this.bascetInfo.comment,
                tovars: this.bascetList,
                amount: parseFloat(this.subtotal) + parseFloat(this.deliveryPrice),
                count: this.count,
                delivery: this.bascetInfo.street+" "+this.bascetInfo.home,
            })
            .then((response) => {
                this.loadet = false;
                console.log(response)
                // if (response.data.pay_info.formUrl !== undefined)
                //     document.location.href=response.data.pay_info.formUrl
                // else {
                //     console.log(response.data.pay_info)
                //     document.location.href = "/bascet/thencs"
                // }
            })
            .catch(error => console.log(error));
        },

        updateItem(item){
            axios.post('/bascet/update', {
                _token: document.querySelector('meta[name="_token"]').content,
                product_id: item.product_sku,
                count: item.quentity
            })
            .then(() => {

                let bascet_counter = document.querySelectorAll(".bascet_counter")
                for (let elem of bascet_counter) {
                    elem.innerHTML = this.count;
                }
            })
            .catch(error => console.log(error));
        },

        updateBascet() {
            if (this.bascetList.length == 0) return;

            this.count = 0;
            this.subtotal = 0;
            for (let i = 0; i<this.bascetList.length; i++) {
                this.count+=this.bascetList[i].quentity
                this.subtotal+=parseFloat(this.bascetList[i].quentity)*parseFloat(this.bascetList[i].price)
            }
        },

        clearBascet() {
            axios.delete('/bascet/clear', {
                _token: document.querySelector('meta[name="_token"]').content
            })
            .then(() => {
                this.count = 0
                this.subtotal = 0
                this.bascetList = []
                this.show_bascet = true
            })
            .catch(error => console.log(error));
        },

        deleteElement(item,index) {
            axios.delete('/bascet/delete', {
                data: {
                    _token: document.querySelector('meta[name="_token"]').content,
                    product_id: item.product_sku
                }
            })
            .then(() => {
                item.quentity = 0
                this.bascetList.splice(index, 1)
                this.updateBascet()

            })
            .catch(error => console.log(error));
        }
    }

}
</script>

<style>

</style>
