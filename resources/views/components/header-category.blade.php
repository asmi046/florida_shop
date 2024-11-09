<section class="head_category_new" id="head_category_new">
    <div class="_wrapper">
        <h2>Популярные категории</h2>
        <swiper-container init="false" id="main_cat_slider">
            @foreach ($all_cat as $item)
                <swiper-slide>
                    <x-cards.main-cat :item="$item"></x-cards.main-cat>
                </swiper-slide>
            @endforeach
        </swiper-container>

        <div class="slider_btn_wrapper">
            <button id="main_cat_slider_btn_prev" class="slider_btn slider_prev">
                <svg class="sprite_icon">
                    <use xlink:href="#arrow"></use>
                </svg>
            </button>

            <button id="main_cat_slider_btn_next" class="slider_btn slider_next">
                <svg class="sprite_icon">
                    <use xlink:href="#arrow"></use>
                </svg>
            </button>
        </div>
        {{-- <ul>
            <li><a class="action pi_after florida_procent" href="{{route('actions')}}">Акции</a></li>

            @for ($i=0; $i<count($all_cat); $i++)
                <li @class(['hidenet' => ($i >= 5)])><a href="{{route('category', $all_cat[$i]->slug)}}">{{$all_cat[$i]->title}}</a></li>
            @endfor

            <li class="more_wrapper">
                @if (count($all_cat) > 5)
                    <a class="more pi_after florida_arrow" href="">Еще</a>
                @endif

                <div class="ower_cat">
                        @for ($i=5; $i<count($all_cat); $i++)
                            <a href="{{route('category', $all_cat[$i]->slug)}}">{{$all_cat[$i]->title}}</a>
                        @endfor
                </div>
            </li>


        </ul> --}}
    </div>
</section>
