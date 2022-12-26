<section id="rew_section">
    <div class="_wrapper">

        <div class="rew_head">
            <h2>Отзывы покупателей</h2>
            <a class="btn btn_empty" href="">Оставить отзыв</a>
        </div>

        <div class="swiper_obj">
            <div class="swiper rew_slider">
                <div class="swiper-wrapper">
                    @for ($i=0; $i<10; $i++)
                        <div class="swiper-slide">
                                <div class="rew_blk">
                                    <div class="foto_wrapper">
                                        <img src="{{asset('img/facer_img/rew_f_1.jpg')}}" alt="">
                                    </div>

                                    <div class="text_wrapper">
                                        <h3>Ирина</h3>
                                        <p>Всегда все на высшем уровне. Удобный заказ. Свежие цветы. Огромный выбор. Очень необычные букеты и сочетания. Абсолютный восторг у получателей. </p>
                                        <a href="#">Читать отзыв в VK</a>
                                    </div>
                                </div>
                        </div>
                    @endfor

                </div>
            </div>

            <div class="banner_controll"></div>
        </div>
    </div>
</section>
