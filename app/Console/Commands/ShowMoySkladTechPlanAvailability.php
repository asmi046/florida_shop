<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MySkladTechPlanAvailabilityService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShowMoySkladTechPlanAvailability extends Command
{
    protected $signature = 'moysklad:techplan-availability
                            {product : SKU или slug товара на сайте}
                            {--no-cache : Не использовать кэш структуры техкарты}';

    protected $description = 'Детализация по составу букета: сколько материала доступно на складе и сколько букетов можно собрать';

    const SESSION_TYPE = 'techplan_availability';

    public function handle(MySkladTechPlanAvailabilityService $service): int
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

        $startTime = now();

        Log::channel('my_sklad')->info('Начало детализации по составу техкарты', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'product_id' => $product->id,
            'processingPlanId' => $product->processingPlanId,
        ]);

        try {
            $data = $service->build($product, ! $this->option('no-cache'));
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            Log::channel('my_sklad')->error('Не удалось получить данные техкарты', [
                'session_type' => self::SESSION_TYPE,
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return 1;
        }

        $this->newLine();
        $this->line('<options=bold>'.$data['product']['title'].'</>');
        $this->line('  SKU: '.$data['product']['sku'].' · slug: '.$data['product']['slug']);
        $this->line('  Техкарта: '.$data['plan']['name'].' ['.substr((string) $data['plan']['id'], 0, 8).']');

        if (! empty($data['plan']['processName'])) {
            $this->line('  Техпроцесс: '.$data['plan']['processName']);
        }

        if (! empty($data['result'])) {
            $this->line('  Результат: '.($data['result']['name'] ?? '—')
                .' × '.$this->quantity((float) $data['result']['quantity']));
        }

        $this->newLine();

        $rows = [];
        $index = 1;

        foreach ($data['materials'] as $material) {
            $rows[] = [
                (string) $index,
                $this->materialLabel($material),
                $this->quantity((float) $material['quantity']),
                $this->quantity((float) $material['stock']),
                (float) $material['quantity'] > 0 ? (string) $material['possible'] : '—',
                $material['status'],
            ];

            $index++;
        }

        $this->table(
            ['№', 'Материал', 'Нужно на букет', 'Остаток сейчас', 'Хватает на букетов', 'Статус'],
            $rows
        );

        $this->newLine();

        if ($data['available'] > 0) {
            $this->info("Можно собрать: <options=bold>{$data['available']}</> букет(ов)");
        } else {
            $this->error('Можно собрать: 0 букетов');
        }

        if (! empty($data['blocking'])) {
            $this->line('  Ограничитель: '.$data['blocking']);
        }

        $this->line('  Текущий skladCount в базе: '.$data['product']['skladCount']);

        $endTime = now();

        Log::channel('my_sklad')->info('Завершение детализации по составу техкарты', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'product_id' => $product->id,
            'processingPlanId' => $data['plan']['id'],
            'materials_count' => count($data['materials']),
            'available' => $data['available'],
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
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
