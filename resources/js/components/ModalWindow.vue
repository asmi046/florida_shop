<template>
    <div v-show="showModal" @click.self="closeWin()" class="popup_wrapper">
        <div class="popup">
            <div @click.prevent="closeWin()" class="popup__close" aria-label="Закрыть модальное окно"></div>
            <h2 class="modal_h2">{{title}}</h2>
            <p class="sub_h">{{subtitle}}</p>
            <form class="sending_form" action="/send_consult" method="POST">
                <input type="hidden" name="_token" :value="_token">
                <input type="text" name="name" v-model="name" placeholder="Имя">
                <input type="text" v-mask="{mask: '+7 (NNN) NNN-NN-NN', model: 'cpf' }" name="phone" v-model="phone" placeholder="Телефон*">
                <div class="error_list_wrap">
                    <div v-for="(item, index) in errorList" :key="index" class="error">{{item}}</div>
                </div>


                <textarea v-if="showarea == 'true'" v-model="review" name="rewiew" placeholder="Ваш отзыв" rows="5"></textarea>

                <p class="policy_descr">Заполняя данную форму и отправляя заявку вы соглашаетесь с <a href="#">политикой конфиденциальности</a></p>
                <div class="control_wrap">
                    <button class="btn" @click.prevent="sendMsg()">Отправить</button> <div v-show="showLoader" class="loader"></div>
                </div>

            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            name:"",
            phone:"",
            review:"",
            showModal:false,
            showLoader:false,
            errorList:[],
            _token: document.querySelector('meta[name="_token"]').content,
        }
    },

    props: ['rout', 'redirect', 'hesh', 'title', 'subtitle', 'showarea'],

    methods:{
        closeWin() {
            this.showModal = false
            history.pushState('', document.title, window.location.pathname+window.location.search)
        },

        openWin() {
            if (location.hash === '#'+this.hesh) {
                this.showModal = true
            }
        },

        sendMsg() {

            this.errorList = [];this.errorList

            if (this.phone == "")
                this.errorList.push("Поле 'Телефон' не заполнено");

            if (this.errorList.length != 0 ) return

            if (this.hesh == "showModalNal") this.review = "Запрос наличия букета. "+window.location;

            this.showLoader = true;
            axios.post(this.rout, {
                _token: this._token,
                name: this.name,
                phone: this.phone,
                review: this.review,

            })
            .then((response) => {
                this.showLoader = false
                console.log(response)
                document.location.href=this.redirect
            })
            .catch( (error) => {
                this.showLoader = false
                this.errorList.push(error.response.data.message)
                console.log(error)
            });
        }
    },

    mounted() {
        window.addEventListener('hashchange', this.openWin)

        if (location.hash === '#'+this.hesh) {
            this.showModal = true
        }
    }
}
</script>

<style scoped>
    .popup_wrapper {
        width:100%;
        height: 100%;
        display: flex;
        position: fixed;
        left: 0;
        top:0;
        z-index: 11000;
        background-color: #000000aa;
    }

    .popup {
        width:50%;
        max-width: 600px;
        min-width: 320px;
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
        background: url("../../../public/img/icons/shop_icon/close.svg") 0 0 no-repeat;
    }

    .policy_descr,
    .sending_form button,

    .sending_form textarea,
    .sending_form input{
        margin: 0 0 20px 0;
    }

    .policy_descr a{
        text-decoration: underline;
    }

    .sending_form .control_wrap button{
        width:50%;
    }

    .sending_form .control_wrap {
        max-width: 100%;
        display: flex;
    }

    .sending_form {
       display: flex;
       flex-direction: column;
    }

    .sub_h {
        margin-bottom: 20px;
    }

    .modal_h2 {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .policy_descr {
        font-size: 14px;
    }

    .loader {
        width:37px;
        height: 37px;
        margin-left: 10px;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQogIHZpZXdCb3g9IjAgMCAxMDAgMTAwIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAxMDAgMTAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxjaXJjbGUgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZmZmIiBzdHJva2Utd2lkdGg9IjQiIHN0cm9rZS1taXRlcmxpbWl0PSIxMCIgY3g9IjUwIiBjeT0iNTAiIHI9IjQ4Ii8+DQo8bGluZSBmaWxsPSJub25lIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSI0IiBzdHJva2UtbWl0ZXJsaW1pdD0iMTAiIHgxPSI1MCIgeTE9IjUwIiB4Mj0iODUiIHkyPSI1MC41Ij4NCiAgPGFuaW1hdGVUcmFuc2Zvcm0NCiAgICAgICBhdHRyaWJ1dGVOYW1lPSJ0cmFuc2Zvcm0iDQogICAgICAgZHVyPSIycyINCiAgICAgICB0eXBlPSJyb3RhdGUiDQogICAgICAgZnJvbT0iMCA1MCA1MCINCiAgICAgICB0bz0iMzYwIDUwIDUwIg0KICAgICAgIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiAvPg0KPC9saW5lPg0KPGxpbmUgZmlsbD0ibm9uZSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2U9IiNmZmYiIHN0cm9rZS13aWR0aD0iNCIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiB4MT0iNTAiIHkxPSI1MCIgeDI9IjQ5LjUiIHkyPSI3NCI+DQogIDxhbmltYXRlVHJhbnNmb3JtDQogICAgICAgYXR0cmlidXRlTmFtZT0idHJhbnNmb3JtIg0KICAgICAgIGR1cj0iMTVzIg0KICAgICAgIHR5cGU9InJvdGF0ZSINCiAgICAgICBmcm9tPSIwIDUwIDUwIg0KICAgICAgIHRvPSIzNjAgNTAgNTAiDQogICAgICAgcmVwZWF0Q291bnQ9ImluZGVmaW5pdGUiIC8+DQo8L2xpbmU+DQo8L3N2Zz4NCg==");
        background-size: 60%;
        background-position: center;
        background-repeat: no-repeat;
        background-color: var(--main-color);
        border-radius: 40px;

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
