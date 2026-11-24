<?php

namespace App\Console\Commands;

use App\Models\MySkladAssortiment;
use App\Models\Product;
use App\Services\MySkladProcessingPlanService;
use App\Services\MySkladTechPlanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShowMoySkladTechPlanAvailability extends Command
{
    protected $signature = 'moysklad:techplan-availability
                            {product : SKU или slug товара на сайте}
                            {--throttle=1100 : Пауза между запросами к API, мс (лимит МойСклад — 1 req/s)}';

    protected $description = 'Детализация по составу букета: сколько материала доступно на складе и сколько букетов можно собрать';

    const SESSION_TYPE = 'techplan_availability';

    public function handle(MySkladProcessingPlanService $planService, MySkladTechPlanService $techPlanService): int
    {
        $login = config('mysklad.login');
        $password = config('mysklad.password');

        if (empty($login) || empty($password)) {
            $this->error('Логин или пароль МойСклад не настроены. Проверьте файл .env');

            return 1;
        }

        $identifier = (string) $this->argument('product');

        $product = Product::query()
            ->where('sku', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        if ($product === null) {
            $this->error("Товар с SKU или slug «{$identifier}» не найден.");

            Log::channel('my_sklad')->error('Товар не найден', [
                'session_type' => self::SESSION_TYPE,
                'identifier' => $identifier,
            ]);

            return 1;
        }

        $planId = $product->processingPlanId;

        if (empty($planId)) {
            $this->error("У товара «{$product->title}» не заполнен processingPlanId — техкарта не привязана.");
            $this->error('Заполните поле в админке или выполните: php artisan moysklad:processing-plans --sync');

            return 1;
        }

        $planService->setThrottleMs((int) $this->option('throttle'));

        $startTime = now();

        Log::channel('my_sklad')->info('Начало детализации по составу техкарты', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'product_id' => $product->id,
            'processingPlanId' => $planId,
        ]);

        try {
            $plan = $planService->fetchPlan($planId);
            $materialsRaw = $planService->fetchPlanMaterials($planId);
            $results = $planService->fetchPlanProducts($planId);
            $processes = $planService->fetchProcesses();
            $assortment = $planService->fetchAssortmentIndex();
            $stocks = $planService->fetchLiveStocks();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            Log::channel('my_sklad')->error('Не удалось получить данные техкарты', [
                'session_type' => self::SESSION_TYPE,
                'processingPlanId' => $planId,
                'error' => $e->getMessage(),
            ]);

            return 1;
        }

        $materials = $this->normalizeMaterials($materialsRaw, $assortment);
        $calculation = $techPlanService->calculate($materials, $stocks);

        $this->newLine();
        $this->line('<options=bold>'.$product->title.'</>');
        $this->line('  SKU: '.$product->sku.' · slug: '.$product->slug);
        $this->line('  Техкарта: '.$plan['name'] ?? '—'
            .' ['.substr((string) $planId, 0, 8).']');

        $processId = $plan['processingProcess']['meta']['href'] ?? null;

        if (! empty($processId)) {
            $processId = basename($processId);
            $this->line('  Техпроцесс: '.($processes[$processId] ?? '—'));
        }

        foreach ($results as $result) {
            $href = $result['assortment']['meta']['href'] ?? ($result['product']['meta']['href'] ?? null);
            $assortmentId = $planService->extractId($href);
            $name = $assortmentId !== null ? ($assortment[$assortmentId]['name'] ?? null) : null;

            $this->line('  Результат: '.($name ?? '—')
                .' × '.rtrim(rtrim(number_format((float) ($result['quantity'] ?? 0), 2, ',', ' '), '0'), ','));
        }

        $this->newLine();

        $rows = [];
        $index = 1;

        foreach ($calculation['structure'] as $material) {
            $quantity = (float) $material['quantity'];
            $stock = (int) $material['stock'];
            $possible = (int) $material['possible'];

            if (! $material['hasStockRow']) {
                $status = 'нет данных об остатке';
            } elseif ($stock <= 0) {
                $status = 'нет остатка';
            } elseif ($quantity <= 0) {
                $status = 'не влияет';
            } else {
                $status = 'хватает';
            }

            $rows[] = [
                (string) $index,
                $this->materialLabel($material),
                $this->quantity($quantity),
                $this->quantity($stock),
                $quantity > 0 ? (string) $possible : '—',
                $status,
            ];

            $index++;
        }

        $this->table(
            ['№', 'Материал', 'Нужно на букет', 'Остаток сейчас', 'Хватает на букетов', 'Статус'],
            $rows
        );

        $this->newLine();

        $count = (int) $calculation['count'];

        if ($count > 0) {
            $this->info("Можно собрать: <options=bold>{$count}</> букет(ов)");
        } else {
            $this->error('Можно собрать: 0 букетов');
        }

        $blocking = $calculation['blocking'];

        if ($blocking !== null) {
            $this->line('  Ограничитель: '.$techPlanService->reasonLabel($blocking));
        }

        $this->line('  Текущий skladCount в базе: '.$product->skladCount);

        $endTime = now();

        Log::channel('my_sklad')->info('Завершение детализации по составу техкарты', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'product_id' => $product->id,
            'processingPlanId' => $planId,
            'materials_count' => count($materials),
            'available' => $count,
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
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

            $name = $assortment[$id]['name'] ?? ($local[$id] ?? null);

            $materials[] = [
                'id' => $id,
                'name' => $name,
                'quantity' => (float) ($item['quantity'] ?? 0),
            ];
        }

        return $materials;
    }

    /**
     * Подпись материала: название, а при его отсутствии — короткий UUID.
     *
     * @param  array<string, mixed>  $material
     */
    private function materialLabel(array $material): string
    {
        $name = $material['name'] ?? null;

        if (! empty($name)) {
            return mb_substr((string) $name, 0, 46);
        }

        $id = (string) ($material['id'] ?? '');

        return $id !== '' ? substr($id, 0, 8).'…' : '—';
    }

    /**
     * Форматирование количества без лишних нулей.
     */
    private function quantity(float $value): string
    {
        if ($value == (int) $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 3, ',', ' '), '0'), ',');
    }
}
