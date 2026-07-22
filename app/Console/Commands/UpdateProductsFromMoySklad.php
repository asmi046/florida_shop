<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MySkladBundleService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateProductsFromMoySklad extends Command
{
    protected $signature = 'moysklad:update-products
                            {--dry-run : Только показать результаты расчёта без записи в БД}';

    protected $description = 'Обновление данных товаров на основе состава из МойСклад';

    const SESSION_TYPE = 'update_data';

    public function handle(MySkladBundleService $bundleService)
    {
        $dryRun = (bool) $this->option('dry-run');
        $startTime = now();

        Log::channel('my_sklad')->info('Начало обновления данных товаров', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'dry_run' => $dryRun,
        ]);

        $products = Product::whereNotNull('code')
            ->whereNotNull('externalCode')
            ->where('code', '!=', '')
            ->where('externalCode', '!=', '')
            ->get();

        if ($products->isEmpty()) {
            $this->warn('Нет товаров с заполненными полями code и externalCode');
            Log::channel('my_sklad')->warning('Нет товаров для обновления', [
                'session_type' => self::SESSION_TYPE,
            ]);
            return 0;
        }

        $modeLabel = $dryRun ? '   [DRY-RUN]' : '';
        $this->info("Найдено товаров для обновления: {$products->count()}" . $modeLabel);
        $this->newLine();

        $updated = 0;
        $skipped = 0;
        $dryReport = [];

        $this->withProgressBar($products, function ($product) use ($bundleService, $dryRun, &$updated, &$skipped, &$dryReport) {
            $result = $bundleService->updateProductStock($product, $dryRun);

            if ($result['skladCount'] > 0) {
                $updated++;
            } else {
                $skipped++;
            }

            if ($dryRun) {
                $dryReport[] = [
                    $product->id,
                    $this->truncate($product->title, 35),
                    $result['skladCount'],
                    $result['asc_nal'] ? '✓' : '✗',
                    $this->skipReasonLabel($result['skipped']),
                ];
            }
        });

        $endTime = now();

        $this->newLine(2);

        if ($dryRun && !empty($dryReport)) {
            $this->info('Результаты расчёта (без записи в БД):');
            $this->table(
                ['ID', 'Товар', 'Кол-во', 'asc_nal', 'Причина'],
                $dryReport
            );
            $this->newLine();
        }

        $this->info('Обновление завершено.');
        $this->table(['Метрика', 'Значение'], [
            ['Режим', $dryRun ? 'DRY-RUN' : 'Запись в БД'],
            ['Всего товаров', $products->count()],
            ['Можно сформировать', $updated],
            ['Нельзя сформировать', $skipped],
            ['Время выполнения', $startTime->diffForHumans($endTime, true)],
        ]);

        Log::channel('my_sklad')->info('Завершение обновления данных товаров', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'dry_run' => $dryRun,
            'total_products' => $products->count(),
            'updated' => $updated,
            'skipped' => $skipped,
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
    }

    private function skipReasonLabel(?string $reason): string
    {
        return match ($reason) {
            'no_assortiment' => 'Нет в my_sklad_assortiments',
            'no_components' => 'Нет components_href',
            'empty_components' => 'Состав пуст',
            'empty_structure' => 'Структура пуста',
            default => $reason === null ? 'Недостаточно остатков' : $reason,
        };
    }

    private function truncate(string $s, int $max): string
    {
        if (mb_strlen($s, 'UTF-8') <= $max) {
            return $s;
        }
        return mb_substr($s, 0, $max - 1, 'UTF-8') . '…';
    }
}