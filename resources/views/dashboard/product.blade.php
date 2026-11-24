@extends('dashboard.layouts.app')

@section('title', 'Состав букета')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Состав букета</h1>
        <a href="{{ route('dashboard.index', ['token' => request('token')]) }}" class="btn btn-outline-secondary btn-sm">
            ← К списку товаров
        </a>
    </div>

    @if ($error)
        <div class="alert alert-warning">{{ $error }}</div>
    @else
        <div class="card mb-3">
            <div class="card-body">
                <h2 class="h5 mb-3">{{ $data['product']['title'] }}</h2>

                <div class="row g-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="text-muted small">Артикул (SKU)</div>
                        <div>{{ $data['product']['sku'] }}</div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="text-muted small">Цена</div>
                        <div>{{ number_format((float) $data['product']['price'], 2, ',', ' ') }} ₽</div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="text-muted small">Расчётное количество</div>
                        <div>{{ $data['product']['skladCount'] }}</div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="text-muted small">Данные на</div>
                        <div>{{ $data['generatedAt'] }}</div>
                    </div>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="text-muted small">Техкарта</div>
                        <div>
                            <a href="{{ $data['plan']['url'] }}" target="_blank" rel="noopener">
                                {{ $data['plan']['name'] }}
                            </a>
                            <span class="text-muted small ms-2">[{{ substr((string) $data['plan']['id'], 0, 8) }}]</span>
                        </div>
                    </div>

                    @if (! empty($data['plan']['processName']))
                        <div class="col-sm-6">
                            <div class="text-muted small">Техпроцесс</div>
                            <div>{{ $data['plan']['processName'] }}</div>
                        </div>
                    @endif

                    @if (! empty($data['result']))
                        <div class="col-sm-6">
                            <div class="text-muted small">Результат техкарты</div>
                            <div>
                                {{ $data['result']['name'] ?? '—' }}
                                × {{ rtrim(rtrim(number_format((float) $data['result']['quantity'], 3, ',', ' '), '0'), ',') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 40px;">№</th>
                        <th>Материал</th>
                        <th class="text-center">Нужно на букет</th>
                        <th class="text-center">Остаток сейчас</th>
                        <th class="text-center">Хватает на букетов</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['materials'] as $index => $material)
                        <tr class="{{ $material['status'] === 'хватает' ? '' : 'table-warning' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $material['name'] ?? substr((string) $material['id'], 0, 8).'…' }}
                            </td>
                            <td class="text-center">
                                {{ rtrim(rtrim(number_format((float) $material['quantity'], 3, ',', ' '), '0'), ',') }}
                            </td>
                            <td class="text-center">
                                {{ rtrim(rtrim(number_format((float) $material['stock'], 3, ',', ' '), '0'), ',') }}
                            </td>
                            <td class="text-center">
                                {{ (float) $material['quantity'] > 0 ? $material['possible'] : '—' }}
                            </td>
                            <td>
                                @if ($material['status'] === 'хватает')
                                    <span class="badge text-bg-success">хватает</span>
                                @elseif ($material['status'] === 'нет остатка')
                                    <span class="badge text-bg-danger">нет остатка</span>
                                @elseif ($material['status'] === 'нет данных об остатке')
                                    <span class="badge text-bg-secondary">нет данных об остатке</span>
                                @else
                                    <span class="badge text-bg-light text-dark">не влияет</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">В техкарте нет материалов</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($data['available'] > 0)
                    <div class="fs-5">
                        Можно собрать:
                        <span class="badge text-bg-success fs-6">{{ $data['available'] }}</span>
                        букет(ов)
                    </div>
                @else
                    <div class="fs-5 text-danger">
                        Можно собрать: 0 букетов
                    </div>
                @endif

                @if (! empty($data['blocking']))
                    <div class="text-muted mt-1">Ограничитель: {{ $data['blocking'] }}</div>
                @endif

                <div class="text-muted small mt-2">
                    Остатки получены из МойСклад в момент открытия страницы.
                    Расчётное количество в таблице дашборда: {{ $data['product']['skladCount'] }}.
                </div>
            </div>
        </div>
    @endif
@endsection
