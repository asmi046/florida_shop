<section class="head_category_new" id="head_category_new">
    <div class="_wrapper">
        <h2>Популярные категории</h2>

        <div class="circle_category">
            @foreach ($categories as $item)
                <x-cards.circle-cat :item="$item"></x-cards.circle-cat>
            @endforeach
        </div>
    </div>
</section>
