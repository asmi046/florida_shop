<section id="uni_map">
    <div class="_wrapper">

        <div class="contacts">
            <h2>Как нас найти</h2>
            <p>Адрес салона</p>
            <h3>{{$options['adress_fk']}}</h3>

            <p>Адрес салона</p>
            <h3><a href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="phone">{{$options['phone']}}</a></h3>

            <a href="#floristConsult" class="button button_green">Помощь флориста</a>

            <p class="social">Соцсети и мессенджеры</p>
            <x-messanger></x-messanger>
        </div>

        <div id="render_map">

        </div>
    </div>

</section>
