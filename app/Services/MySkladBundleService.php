<?php

namespace App\Services;

use App\Models\MySkladStock;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MySkladBundleService
{
    /**
     * Обновляет данные продукта на основе состава из МойСклад.
     *
     * @param Product $product
     * @param bool $dryRun Если true — расчёт без записи в БД
     * @return array [skladCount, asc_nal, structure, skipped]
     */
    public function updateProductStock(Product $product, bool $dryRun = false): array
    {
        $assortiment = \App\Models\MySkladAssortiment::where('externalCode', $product->externalCode)->first();

        if (!$assortiment) {
            return $this->emptyResult('no_assortiment');
        }

        if (empty($assortiment->components_href)) {
            return $this->emptyResult('no_components');
        }

        $components = $this->fetchComponents($assortiment->components_href);

        if (empty($components)) {
            return $this->emptyResult('empty_components');
        }

        $structure = $this->buildStockStructure($components);

        if (empty($structure)) {
            return $this->emptyResult('empty_structure');
        }

        $canProduce = $this->calculateProducibleCount($structure);

        if (!$dryRun) {
            $product->skladCount = $canProduce;
            $product->asc_nal = $canProduce > 0;
            $product->save();

            Log::channel('my_sklad')->info('Обновлен продукт', [
                'product_id' => $product->id,
                'title' => $product->title,
                'externalCode' => $product->externalCode,
                'components' => $structure,
                'skladCount' => $canProduce,
                'asc_nal' => $canProduce > 0,
            ]);
        }

        return [
            'skladCount' => $canProduce,
            'asc_nal' => $canProduce > 0,
            'structure' => $structure,
            'skipped' => null,
        ];
    }

    /**
     * Возвращает пустой результат с причиной пропуска.
     *
     * @param string $reason
     * @return array
     */
    private function emptyResult(string $reason): array
    {
        return [
            'skladCount' => 0,
            'asc_nal' => false,
            'structure' => [],
            'skipped' => $reason,
        ];
    }

    /**
     * Получает состав комплекта по ссылке.
     *
     * @param string $href
     * @return array
     */
    private function fetchComponents(string $href): array
    {
        $login = config('mysklad.login');
        $password = config('mysklad.password');

        $response = Http::withBasicAuth($login, $password)
            ->withHeaders([
                'Accept' => 'application/json;charset=utf-8',
                'Accept-Encoding' => 'gzip',
            ])
            ->get($href);

        if (!$response->successful()) {
            Log::channel('my_sklad')->error('Ошибка при запросе состава комплекта', [
                'href' => $href,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();
        return $data['rows'] ?? [];
    }

    /**
     * Формирует структуру с id, quantity и stock.
     *
     * @param array $components
     * @return array
     */
    private function buildStockStructure(array $components): array
    {
        $ids = array_column(array_map(function ($item) {
            return [
                'id' => $item['assortment']['meta']['href'] ?? null,
            ];
        }, $components), 'id');

        $ids = array_filter($ids);
        $skladIds = array_map(fn($href) => $this->extractIdFromHref($href), $ids);

        $stocks = MySkladStock::whereIn('assortmentId', $skladIds)
            ->pluck('freeStock', 'assortmentId')
            ->toArray();

        $structure = [];
        foreach ($components as $component) {
            $href = $component['assortment']['meta']['href'] ?? null;
            $componentId = $this->extractIdFromHref($href);
            $quantity = $component['quantity'] ?? 0;

            $structure[] = [
                'id' => $componentId,
                'quantity' => (float) $quantity,
                'stock' => $stocks[$componentId] ?? 0,
            ];
        }

        return $structure;
    }

    /**
     * Извлекает UUID из URL.
     *
     * @param string|null $href
     * @return string|null
     */
    private function extractIdFromHref(?string $href): ?string
    {
        if (empty($href)) {
            return null;
        }

        preg_match('/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i', $href, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Вычисляет, сколько продуктов можно сформировать.
     *
     * @param array $structure
     * @return int
     */
    private function calculateProducibleCount(array $structure): int
    {
        if (empty($structure)) {
            return 0;
        }

        $possibleCounts = [];

        foreach ($structure as $item) {
            if ($item['quantity'] <= 0) {
                continue;
            }

            if ($item['stock'] <= 0) {
                return 0;
            }

            $possibleCounts[] = (int) floor($item['stock'] / $item['quantity']);
        }

        return empty($possibleCounts) ? 0 : min($possibleCounts);
    }
}