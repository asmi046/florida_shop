<div class="cabinet_control_panel">
    <a @class([
        'cabinet_user_datd_btn',
        'active' => Request::route()->named('cabinet.home')
        ])
        href="{{route('cabinet.home')}}">Личные данные</a>

    <a @class([
        'cabinet_orders_btn',
        'active' => Request::route()->named('cabinet.orders')
        ])
        href="{{route('cabinet.orders')}}">Мои заказы</a>

    <a @class([
        'cabinet_bonuses_btn',
        'active' => Request::route()->named('cabinet.bonuses')
        ])
        href="{{route('cabinet.bonuses')}}">Бонусная система</a>

    <a class="cabinet_exit_btn" href="{{route('logout')}}">Выйти из кабинета</a>
</div>
