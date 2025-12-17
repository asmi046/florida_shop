<footer>
    <div class="_wrapper">
        <div class="cols">
            <div class="col">
                <a class="logo" href="{{route('home')}}">
                    <img src="{{asset('img/logo_new_white.svg')}}" alt="Florida - Курск">
                </a>
            </div>

            <div class="col">
                <h4>Меню</h4>
                <x-main-menu></x-main-menu>
            </div>
            <div class="col">
                <h4>Категории</h4>

                <ul>
                    @foreach ($all_cat as $cat)
                        <li><a href="{{route('category', $cat->slug)}}">{{$cat->title}}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col">
                <h4>Особенности</h4>
                <ul>
                    <li><a href="{{route('hits')}}">Хит продаж</a></li>
                    <li><a href="{{route('new_tovar')}}">Новинка</a></li>
                    <li><a href="{{route('actions')}}">Скидки</a></li>
                </ul>
                <h4>Аккаунт</h4>

                <ul>
                    <li><a href="{{ route('bascet') }}">Корзина</a></li>
                    @auth('web')
                        <li><a href="{{route('cabinet.home')}}">Кабинет</a></li>
                    @endauth

                    @guest
                        <li><a href="{{route('login')}}">Войти</a></li>
                    @endguest
                </ul>

            </div>
            <div class="col">
                <h4>Контакты</h4>
                <a class="phone" href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="phone">{{$options['phone']}}</a>
                <p class="adress">{{$options['adress_fk']}}</p>

                <h4>Соцсети и мессенджеры</h4>
                <x-messanger></x-messanger>
            </div>
        </div>
        <div class="policy_line">
            <br/>
            <a href="{{route("policy")}}">Политика в области обработки конфиденциальной информации и персональных данных</a> и <a href="{{route("policy_accept")}}">Согласие на обработку персональных данных</a>
            <br/>
            <br/>
            <p>Индивидуальным предпринимателем Арепьев И.М. (Флорида)</p><br/>
            <p>Курская область, Курский район, д. Зорино, ул. Пески д. 13</p><br/>
            <a href="tel:{{str_replace(array('-', ' ', '(' , ')'), '', $options['phone'])}}" class="phone">{{$options['phone']}}</a><br/>
        </div>
    </div>

</footer>
