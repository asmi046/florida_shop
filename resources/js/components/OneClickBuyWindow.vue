<template>
    <div v-show="showModal" @click.self="closeWin()" class="popup_wrapper">
        <div class="popup_c">
            <div @click.prevent="closeWin()" class="popup__close" aria-label="Закрыть модальное окно"></div>

            <div v-show="showLoader" class="bascet_lader">
                <span></span>
                <p>Загружаем товар...</p>
            </div>

            <div v-show="!showLoader" class="tInfo">
                <h2 class="modal_h2">Купить в один клик</h2>
                <div class="ocbuy_tovar">
                    <div class="photo">
                        <img :src="tovarInfo.img" alt="">
                    </div>
                    <div class="info">
                        <strong>{{ tovarInfo.title }}</strong>
                        <p class="sku_p">Артикул: {{ tovarInfo.sku }}</p>
                        <p>
                            {{ tovarInfo.price }} <span class="rub_symbol">₽</span>
                            <span v-show="tovarInfo.old_price != 0" class="old_price">{{ tovarInfo.old_price }} <span class="rub_symbol">₽</span></span>
                        </p>
                    </div>
                </div>
                <form class="buy_form" action="">
                    <input v-model="phone" @focus="phoneError=false" :class="{_error:phoneError}" v-mask="{mask: '+7 (NNN) NNN-NN-NN', model: 'cpf' }" type="tel" placeholder="Введите телефон" />
                    <button @click.prevent="buyTovar" class="btn">Купить</button>
                    <p class="policy_des">Заполняя данную форму и отправляя заказ вы соглашаетесь с <a href="/policy">политикой конфиденциальности</a></p>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import AddToBascetBtn from './AddToBascetBtn.vue'
export default {
    components: { AddToBascetBtn },
    data() {
        return {
            tovarId:0,
            phone:"",
            phoneError:false,
            tovarInfo:{
                id:"",
                title:"",
                img:"",
                brand:"",
                state:"",
                sku:"",
                price:0,
                old_price:0,
                insklad:0,
                product_propertys:[]
            },
            showModal:false,
            showLoader:true,
            _token: document.querySelector('meta[name="_token"]').content,
        }
    },

    methods:{
        closeWin() {
            this.showModal = false
            history.pushState('', document.title, window.location.pathname+window.location.search)
        },

        openWin() {
            if (location.hash.indexOf("ocbuy_") !== -1){
                this.showModal = true
                this.tovarId = location.hash.split('_')[1]
                this.getTovarData()
            }
        },

        getTovarData() {

            if (this.tovarId == 0) return;

            this.showLoader = true;
            axios.get("/get_product_info/" + this.tovarId, {
                _token: this._token,
            })
            .then((response) => {
                console.log(response)
                this.showLoader = false
                this.tovarInfo = response.data.product
                this.tovarInfo.img = response.data.main_img
            })
            .catch( error => console.log(error));
        },

        buyTovar() {
            console.log(this.phone)

            let re = /([\+]?[7|8][\s-(]?[9][0-9]{2}[\s-)]?)?([\d]{3})[\s-]?([\d]{2})[\s-]?([\d]{2})/

            if (!re.test(this.phone)) {
                this.phoneError = true
                return
            }




            const tovar_position = [
                    {
                        product_sku:this.tovarInfo.sku,
                        quentity:1,
                        price:this.tovarInfo.price,

                        tovar_data: {
                            img:this.tovarInfo.img,
                            brand:this.tovarInfo.brand,
                            state:this.tovarInfo.state,
                            title:this.tovarInfo.title,
                            price:this.tovarInfo.price,
                            quentity:1
                        }
                    }
                ]

                console.log(tovar_position)

            axios.post('/bascet/ocsend', {
                _token: document.querySelector('meta[name="_token"]').content,
                fio: "Покупка в 1 клик",
                name: "Покупка в 1 клик",
                id:this.tovarInfo.id,
                phone: this.phone,
                tovars: tovar_position,
            })
            .then((response) => {
                this.loadet = false;
                console.log(response)
                document.location.href="/bascet/thencs"
            })
            .catch(error => console.log(error));
        }
    },

    mounted() {
        window.addEventListener('hashchange', this.openWin)

        this.openWin()
    }
}
</script>

<style scoped>

