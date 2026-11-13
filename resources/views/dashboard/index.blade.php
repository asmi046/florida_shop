@extends('dashboard.layouts.app')

@section('title', 'Складской дашборд')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Товары: складской учёт</h1>
        <span class="text-muted">Всего: {{ $products->count() }}</span>
    </div>

    <form method="GET" action="{{ route('dashboard.index') }}" class="row g-2 mb-3">
        <input type="hidden" name="token" value="{{ request('token') }}">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ $search }}" class="form-control"
                   placeholder="Поиск по наименованию...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Найти</button>
            @if ($search !== '')
                <a href="{{ route('dashboard.index', ['token' => request('token')]) }}" class="btn btn-outline-secondary">Сбросить</a>
            @endif
        </div>
    </form>

    @php
        $toggle = fn (string $column) => [
            'token' => request('token'),
            'search' => $search ?: null,
            'sort' => $column,
            'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc',
        ];
        $arrow = fn (string $column) => $sort === $column ? ($direction === 'asc' ? ' ▲' : ' ▼') : '';
    @endphp

    <div class="table-responsive">
        <table class="table table-striped table-hover table-sm align-middle">
            <thead class="table-dark">
                <tr>
                    <th><a class="link-light text-decoration-none" href="{{ route('dashboard.index', $toggle('title')) }}">Наименование{!! $arrow('title') !!}</a></th>
                    <th class="text-center"><a class="link-light text-decoration-none" href="{{ route('dashboard.index', $toggle('skladCount')) }}">Расчётное количество{!! $arrow('skladCount') !!}</a></th>
                    <th>ID техкарты</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->title }}</td>
                        <td class="text-center">{{ $product->skladCount }}</td>
                        <td>
                            @if ($product->processingPlanId)
                                <a href="https://online.moysklad.ru/app/#processingplan/edit?id={{ $product->processingPlanId }}"
                                   target="_blank" rel="noopener">{{ $product->processingPlanId }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Ничего не найдено</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
