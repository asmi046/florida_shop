<a href="{{ route('category', $item->slug) }}" class="circle_category_item">
    <div class="circle">
        <img src="{{ (!$item->img)?asset('img/cat_tmp.jpg'):$item->img }}" alt="{{ $item->title }}">
    </div>
    <p>
        {!! ($item->showed_title)?$item->showed_title:$item->title !!}
    </p>
</a>
