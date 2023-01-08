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
                                <div class="rew_blk">
                                    <div class="foto_wrapper">
                                        <img src="{{$item['avatar']}}" alt="Отзвыв от покупателя: {{$item['name']}}">
                                    </div>

                                    <div class="text_wrapper">
                                        <h3>{{$item['name']}}</h3>
                                        <p>{{$item['text']}}</p>
                                        <a href="{{$item['lnk']}}">Читать отзыв в VK</a>
                                    </div>
                                </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="banner_controll"></div>
        </div>
    </div>
</section>
