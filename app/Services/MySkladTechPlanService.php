<?php

namespace App\Services;

use App\Models\MySkladStock;
use App\Models\Product;
use Illuminate\Support\Collection;

class MySkladTechPlanService
{
    /**
     * Товары магазина, у которых проставлена привязка к технике.
     *
     * @return Collection<int, Product>
     */
    public function findProductsByPlanId(string $planId): Collection
    {
        return Product::query()
            ->where('processingPlanId', $planId)
            ->get(['id', 'title', 'code', 'externalCode', 'skladCount', 'asc_nal', 'processingPlanId']);
    }

    /**
     * Получить остатки по всем позициям номенклатуры: assortmentId => freeStock.
     *
     * @return array<string, int>
     */
    public function fetchStocks(): array
    {
        return MySkladStock::query()
            ->pluck('freeStock', 'assortmentId')
            ->map(fn ($value) => (int) $value)
            ->all();
    }

    /**
     * Рассчитать, сколько единиц можно собрать из материалов техкарты.
     *
     * @param  array<int, array<string, mixed>>  $materials  id / name / quantity
     * @param  array<string, int>  $stocks  assortmentId => freeStock
     * @return array<string, mixed>
     */
    public function calculate(array $materials, array $stocks): array
    {
        $structure = [];
        $counts = [];
        $blocking = null;

        foreach ($materials as $material) {
            $id = $material['id'] ?? null;
            $quantity = (float) ($material['quantity'] ?? 0);
            $hasStockRow = $id !== null && array_key_exists($id, $stocks);
            $stock = $hasStockRow ? (int) $stocks[$id] : 0;

            $structure[] = [
                'id' => $id,
                'name' => $material['name'] ?? null,
                'quantity' => $quantity,
                'stock' => $stock,
                'hasStockRow' => $hasStockRow,
            ];

            if ($quantity <= 0) {
                continue;
            }

            if ($stock <= 0) {
                if ($blocking === null) {
                    $blocking = [
                        'reason' => $hasStockRow ? 'no_stock' : 'no_stock_data',
                        'material' => $material['name'] ?? $id,
                        'needed' => $quantity,
                    ];
                }

                continue;
            }

            $counts[] = (int) floor($stock / $quantity);
        }

        if (empty($materials)) {
            $blocking = ['reason' => 'empty_materials', 'material' => null, 'needed' => 0];
        } elseif (empty($counts) && $blocking === null) {
            $blocking = ['reason' => 'no_quantities', 'material' => null, 'needed' => 0];
        }

        $count = $blocking !== null ? 0 : min($counts);

        return [
            'count' => $count,
            'structure' => $structure,
            'blocking' => $blocking,
        ];
    }

    /**
     * Применить результат расчёта к товару.
     *
     * В dry-run режиме база не изменяется.
     */
    public function apply(Product $product, int $count, bool $dryRun): void
    {
        if ($dryRun) {
            return;
        }

        $product->skladCount = $count;
        $product->asc_nal = $count <= 0;
        $product->save();
    }

    /**
     * Текстовое описание причины, по которой нельзя собрать продукт.
     *
     * @param  array<string, mixed>|null  $blocking
     */
    public function reasonLabel(?array $blocking): string
    {
        if ($blocking === null) {
            return '';
        }

        $material = $blocking['material'] ?? null;
        $needed = (float) ($blocking['needed'] ?? 0);

        return match ($blocking['reason'] ?? '') {
            'no_stock' => 'Нет остатка: '.$this->materialLabel($material, $needed),
            'no_stock_data' => 'Нет данных об остатке: '.$this->materialLabel($material, $needed),
            'empty_materials' => 'В техкарте нет материалов',
            'no_quantities' => 'У материалов не задано количество',
            default => (string) ($blocking['reason'] ?? 'Недостаточно остатков'),
        };
    }

    /**
     * Подпись материала для отчёта.
     */
    private function materialLabel(?string $material, float $needed): string
    {
        if ($material === null) {
            return '—';
        }

        $quantity = $needed == (int) $needed
            ? (string) (int) $needed
            : rtrim(rtrim(number_format($needed, 3, ',', ' '), '0'), ',');

        return $this->shortName($material).' (нужно '.$quantity.')';
    }

    /**
     * Короткое имя позиции: название, если известно, иначе первые символы UUID.
     */
    private function shortName(string $material): string
    {
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-/i', $material)) {
            return substr($material, 0, 8).'…';
        }

        return $material;
    }
}
