        <div class="head_search {{ $class ?? '' }}">
            <form action=" {{ route('show_search_page') }} " method="GET" class="search_form">
                <input type="text" name="search" value="{{ old('search') ?? request('search') }}" placeholder="Поиск по сайту...">
                <button type="submit">
                    <svg class="sprite_icon">
                        <use xlink:href="#search"></use>
                    </svg>
                </button>
            </form>
        </div>
