@props([
    'product' => null,
    'images' => [],
    'video' => null,
])

@php
    $videoUrl = $video ?? ($product['video_review'] ?? null);
    $hasVideo = !empty($videoUrl);
    $hasMainImg = !empty($product['img']);
    $hasExtra = !empty($images) && count($images) > 0;
    $showNoPhoto = !$hasVideo && !$hasMainImg && !$hasExtra;
    $videoPosterUrl = !empty($product['img'])
        ? asset($product['img'])
        : (!empty($images) && !empty($images[0]['link']) ? asset($images[0]['link']) : null);
@endphp

<div class="tovar_galery_splide">
    <section
        class="splide tovar_splide_main"
        aria-label="Галерея товара {{ $product['title'] ?? '' }}"
    >
        <div class="splide__track">
            <ul class="splide__list">
                @if ($hasVideo)
                    <li class="splide__slide">
                        <div class="tovar_splide_video">
                            <video
                                src="{{ asset($videoUrl) }}"
                                muted="muted"
                                autoplay="autoplay"
                                loop="loop"
                                playsinline
                                preload="metadata"
                                data-splide-video
                            ></video>
                            <span class="tovar_splide_video_label">Видеообзор</span>
                        </div>
                    </li>
                @endif

                @if ($hasMainImg)
                    <li class="splide__slide">
                        <img src="{{ asset($product['img']) }}" alt="{{ $product['title'] ?? '' }}">
                    </li>
                @endif

                @foreach ($images as $img)
                    <li class="splide__slide">
                        <img src="{{ asset($img['link']) }}" alt="{{ $img['alt'] ?? '' }}">
                    </li>
                @endforeach

                @if ($showNoPhoto)
                    <li class="splide__slide">
                        <img src="{{ asset('img/noPhoto.jpg') }}" alt="{{ $product['title'] ?? '' }}">
                    </li>
                @endif
            </ul>
        </div>

        <div class="splide__arrows">
            <button class="splide__arrow splide__arrow--prev" type="button" aria-label="Предыдущий слайд">
                <svg class="sprite_icon" aria-hidden="true"><use xlink:href="#arrow_green"></use></svg>
            </button>
            <button class="splide__arrow splide__arrow--next" type="button" aria-label="Следующий слайд">
                <svg class="sprite_icon" aria-hidden="true"><use xlink:href="#arrow_green"></use></svg>
            </button>
        </div>
    </section>

    <div class="tovar_splide_thumbs">
        <div
            class="splide tovar_splide_thumbs_slider"
            aria-label="Превью галереи"
        >
            <div class="splide__track">
                <ul class="splide__list">
                    @if ($hasVideo)
                        <li class="splide__slide">
                            <div class="tovar_splide_thumb_video">
                                @if ($videoPosterUrl)
                                    <img class="tovar_splide_thumb_video_poster" src="{{ $videoPosterUrl }}" alt="" loading="lazy">
                                @endif
                                <svg class="tovar_splide_thumb_video_play" viewBox="0 0 24 24" aria-hidden="true">
                                    <polygon points="9,7 9,17 17,12"></polygon>
                                </svg>
                            </div>
                        </li>
                    @endif

                    @if ($hasMainImg)
                        <li class="splide__slide">
                            <img src="{{ asset($product['img']) }}" alt="">
                        </li>
                    @endif

                    @foreach ($images as $img)
                        <li class="splide__slide">
                            <img src="{{ asset($img['link']) }}" alt="{{ $img['alt'] ?? '' }}">
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        function initToverSplide() {
            if (typeof window.Splide === 'undefined') return;

            document.querySelectorAll('.tovar_galery_splide').forEach(function (root) {
                var mainEl = root.querySelector('.tovar_splide_main');
                var thumbsEl = root.querySelector('.tovar_splide_thumbs_slider');
                if (!mainEl) return;

                var hasThumbs = !!thumbsEl;
                var main = new window.Splide(mainEl, {
                    type: 'loop',
                    perPage: 1,
                    arrows: true,
                    pagination: false,
                    speed: 400,
                    waitForTransition: true,
                });

                var thumbs = null;
                if (hasThumbs) {
                    thumbs = new window.Splide(thumbsEl, {
                        direction: 'ltr',
                        fixedWidth: 76,
                        perPage: 5,
                        gap: 8,
                        arrows: false,
                        pagination: false,
                        isNavigation: true,
                        breakpoints: {
                            768: { fixedWidth: 64, perPage: 4 },
                            480: { fixedWidth: 56, perPage: 3 },
                        },
                    });

                    // Сначала связываем, потом монтируем — без этого клик по превью
                    // переключает только превью, но не главный слайдер
                    main.sync(thumbs);
                }

                try {
                    main.mount();
                } catch (e) {
                    console.warn('[TOVAR-SPLIDE] main.mount()', e.message);
                }

                if (thumbs) {
                    try {
                        thumbs.mount();
                    } catch (e) {
                        console.warn('[TOVAR-SPLIDE] thumbs.mount()', e.message);
                    }
                }

                // Гарантированно выключаем звук у видео (страховка от перезаписи muted в JS)
                var videoSlides = mainEl.querySelectorAll('.splide__slide');
                videoSlides.forEach(function (slide) {
                    var v = slide.querySelector('video[data-splide-video]');
                    if (!v) return;
                    v.muted = true;
                    v.setAttribute('muted', '');
                    v.volume = 0;
                });

                // Автоплей/пауза встроенного <video> в зависимости от активного слайда
                var syncVideo = function () {
                    videoSlides.forEach(function (slide, idx) {
                        var v = slide.querySelector('video[data-splide-video]');
                        if (!v) return;
                        v.muted = true;
                        v.volume = 0;
                        if (idx === main.index) {
                            var p = v.play();
                            if (p && typeof p.catch === 'function') {
                                p.catch(function () {});
                            }
                        } else {
                            v.pause();
                        }
                    });
                };
                main.on('move', syncVideo);
                main.on('mounted', syncVideo);
            });
        }

        if (document.readyState === 'complete') {
            initToverSplide();
        } else {
            window.addEventListener('load', initToverSplide);
        }
    </script>
@endonce
