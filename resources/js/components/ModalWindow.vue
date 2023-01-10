<template>
    <div v-show="showModal" @click.self="closeWin()" class="popup_wrapper">
        <div class="popup">
            <div @click.prevent="closeWin()" class="popup__close" aria-label="Закрыть модальное окно"></div>
            <h2 class="modal_h2">{{title}}</h2>
            <p>{{subtitle}}</p>
            <form class="sending_form" action="/send_consult" method="POST">
                <input type="hidden" name="_token" :value="_token">
                <input type="text" name="name" placeholder="Имя">
                <input type="text" name="phone" placeholder="Телефон*">
                <p class="policy_descr">Заполняя данную форму и отправляя заявку вы соглашаетесь с <a href="#">политикой конфиденциальности</a></p>
                <button class="btn">Отправить</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showModal:false,
            _token: document.querySelector('meta[name="_token"]').content,
        }
    },

    props: ['rout', 'hesh', 'title', 'subtitle'],

    methods:{
        closeWin() {
            this.showModal = false
            history.pushState('', document.title, window.location.pathname+window.location.search)
        },

        openWin() {
            if (location.hash === '#'+this.hesh) {
                this.showModal = true
            }
        }
    },

    updated() {
        console.log('Совершён переход по ссылке')
    },

    mounted() {
        window.addEventListener('hashchange', this.openWin)

        if (location.hash === '#'+this.hesh) {
            this.showModal = true
        }
    }
}
</script>

<style>
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
        height: 400px;
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
    .sending_form input{
        margin: 0 0 20px 0;
    }

    .policy_descr a{
        text-decoration: underline;
    }

    .sending_form {
       display: flex;
       flex-direction: column;
    }

    .modal_h2 {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 40px;
    }



</style>
