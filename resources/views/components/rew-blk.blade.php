<div class="rew_blk">
    @if (empty($item['avatar']))
        <div class="foto_text_wrapper">
            <span>{{mb_substr(mb_strtoupper($item['name']), 0, 1)}}</span>
        </div>
    @else

        <div class="foto_wrapper">
            <img src="{{$item['avatar']}}" alt="Отзвыв от покупателя: {{$item['name']}}">
        </div>

    @endif


    <div class="text_wrapper">
        <h3>{{$item['name']}}</h3>
        <p>{{$item['text']}}</p>
        <a target="_blank" href="{{$item['lnk']}}">Читать далее...</a>
    </div>
</div>
