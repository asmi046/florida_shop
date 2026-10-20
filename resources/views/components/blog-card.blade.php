@props(['item'])

<a href="{{ route('blog_page', $item->slug) }}" class="blog_blk">
    <div class="img_wrp">
        <img src="{{ $item->img }}" alt="">
    </div>
    <h2>{{ $item->title }}</h2>
    <p class="data">{{ $item->published_at->format('d.m.Y') }}</p>
    <p class="descr">{{ mb_strimwidth($item->short_description, 0, 90, '...') }}</p>
</a>
