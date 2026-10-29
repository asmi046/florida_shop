<section id="catalog_section">
    <div class="_wrapper">
        <div class="sitebar">
            <h2>Каталог цветов</h2>

            <x-tovar-filter></x-tovar-filter>
        </div>
        <div class="tovars_blk">
            <div class="tovar_wrapper">

                @foreach ($allproduct as $tovar)
                    <x-cards.tovar-card :tovar="$tovar"></x-cards.tovar-card>
                @endforeach
            </div>
        </div>
    </div>
</section>
