<section id="catalog_section">
    <div class="_wrapper">
        <div class="sitebar">
            <h2>Каталог цветов</h2>

            <x-tovar-filter></x-tovar-filter>
        </div>
        <div class="tovars_blk">
            <div class="tovar_blk_wrap">

                @foreach ($allproduct as $tovar)
                    <x-tovar-card :isslide="false" :tovar="$tovar"></x-tovar-card>
                @endforeach
            </div>
        </div>
    </div>
</section>
