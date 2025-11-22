<section class="uni_breadcrumbs">
    <div class="_container">
        <div class="breadcrumbs">
            <a href="{{route('home')}}">Главная</a>
            {{-- @if ((Request::route()->named('category'))||(Request::route()->named('actions'))||(Request::route()->named('hits'))
            ||(Request::route()->named('new_tovar'))
            ||(Request::route()->named('celebration'))
            )
                <span class="sep"> / </span> <span class="finish">{{$category['title']}}</span>
            @endif --}}

            @if (Request::route())
                @if ((Request::route()->named('category'))||(Request::route()->named('celebration'))||(Request::route()->named('tag')))
                    <span class="sep"> / </span> <a href="{{route('catalog')}}">Букеты</a> <span class="sep"> / </span> <span class="finish">{!!$title!!}</span>
                @elseif (Request::route()->named('tovar'))
                    <span class="sep"> / </span> <a href="{{route('catalog')}}">Букеты</a> <span class="sep"> / </span> <span class="finish">{!!$title!!}</span>
                @elseif (Request::route()->named('blog_page'))
                    <span class="sep"> / </span> <a href="{{route('blog')}}">Блог</a> <span class="sep"> / </span> <span class="finish">{!! $title !!}</span>
                @elseif (isset($title))
                    <span class="sep"> / </span> <span class="finish">{{ $title }}</span>
                @endif
            @else
                <span class="sep"> / </span> <span class="finish">{{ $title }}</span>
            @endif

         </div>
    </div>
</section>
