<section class="tovar_page_photo_section">
    <div class="_container">
        <div class="tovar_galery">
            <x-tovar-page-content.tovar-galery :images="$images" :product="$product"></x-tovar-page-content.tovar-galery>
        </div>

        <div class="tovar_info_side">
            <x-tovar-page-content.tovar-info :product="$product"></x-tovar-page-content.tovar-info>
        </div>
    </div>
</section>

<section class="tovar_description">
    <div class="_container">
        <x-tovar-page-content.tovar-description :product="$product"></x-tovar-page-content.tovar-description>
    </div>
</section>