input._error {
  border: 1px solid #ff000033;
  background-color: #ff000013;
}
    .popup_wrapper {
        width:100%;
        height: 100%;
        display: flex;
        position: fixed;
        left: 0;
        top:0;
        z-index: 11000;
        background-color: #000000aa;
        color:#333;
        overflow-y: scroll;
        padding: 20px 0;
    }

    .popup_c {
        width:40%;
        min-width: 300px;
        max-width: 500px;
        height: auto;
        background-color: white;
        margin: auto;
        border-radius: 11px;
        position:relative;
        padding: 40px;
    }

    .popup__close {
        position: absolute;
        top: 18px;
        right: 15px;
        cursor: pointer;
        z-index: 30;
        width: 20px;
        height: 20px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='18' height='18' viewBox='0 0 18 18'%3E%3Cdefs%3E%3Cpath id='avzva' d='M691.485 160.07l-1.414 1.415-7.071-7.071-7.071 7.071-1.414-1.414 7.07-7.071-7.07-7.071 1.414-1.415 7.07 7.071 7.072-7.07 1.414 1.414-7.07 7.07z'/%3E%3C/defs%3E%3Cg%3E%3Cg transform='translate(-674 -144)'%3E%3Cuse fill='%232e2e2e' xlink:href='%23avzva'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") 0 0 no-repeat;
    }

    .policy_descr,
    .sending_form button,
    .sending_form input{
        margin: 0 0 20px 0;
    }


    .modal_h2 {
        font-size: 28px;
        font-weight: bold;
        line-height: 1;
        padding:  0 0 0.5em 0;
    }

    .policy_descr {
        font-size: 14px;
    }

    .loader {
        width:37px;
        height: 37px;
        margin-left: 10px;
        background-image: url("data:image/svg+xml,%3Csvg version='1.1' id='L2' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 100 100' enable-background='new 0 0 100 100' xml:space='preserve'%3E%3Ccircle fill='none' stroke='%23839F60' stroke-width='4' stroke-miterlimit='10' cx='50' cy='50' r='48'/%3E%3Cline fill='none' stroke-linecap='round' stroke='%232A7D5A' stroke-width='4' stroke-miterlimit='10' x1='50' y1='50' x2='85' y2='50.5'%3E%3CanimateTransform attributeName='transform' dur='2s' type='rotate' from='0 50 50' to='360 50 50' repeatCount='indefinite' /%3E%3C/line%3E%3Cline fill='none' stroke-linecap='round' stroke='%23839F60' stroke-width='4' stroke-miterlimit='10' x1='50' y1='50' x2='49.5' y2='74'%3E%3CanimateTransform attributeName='transform' dur='15s' type='rotate' from='0 50 50' to='360 50 50' repeatCount='indefinite' /%3E%3C/line%3E%3C/svg%3E");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .error_list_wrap {
        display: flex;
        flex-direction: column;
    }

    .error_list_wrap .error{
        color: white;
        background-color: crimson;
        border-radius: 9px;
        padding: 5px 15px;
        margin-bottom: 20px;
    }

    .qs_wrap {
        display: flex;
        max-height: 630px;
    }

    .qs_wrap .left img{
        object-fit: contain;
    }

    .qs_wrap .left{
        flex:2;
        border-radius: 15px;
        margin-right: 30px;
        overflow: hidden;
        border: 0.4px solid #d2d1d1;
    }

    .qs_wrap .right{
        flex:1;
    }

    .param_scroll_wrap {
        padding-right: 10px;
    }
    .param_scroll {
        max-height: 200px;
        overflow-y: scroll;
        padding-right: 10px;
    }

    .m_bottom {
        margin-bottom: 30px;
    }

    .ocbuy_tovar {
        display: flex;
    }

    .ocbuy_tovar .info p.sku_p{
        font-size: 12px;
    }

    .ocbuy_tovar .info p{
        margin: 5px 0;
    }

    .ocbuy_tovar .info strong{
        font-size: 18px;
        margin-bottom: 10px;
    }

    .ocbuy_tovar .info{
        display: flex;
        flex-direction: column;
        flex: 4;
    }
    .ocbuy_tovar .photo{
        width:80px;
        height:80px;
        margin-right: 20px;
        flex: 1;
    }

    .buy_form {
        margin-top: 20px;
        display: flex;
        flex-direction: column;
    }

    .buy_form input[type=tel]{
        margin-bottom: 20px;
    }

    .policy_des {
        margin-top: 20px;
    }

    .policy_des a:hover{
        text-decoration: underline;
    }
    .policy_des a{
        color:var(--main-color)
    }

    @media (max-width: 480px){
        .popup {
            padding: 30px;
        }

        .modal_h2 {
            font-size: 42px;
        }
    }

    @media (max-width: 320px){
        .popup {
            padding: 20px;
            min-width: 310px;
        }

        .modal_h2 {
            font-size: 38px;
        }

        .modal_h2 {
            margin-bottom: 0;
        }
    }

</style>
