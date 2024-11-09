

<a href="{{ route('category', $item->slug) }}" class="cat_blk">
    <div class="shadow"></div>
    <img src="{{ (!$item->img)?asset('img/cat_tmp.jpg'):$item->img }}" alt="{{ $item->title }}">
    <h3>{!! ($item->showed_title)?$item->showed_title:$item->title !!}</h3>
</a>
