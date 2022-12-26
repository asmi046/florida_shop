<section id="sales_action">
    <div class="_wrapper">
        <h2>Скидки и акции</h2>
            <div class="swiper_obj">
                <div class="swiper sales_slider">
                    <div class="swiper-wrapper">
                        @for ($i=0; $i<10; $i++)
                            <div class="swiper-slide">
                                <x-tovar-card></x-tovar-card>
                            </div>
                        @endfor

                    </div>
                </div>

                <div id="sale_btn_left" class="btn_all btn_left"></div>
                <div id="sale_btn_right" class="btn_all btn_right"></div>
            </div>
    </div>
</section>
