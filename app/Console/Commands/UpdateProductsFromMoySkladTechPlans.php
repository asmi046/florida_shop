<?php

namespace App\Console\Commands;

use App\Models\MySkladAssortiment;
use App\Services\MySkladProcessingPlanService;
use App\Services\MySkladTechPlanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class UpdateProductsFromMoySkladTechPlans extends Command
{
    protected $signature = 'moysklad:update-products-from-techplans
                            {--dry-run : Только показать результаты расчёта без записи в БД}
                            {--limit=1000 : Максимум техкарт обработать (макс. 1000)}
                            {--search= : Серверный поиск по названию техкарты}
                            {--throttle=1100 : Пауза между запросами к API, мс (лимит МойСклад — 1 req/s)}
                            {--skip-unmapped : Не выводить техкарты, не связанные с товарами магазина}';

    protected $description = 'Обновление данных товаров на основе состава из техкарты МойСклад (processingplan)';

    const SESSION_TYPE = 'update_techplans';

    public function handle(MySkladProcessingPlanService $planService, MySkladTechPlanService $techPlanService): int
    {
        $login = config('mysklad.login');
        $password = config('mysklad.password');

        if (empty($login) || empty($password)) {
            $this->error('Логин или пароль МойСклад не настроены. Проверьте файл .env');
            Log::channel('my_sklad')->error('Не настроены логин или пароль МойСклад', [
                'session_type' => self::SESSION_TYPE,
                'error' => 'credentials_missing',
            ]);

            return 1;
        }

        $dryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $search = $this->option('search');
        $throttle = (int) $this->option('throttle');
        $skipUnmapped = (bool) $this->option('skip-unmapped');

        $planService->setThrottleMs($throttle);

        $startTime = now();
        $modeLabel = $dryRun ? '   [DRY-RUN]' : '';

        Log::channel('my_sklad')->info('Начало обновления данных товаров по техкартам', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'dry_run' => $dryRun,
            'limit' => $limit,
            'search' => $search,
        ]);

        $this->info("Обновление товаров по техкартам МойСклад{$modeLabel}");
        $this->newLine();

        try {
            $plans = $planService->fetchPlans($limit, $search);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            Log::channel('my_sklad')->error('Не удалось получить данные техкарт', [
                'session_type' => self::SESSION_TYPE,
                'error' => $e->getMessage(),
            ]);

            return 1;
        }

        if (empty($plans)) {
            $this->warn('Техкарты не найдены.');

            return 0;
        }

        $stocks = $techPlanService->fetchStocks();

        if (empty($stocks)) {
            $this->warn('Таблица my_sklad_stocks пуста — остатки неизвестны. Запустите moysklad:fetch.');
        }

        $this->info('Техкарт к обработке: '.count($plans));
        $this->info('Позиций с остатками: '.count($stocks));
        $this->newLine();

        $report = [];
        $unmapped = [];
        $updated = 0;
        $skipped = 0;
        $unmappedCount = 0;

        $this->withProgressBar($plans, function ($plan) use (
            $planService, $techPlanService, $stocks, $dryRun, $skipUnmapped,
            &$report, &$unmapped, &$updated, &$skipped, &$unmappedCount
        ) {
            $planId = $plan['id'] ?? null;

            if ($planId === null) {
                return;
            }

            $products = $techPlanService->findProductsByPlanId($planId);

            if ($products->isEmpty()) {
                $unmappedCount++;

                if (! $skipUnmapped) {
                    $unmapped[] = [
                        (string) substr((string) $planId, 0, 8),
                        $this->truncate((string) ($plan['name'] ?? ''), 40),
                    ];
                }

                return;
            }

            try {
                $materials = $this->fetchMaterials($planService, $planService->fetchPlanMaterials($planId));
            } catch (RuntimeException $e) {
                $this->warn("Не удалось получить материалы техкарты «{$plan['name']}»: {$e->getMessage()}");
                Log::channel('my_sklad')->error('Ошибка загрузки материалов техкарты', [
                    'session_type' => self::SESSION_TYPE,
                    'plan_id' => $planId,
                    'error' => $e->getMessage(),
                ]);

                return;
            }

            $calculation = $techPlanService->calculate($materials, $stocks);

            foreach ($products as $product) {
                $reason = $techPlanService->reasonLabel($calculation['blocking']);

                if ($calculation['count'] <= 0 && $reason === '') {
                    $reason = 'Недостаточно остатков';
                }

                if ($dryRun) {
                    $action = 'к записи';
                } elseif ($product->skladCount === $calculation['count']
                    && (bool) $product->asc_nal === ($calculation['count'] <= 0)) {
                    $action = 'без изменений';
                    $skipped++;
                } else {
                    $techPlanService->apply($product, $calculation['count'], false);
                    $action = 'записано';
                    $updated++;
                }

                $report[] = [
                    (string) $product->id,
                    $this->truncate((string) $product->title, 34),
                    (string) $calculation['count'],
                    ($calculation['count'] <= 0 ? '✓' : '✗'),
                    $reason !== '' ? $reason : 'Можно сформировать',
                    $action,
                ];

                Log::channel('my_sklad')->info('Товар пересчитан по техкарте', [
                    'session_type' => self::SESSION_TYPE,
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'processingPlanId' => $planId,
                    'materials_count' => count($materials),
                    'skladCount' => $calculation['count'],
                    'asc_nal' => $calculation['count'] <= 0,
                    'dry_run' => $dryRun,
                ]);
            }
        });

        $this->newLine(2);

        if (! empty($report)) {
            $this->info($dryRun
                ? 'Результаты расчёта (без записи в БД):'
                : 'Результаты расчёта:');
            $this->newLine();

            $this->table(
                ['ID', 'Товар', 'Кол-во', 'asc_nal', 'Причина', 'Действие'],
                $report
            );
            $this->newLine();
        } else {
            $this->warn('Ни один товар не связан с техкартами.');
            $this->newLine();
        }

        if (! empty($unmapped)) {
            $this->info('Техкарты без товара в магазине ('.count($unmapped).'):');
            $this->newLine();
            $this->table(['Техкарта', 'Название'], $unmapped);
            $this->newLine();
        }

        $endTime = now();

        $this->table(['Метрика', 'Значение'], [
            ['Режим', $dryRun ? 'DRY-RUN' : 'Запись в БД'],
            ['Техкарт обработано', count($plans)],
            ['Товаров пересчитано', count($report)],
            ['Обновлено записей', $dryRun ? 0 : $updated],
            ['Без изменений', $skipped],
            ['Техкарт без товара', $unmappedCount],
            ['Время выполнения', $startTime->diffForHumans($endTime, true)],
        ]);

        Log::channel('my_sklad')->info('Завершение обновления данных товаров по техкартам', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'dry_run' => $dryRun,
            'total_plans' => count($plans),
            'products_processed' => count($report),
            'updated' => $dryRun ? 0 : $updated,
            'skipped' => $skipped,
            'unmapped_plans' => $unmappedCount,
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
    }

    /**
     * Привести материалы к виду: id / name / quantity.
     *
     * Название берётся из локальной таблицы ассортимента.
     *
     * @param  array<int, array<string, mixed>>  $raw
     * @return array<int, array<string, mixed>>
     */
    private function fetchMaterials(MySkladProcessingPlanService $planService, array $raw): array
    {
        $ids = [];

        foreach ($raw as $item) {
            $href = $item['assortment']['meta']['href'] ?? ($item['product']['meta']['href'] ?? null);
            $id = $planService->extractId($href);

            if ($id !== null) {
                $ids[$id] = $href;
            }
        }

        $names = MySkladAssortiment::query()
            ->whereIn('sklad_id', array_keys($ids))
            ->pluck('name', 'sklad_id')
            ->all();

        $materials = [];

        foreach ($raw as $item) {
            $href = $item['assortment']['meta']['href'] ?? ($item['product']['meta']['href'] ?? null);
            $id = $planService->extractId($href);

            $name = $id !== null ? ($names[$id] ?? null) : null;

            if ($name === null && $id !== null) {
                $name = $assortment[$id]['name'] ?? null;
            }

            $materials[] = [
                'id' => $id,
                'name' => $name,
                'quantity' => (float) ($item['quantity'] ?? 0),
            ];
        }

        return $materials;
    }

    /**
     * Обрезка строки до указанной длины с многобайтовой безопасностью.
     */
    private function truncate(string $s, int $max): string
    {
        if (mb_strlen($s, 'UTF-8') <= $max) {
            return $s;
        }

        return mb_substr($s, 0, $max - 1, 'UTF-8').'…';
    }
}
