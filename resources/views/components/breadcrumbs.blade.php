<section class="uni_breadcrumbs">
    <div class="_container">
        <div class="breadcrumbs">
            <a href="{{route('home')}}">Главная</a>
            @if (Request::route()->named('category'))
                <span class="sep"> / </span> <span class="finish">{{$category['title']}}</span>
            @endif

            @if (Request::route()->named('tovar'))
                {{-- <span class="sep"> / </span> <a href="{{route('category', $tovar['tovar_category']->slug)}}">{{$tovar['tovar_category']->title}}</a> --}}
                <span class="sep"> / </span> <span class="finish">{{$tovar['title']}}</span>
            @endif

            @if (isset($title))
                <span class="sep"> / </span> <span class="finish">{{ $title }}</span>
            @endif

            @if (isset($blog))
            <span class="sep"> / </span> <a href="{{route('blog')}}">Блог</a> <span class="sep"> / </span> <span class="finish">{{ $blog }}</span>
            @endif
         </div>
    </div>
</section>
