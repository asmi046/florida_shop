<section class="head_category_new" id="head_category_new">
    <div class="_wrapper">
        <h2>Популярные категории</h2>

        <div class="circle_category">
            @foreach ($all_cat as $item)
                <x-cards.circle-cat :item="$item"></x-cards.circle-cat>
            @endforeach
        </div>


        {{-- <swiper-container init="false" id="main_cat_slider">
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
        </div> --}}

    </div>
</section>
