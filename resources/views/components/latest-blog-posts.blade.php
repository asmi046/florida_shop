<section class="sales_hits_section">
    <div class="_wrapper">
        <h2>Полезные материалы</h2>

        <div class="blog_in_page">
            @foreach ($posts as $item)
                <x-blog-card :item="$item" />
            @endforeach
        </div>

        <div class="latest_blog_link">
            <a class="button" href="{{ route('blog') }}">Смотреть все</a>
        </div>
    </div>
</section>
