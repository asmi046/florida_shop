<section id="about_main">
    <div class="_wrapper">
            <div class="img_wraper">
                <img src="{{asset('img/about-picture.jpg')}}" alt="">
            </div>
            <div class="text_wrapper text_blk">
                <h2>{{$options['main_h1']}}</h2>

                {!!$options['main_text']!!}

                <a class="btn read_more btn_icon_after" href="{{route('about')}}">Читать далее</a>
            </div>
    </div>
</section>
