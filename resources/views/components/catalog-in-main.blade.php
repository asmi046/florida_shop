<section id="catalog_section">
    <div class="_wrapper">
        <div class="sitebar">
            <h2>Каталог цветов</h2>

            <x-tovar-filter></x-tovar-filter>
        </div>
        <div class="tovars_blk">
            <div class="tovar_blk_wrap">
                @for ($i=0; $i<9; $i++)
                    <x-tovar-card></x-tovar-card>
                @endfor
            </div>
        </div>
    </div>
</section>
