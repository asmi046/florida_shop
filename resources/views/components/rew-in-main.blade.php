<section id="rew_section">
    <div class="_wrapper">

        <div class="rew_head">
            <h2>Отзывы покупателей</h2>
            <a class="btn btn_empty" href="">Оставить отзыв</a>
        </div>

        <div class="swiper_obj">
            <div class="swiper rew_slider">
                <div class="swiper-wrapper">
                    @foreach ($reviews as $item)
                        <div class="swiper-slide">
                                <x-rew-blk :item="$item"></x-rew-blk>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="banner_controll"></div>
        </div>
    </div>
</section>
