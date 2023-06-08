<section id="head_category">
    <div class="_wrapper">
        <ul>
            <li><a class="action pi_after florida_procent" href="">Акции</a></li>

            @for ($i=0; $i<count($all_cat); $i++)
                <li @class(['hidenet' => ($i >= 5)])><a href="{{route('category', $all_cat[$i]->slug)}}">{{$all_cat[$i]->title}}</a></li>
            @endfor

            <li class="more_wrapper">
                <a class="more pi_after florida_arrow" href="">Еще</a>

                <div class="ower_cat">
                        @for ($i=5; $i<count($all_cat); $i++)
                            <a href="{{route('category', $all_cat[$i]->slug)}}">{{$all_cat[$i]->title}}</a>
                        @endfor
                </div>
            </li>


        </ul>
    </div>
</section>
