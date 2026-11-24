<?php

namespace App\Services;

use App\Models\MySkladAssortiment;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class MySkladTechPlanAvailabilityService
{
    /**
     * Время кэширования структуры техкарты, секунды.
     */
    public const CACHE_TTL = 3600;

    public function __construct(
        private readonly MySkladProcessingPlanService $planService,
        private readonly MySkladTechPlanService $techPlanService
    ) {}

    /**
     * Собрать детализацию по составу букета: что есть на складе и сколько букетов можно собрать.
     *
     * Структура техкарты кэшируется на час, остатки запрашиваются в реальном времени.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    public function build(Product $product, bool $useCache = true): array
    {
        $planId = $product->processingPlanId;

        if (empty($planId)) {
            throw new RuntimeException('У товара не заполнен processingPlanId — техкарта не привязана.');
        }

        $planData = $this->fetchPlanData((string) $planId, $useCache);

        $stocks = $this->planService->fetchLiveStocks();
        $calculation = $this->techPlanService->calculate($planData['materials'], $stocks);

        $materials = [];

        foreach ($calculation['structure'] as $material) {
            $materials[] = [
                'id' => $material['id'],
                'name' => $material['name'],
                'quantity' => (float) $material['quantity'],
                'stock' => (int) $material['stock'],
                'possible' => (int) $material['possible'],
                'hasStockRow' => (bool) $material['hasStockRow'],
                'status' => $this->materialStatus($material),
            ];
        }

        return [
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'sku' => $product->sku,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'skladCount' => (int) $product->skladCount,
                'asc_nal' => $product->asc_nal,
                'processingPlanId' => $planId,
            ],
            'plan' => $planData['plan'],
            'result' => $planData['result'],
            'materials' => $materials,
            'available' => (int) $calculation['count'],
            'blocking' => $this->techPlanService->reasonLabel($calculation['blocking']),
            'generatedAt' => now()->toDateTimeString(),
        ];
    }

    /**
     * Получить структуру техкарты: сама техкарта, материалы и результат.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    private function fetchPlanData(string $planId, bool $useCache = true): array
    {
        if (! $useCache) {
            return $this->requestPlanData($planId);
        }

        return Cache::remember(
            'moysklad:techplan:'.$planId,
            self::CACHE_TTL,
            fn () => $this->requestPlanData($planId)
        );
    }

    /**
     * Запросить данные техкарты у МойСклад.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    private function requestPlanData(string $planId): array
    {
        $plan = $this->planService->fetchPlan($planId);
        $materialsRaw = $this->planService->fetchPlanMaterials($planId);
        $results = $this->planService->fetchPlanProducts($planId);
        $processes = $this->planService->fetchProcesses();
        $assortment = $this->planService->fetchAssortmentIndex();

        $processId = $plan['processingProcess']['meta']['href'] ?? null;

        return [
            'plan' => [
                'id' => $planId,
                'name' => $plan['name'] ?? '',
                'processName' => $processId !== null ? ($processes[basename($processId)] ?? null) : null,
                'url' => 'https://online.moysklad.ru/app/#processingplan/edit?id='.$planId,
                'updated' => $plan['updated'] ?? null,
            ],
            'materials' => $this->normalizeMaterials($materialsRaw, $assortment),
            'result' => $this->normalizeResult($results, $assortment),
        ];
    }

    /**
     * Привести материалы к виду: id / name / quantity.
     *
     * Название берётся из ассортимента API, при отсутствии — из локальной таблицы.
     *
     * @param  array<int, array<string, mixed>>  $raw
     * @param  array<string, array<string, mixed>>  $assortment
     * @return array<int, array<string, mixed>>
     */
    private function normalizeMaterials(array $raw, array $assortment): array
    {
        $ids = [];

        foreach ($raw as $item) {
            $href = $item['assortment']['meta']['href'] ?? ($item['product']['meta']['href'] ?? null);
            $id = basename((string) $href);

            if ($id !== '') {
                $ids[] = $id;
            }
        }

        $local = empty($ids)
            ? []
            : MySkladAssortiment::query()
                ->whereIn('sklad_id', $ids)
                ->pluck('name', 'sklad_id')
                ->all();

        $materials = [];

        foreach ($raw as $item) {
            $href = $item['assortment']['meta']['href'] ?? ($item['product']['meta']['href'] ?? null);
            $id = basename((string) $href);

            $materials[] = [
                'id' => $id,
                'name' => $assortment[$id]['name'] ?? ($local[$id] ?? null),
                'quantity' => (float) ($item['quantity'] ?? 0),
            ];
        }

        return $materials;
    }

    /**
     * Привести результат техкарты к виду: name / quantity.
     *
     * @param  array<int, array<string, mixed>>  $results
     * @param  array<string, array<string, mixed>>  $assortment
     * @return array<string, mixed>|null
     */
    private function normalizeResult(array $results, array $assortment): ?array
    {
        $result = $results[0] ?? null;

        if ($result === null) {
            return null;
        }

        $href = $result['assortment']['meta']['href'] ?? ($result['product']['meta']['href'] ?? null);
        $id = basename((string) $href);

        return [
            'id' => $id,
            'name' => $assortment[$id]['name'] ?? null,
            'quantity' => (float) ($result['quantity'] ?? 0),
        ];
    }

    /**
     * Статус позиции по остатку.
     *
     * @param  array<string, mixed>  $material
     */
    private function materialStatus(array $material): string
    {
        if (! $material['hasStockRow']) {
            return 'нет данных об остатке';
        }

        if ((int) $material['stock'] <= 0) {
            return 'нет остатка';
        }

        if ((float) $material['quantity'] <= 0) {
            return 'не влияет';
        }

        return 'хватает';
    }
}
