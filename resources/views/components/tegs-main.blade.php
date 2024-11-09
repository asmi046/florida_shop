<div class="tag_wrapper">
    @foreach ($all_tag as $item)
        <x-cards.tag :item="$item"></x-cards.tag>
    @endforeach
</div>
