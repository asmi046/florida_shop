<div class="swiper_obj">
    <div class="swiper tovar_slider">
        <div class="swiper-wrapper">
            @if ($product['img'] == "")
                <div class="swiper-slide"><img src="{{asset('img/noPhoto.jpg')}}" alt="{{$product['title']}}"></div>
            @else
                <div class="swiper-slide"><img src="{{asset($product['img'])}}" alt="{{$product['title']}}"></div>
            @endif

            @foreach ($images as $img)
                <div class="swiper-slide"><img src="{{asset($img['link'])}}" alt="{{$img['alt']}}"></div>
            @endforeach

        </div>

        <div class="btn_all btn_right"></div>
        <div class="btn_all btn_left"></div>
    </div>
</div>

<div class="galery_thumbs">
    <div class="swiper_obj">
        <div class="swiper tovar_thumbs_slider">
            <div class="swiper-wrapper">

                @if ($product['img'] != "")
                <div class="swiper-slide"><img src="{{asset($product['img'])}}" alt="{{$product['title']}}"></div>
                @endif

                @if (count($images) != 0)

                    @foreach ($images as $img)
                        <div class="swiper-slide"><img src="{{asset($img['link'])}}" alt="{{$img['alt']}}"></div>
                    @endforeach

                @endif
            </div>
        </div>
    </div>

    <div class="galery_gift">
        <div class="gift_inner_blk gift_img">
            <img src="{{asset('img/gift.jpg')}}" alt="">
        </div>
        <div class="gift_inner_blk gift_text">
            <span>Добавить<br/> подарок</span>
        </div>
    </div>
</div>
