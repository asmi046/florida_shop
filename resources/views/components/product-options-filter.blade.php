@php
    $selected = array_map('intval', (array) request()->query($dimension, []));
@endphp

<div class="acc_blk">
    <div class="acc_head open">
        <span>{{ $title }}</span>
    </div>
    <div class="acc_body open">
        @forelse ($values as $value)
            @php
                $swatch = $value->meta['gradient'] ?? ($value->meta['hex'] ?? null);
            @endphp
            <label class="ch_label">
                <input type="checkbox" name="{{ $dimension }}[]" value="{{ $value->id }}"
                    @checked(in_array((int) $value->id, $selected, true))>
                <span>@if ($swatch)<i class="color_swatch" style="background: {{ $swatch }}"></i>@endif{{ $value->title }}</span>
            </label>
        @empty
            <p class="filter_empty">Нет значений</p>
        @endforelse
    </div>
</div>
