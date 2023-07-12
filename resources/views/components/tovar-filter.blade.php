<div class="filter_wrapper">

    <div class="filter_mobile_panel">
        <span class="filter_icon"></span> Фильтры
    </div>

    <form action="{{url()->current()}}" method="GET" class="tovar_filter accordion">
        <input type="hidden" name="order" value="{{ value_check('order','',"Сначала дешевые") }}">
        <input type="hidden" name="brand" value="{{ value_check('brand','','%') }}">
        <div class="acc_blk ">
            <div class="acc_head open">
                <span>Стоимость</span>
            </div>
            <div class="acc_body open">
                <div class="price_selector_wrapper">
                    <label class="price_start" for="price_start">
                        <span>От</span>
                        <input name="minprice" value="{{ value_check('minprice', '', 5)}}" type="text" id="price_start">
                    </label>

                    <label class="price_end" for="price_end">
                        <span>До</span>
                        <input type="text" name="maxprice" value="{{ value_check('maxprice', '', 10000) }}"  id="price_end">
                    </label>
                </div>
            </div>
        </div>

        <div class="acc_blk ">
            <div class="acc_head open">
                <span>Особенности</span>
            </div>
            <div class="acc_body open">
                <label class="ch_label">
                    <input type="checkbox" value="Новинки" {{ value_check('osobennosti', 'Новинки')?"checked":""}} name="osobennosti[]">
                    <span>Новинки</span>
                </label>
                <label class="ch_label">
                    <input type="checkbox" value="Хит продаж" {{ value_check('osobennosti', 'Хит продаж')?"checked":""}}   name="osobennosti[]">
                    <span>Хит продаж</span>
                </label>
                <label class="ch_label">
                    <input type="checkbox" value="Скидка" {{ value_check('osobennosti', 'Скидка')?"checked":""}} name="osobennosti[]">
                    <span>Скидка</span>
                </label>
            </div>
        </div>


        <div class="filter_control_blk">
            <button name="filter" type="submit" class="btn ">Применить</button>
            <a href="{{route('catalog')}}" class="btn btn_empty">Сбросить фильтр</a>
        </div>

    </form>
</div>
