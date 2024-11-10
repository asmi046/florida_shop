<section id="about_main">
    <div class="_wrapper">
            <div class="video_wraper">
                <div class="mobile">
                    <video autoplay muted loop width="250">
                        <source src="{{ asset('video/present_video.mp4') }}" type="video/mp4" />
                    </video>
                    <div class="mb"></div>
                    <div class="circl"></div>
                </div>
            </div>
            <div class="text_wrapper text_blk">
                <h2>{{$options['main_h1']}}</h2>

                {!!$options['main_text']!!}
            </div>
    </div>
</section>
