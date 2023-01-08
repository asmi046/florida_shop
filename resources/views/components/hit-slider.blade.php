<section class="hit_sliders">
    <div class="_wrapper">
        <h2>Хиты продаж</h2>
        <div class="swiper_obj">
            <div class="swiper hit_slider">
                <div class="swiper-wrapper">
                    @foreach ( $salesliders as $tovar)
                        <x-tovar-card-hit :tovar="$tovar"></x-tovar-card-hit>
                    @endforeach

                </div>
            </div>

            <div id="hit_btn_left" class="btn_all btn_left"></div>
            <div id="hit_btn_right" class="btn_all btn_right"></div>
        </div>
    </div>
</section>
