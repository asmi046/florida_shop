<div class="margin_top_bottom tovar_wrapper" id="product-list">
    @foreach ($allproduct as $tovar)
        <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
    @endforeach
</div>

<section class="category" id="product-pagination">
    <div class="_wrapper">
        <x-pagination :tovars="$allproduct"></x-pagination>
    </div>
</section>
