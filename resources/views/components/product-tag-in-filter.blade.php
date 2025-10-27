<div class="acc_blk">
    <div class="acc_head open">
        <span>Цветы</span>
    </div>
    <div class="acc_body open">
        @foreach($tags as $tag)
            <a href="{{ route('tag', ['slug' => $tag->slug]) }}" @class(['tag_lnk', 'active' => request()->route('slug') == $tag->slug])>{{ $tag->title }}</a>
        @endforeach
    </div>
</div>
