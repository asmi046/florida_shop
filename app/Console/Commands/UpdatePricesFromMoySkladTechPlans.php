<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MySkladProcessingPlanService;
use App\Services\MySkladTechPlanPriceService;
use App\Services\MySkladTechPlanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class UpdatePricesFromMoySkladTechPlans extends Command
{
    protected $signature = 'moysklad:update-prices-from-techplans
                            {--dry-run : Только показать результаты без записи в БД}
                            {--limit=1000 : Максимум техкарт обработать (макс. 1000)}
                            {--search= : Серверный поиск по названию техкарты}
                            {--markup=1.00 : Коэффициент наценки к цене МойСклад}
                            {--old-price : Переносить текущую цену товара в old_price}
                            {--throttle=1100 : Пауза между запросами к API, мс (лимит МойСклад — 1 req/s)}';

    protected $description = 'Обновление цен товаров по цене результата техкарты МойСклад (processingplan)';

    const SESSION_TYPE = 'update_prices';

    public function handle(
        MySkladProcessingPlanService $planService,
        MySkladTechPlanService $techPlanService,
        MySkladTechPlanPriceService $priceService
    ): int {
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
        $markup = (float) $this->option('markup');
        $moveToOldPrice = (bool) $this->option('old-price');
        $throttle = (int) $this->option('throttle');

        if ($markup <= 0) {
            $this->error('Коэффициент наценки должен быть больше нуля.');

            return 1;
        }

        $planService->setThrottleMs($throttle);

        $startTime = now();
        $modeLabel = $dryRun ? '   [DRY-RUN]' : '';

        Log::channel('my_sklad')->info('Начало обновления цен товаров по техкартам', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'dry_run' => $dryRun,
            'limit' => $limit,
            'search' => $search,
            'markup' => $markup,
            'old_price' => $moveToOldPrice,
        ]);

        $this->info("Обновление цен товаров по цене результата техкарты{$modeLabel}");
        $this->newLine();

        try {
            $plans = $planService->fetchPlans($limit, $search);
            $assortment = $planService->fetchAssortmentIndex();
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

        $this->info('Техкарт к обработке: '.count($plans));
        $this->info('Источник цены: «Цена по умолчанию» в МойСклад'
            .' · наценка: ×'.number_format($markup, 2, ',', ' '));
        $this->newLine();

        $report = [];
        $unmapped = [];
        $updated = 0;
        $skipped = 0;
        $noPriceCount = 0;
        $unmappedCount = 0;

        $this->withProgressBar($plans, function ($plan) use (
            $planService, $techPlanService, $priceService, $assortment,
            $dryRun, $moveToOldPrice, $markup,
            &$report, &$unmapped, &$updated, &$skipped, &$noPriceCount, &$unmappedCount
        ) {
            $planId = $plan['id'] ?? null;

            if ($planId === null) {
                return;
            }

            $products = $techPlanService->findProductsByPlanId($planId);

            if ($products->isEmpty()) {
                $unmappedCount++;

                $unmapped[] = [
                    (string) substr((string) $planId, 0, 8),
                    $this->truncate((string) ($plan['name'] ?? ''), 40),
                ];

                return;
            }

            try {
                $results = $planService->fetchPlanProducts($planId);
            } catch (RuntimeException $e) {
                $this->warn("Не удалось получить результат техкарты «{$plan['name']}»: {$e->getMessage()}");
                Log::channel('my_sklad')->error('Ошибка загрузки результата техкарты', [
                    'session_type' => self::SESSION_TYPE,
                    'plan_id' => $planId,
                    'error' => $e->getMessage(),
                ]);

                return;
            }

            $prices = $this->collectResultPrices($planService, $results, $assortment);

            foreach ($products as $product) {
                $msPrice = $this->matchPrice($product, $prices);

                if ($msPrice === null) {
                    $noPriceCount++;
                    $action = 'пропущено (нет цены в МойСклад)';

                    $report[] = [
                        (string) $product->id,
                        $this->truncate((string) $product->title, 30),
                        '—',
                        '—',
                        '—',
                        $this->money((float) $product->price),
                        '—',
                        $action,
                    ];

                    continue;
                }

                $newPrice = $priceService->calculate($msPrice, $markup);
                $currentPrice = (float) $product->price;

                if ($currentPrice === $newPrice) {
                    $action = 'без изменений';
                    $skipped++;
                } else {
                    $priceService->apply($product, $newPrice, $dryRun, $moveToOldPrice);
                    $action = $dryRun ? 'к записи' : 'записано';
                    $updated++;
                }

                $report[] = [
                    (string) $product->id,
                    $this->truncate((string) $product->title, 30),
                    $this->money($msPrice),
                    '×'.number_format($markup, 2, ',', ' '),
                    $this->money($newPrice),
                    $this->money($currentPrice),
                    $this->delta($currentPrice, $newPrice),
                    $action,
                ];

                Log::channel('my_sklad')->info('Цена товара обновлена по цене результата техкарты', [
                    'session_type' => self::SESSION_TYPE,
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'processingPlanId' => $planId,
                    'ms_price' => $msPrice,
                    'markup' => $markup,
                    'price_before' => $currentPrice,
                    'price_after' => $newPrice,
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
                ['ID', 'Товар', 'Цена МС', 'Наценка', 'Новая цена', 'Текущая', 'Δ', 'Действие'],
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
            ['Пропущено (нет цены в МС)', $noPriceCount],
            ['Техкарт без товара', $unmappedCount],
            ['Время выполнения', $startTime->diffForHumans($endTime, true)],
        ]);

        Log::channel('my_sklad')->info('Завершение обновления цен товаров по техкартам', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'dry_run' => $dryRun,
            'total_plans' => count($plans),
            'products_processed' => count($report),
            'updated' => $dryRun ? 0 : $updated,
            'skipped' => $skipped,
            'without_price' => $noPriceCount,
            'unmapped_plans' => $unmappedCount,
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
    }

    /**
     * Собрать цены результатов техкарты.
     *
     * @param  array<int, array<string, mixed>>  $results
     * @param  array<string, array<string, mixed>>  $assortment
     * @return array{byKey: array<string, float>, byId: array<string, float>}
     */
    private function collectResultPrices(MySkladProcessingPlanService $planService, array $results, array $assortment): array
    {
        $byKey = [];
        $byId = [];

        foreach ($results as $result) {
            $href = $result['assortment']['meta']['href'] ?? ($result['product']['meta']['href'] ?? null);
            $assortmentId = $planService->extractId($href);

            if ($assortmentId === null) {
                continue;
            }

            $meta = $assortment[$assortmentId] ?? null;

            if ($meta === null || ($meta['salePrice'] ?? null) === null) {
                continue;
            }

            // Цены МойСклад в копейках, в отчёте — рубли.
            $price = (float) $meta['salePrice'] / 100;

            $byId[$assortmentId] = $price;

            foreach (['code', 'externalCode'] as $key) {
                if (! empty($meta[$key])) {
                    $byKey[(string) $meta[$key]] = $price;
                }
            }
        }

        return ['byKey' => $byKey, 'byId' => $byId];
    }

    /**
     * Подобрать цену МойСклад для товара магазина.
     *
     * @param  array{byKey: array<string, float>, byId: array<string, float>}  $prices
     */
    private function matchPrice(Product $product, array $prices): ?float
    {
        foreach (['code', 'externalCode'] as $key) {
            $value = (string) ($product->{$key} ?? '');

            if ($value !== '' && isset($prices['byKey'][$value])) {
                return $prices['byKey'][$value];
            }
        }

        // Единственный результат техкарты — берём его цену.
        if (count($prices['byId']) === 1) {
            return reset($prices['byId']);
        }

        return null;
    }

    /**
     * Форматирование денежной суммы.
     */
    private function money(float $value): string
    {
        return number_format($value, 2, ',', ' ').' ₽';
    }

    /**
     * Разница между текущей и новой ценой.
     */
    private function delta(float $current, float $new): string
    {
        $diff = $new - $current;

        if ($diff == 0.0) {
            return '—';
        }

        return ($diff > 0 ? '+' : '').number_format($diff, 2, ',', ' ');
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
