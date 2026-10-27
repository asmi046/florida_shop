<?php

namespace App\Console\Commands;

use App\Models\MySkladAssortiment;
use App\Services\MySkladProcessingPlanService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ListMoySkladProcessingPlans extends Command
{
    protected $signature = 'moysklad:processing-plans
                            {--limit=1000 : Максимум техкарт загрузить (макс. 1000)}
                            {--search= : Серверный поиск по названию техкарты}
                            {--archived : Включить архивные техкарты}
                            {--with-details : Догрузить материалы, этапы и результат техкарт}
                            {--detail-limit=15 : Максимум техкарт с деталями}
                            {--json : Вывести результат в JSON}';

    protected $description = 'Список зарегистрированных техкарт (processingplan) в МойСклад';

    const SESSION_TYPE = 'list_processing_plans';

    /**
     * Максимум прямых запросов к отсутствующим позициям номенклатуры за один запуск.
     */
    private const MAX_DIRECT_LOOKUPS = 20;

    public function handle(MySkladProcessingPlanService $service): int
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

        $limit = (int) $this->option('limit');
        $search = $this->option('search');
        $archived = (bool) $this->option('archived');
        $withDetails = (bool) $this->option('with-details');
        $detailLimit = (int) $this->option('detail-limit');
        $asJson = (bool) $this->option('json');

        $startTime = now();

        Log::channel('my_sklad')->info('Начало получения списка техкарт', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'limit' => $limit,
            'search' => $search,
            'archived' => $archived,
            'with_details' => $withDetails,
        ]);

        try {
            $plans = $service->fetchPlans($limit, $search, $archived);
            $processes = $service->fetchProcesses();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            Log::channel('my_sklad')->error('Не удалось получить список техкарт', [
                'session_type' => self::SESSION_TYPE,
                'error' => $e->getMessage(),
            ]);

            return 1;
        }

        $details = [];

        if ($withDetails && ! empty($plans)) {
            $details = $this->loadDetails($service, $plans, $detailLimit);
        }

        if ($asJson) {
            $this->line((string) json_encode(
                $this->buildJsonPayload($plans, $processes, $details),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            ));

            return 0;
        }

        if (empty($plans)) {
            $this->warn('Техкарты не найдены.');

            return 0;
        }

        $this->info('Найдено техкарт: '.count($plans));
        $this->newLine();

        $this->table(
            ['#', 'ID', 'Название', 'Техпроцесс', 'Эт.', 'Мат.', 'Прод.', 'С/с', 'Обновлена'],
            $this->buildRows($plans, $processes)
        );

        if (! empty($details)) {
            $this->newLine();
            $this->info('Состав техкарт:');
            $this->newLine();

            foreach ($details as $planId => $detail) {
                $this->printDetails($plans, $planId, $detail, $processes);
            }
        }

        $endTime = now();

        $this->newLine();
        $this->table(['Метрика', 'Значение'], [
            ['Всего техкарт', count($plans)],
            ['С деталями', count($details)],
            ['Техпроцессов', count($processes)],
            ['Время выполнения', $startTime->diffForHumans($endTime, true)],
        ]);

        Log::channel('my_sklad')->info('Завершение получения списка техкарт', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'total_plans' => count($plans),
            'with_details' => count($details),
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
    }

    /**
     * Загрузить детали (материалы, этапы, результат) для первых N техкарт.
     *
     * @param  array<int, array<string, mixed>>  $plans
     * @return array<string, array<string, mixed>>
     */
    private function loadDetails(MySkladProcessingPlanService $service, array $plans, int $detailLimit): array
    {
        $detailLimit = max(0, min($detailLimit, count($plans)));
        $selected = array_slice($plans, 0, $detailLimit);

        $this->info("Загрузка деталей для {$detailLimit} техкарт...");

        $raw = [];

        foreach ($selected as $plan) {
            $planId = $plan['id'] ?? null;

            if ($planId === null) {
                continue;
            }

            try {
                $raw[$planId] = $service->fetchDetails($planId);
            } catch (RuntimeException $e) {
                $this->warn("Не удалось получить детали техкарты «{$plan['name']}»: {$e->getMessage()}");
                Log::channel('my_sklad')->error('Ошибка загрузки деталей техкарты', [
                    'session_type' => self::SESSION_TYPE,
                    'plan_id' => $planId,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        $names = $this->resolveAssortmentNames($service, $raw);

        $details = [];

        foreach ($raw as $planId => $planRaw) {
            $details[$planId] = [
                'materials' => $this->normalizeItems($planRaw['materials'] ?? [], $service, $names),
                'stages' => $planRaw['stages'] ?? [],
                'products' => $this->normalizeItems($planRaw['products'] ?? [], $service, $names),
            ];
        }

        return $details;
    }

    /**
     * Разрешить названия номенклатуры: локальная таблица → ассортимент API → прямая ссылка.
     *
     * @param  array<string, array<string, mixed>>  $raw
     * @return array<string, string>
     */
    private function resolveAssortmentNames(MySkladProcessingPlanService $service, array $raw): array
    {
        $hrefs = [];

        foreach ($raw as $planRaw) {
            foreach (['materials', 'products'] as $key) {
                foreach ($planRaw[$key] ?? [] as $item) {
                    $href = $item['assortment']['meta']['href'] ?? ($item['product']['meta']['href'] ?? null);

                    if (empty($href)) {
                        continue;
                    }

                    $id = $service->extractId($href);

                    if ($id !== null) {
                        $hrefs[$id] = $href;
                    }
                }
            }
        }

        if (empty($hrefs)) {
            return [];
        }

        $ids = array_keys($hrefs);

        $names = MySkladAssortiment::whereIn('sklad_id', $ids)->pluck('name', 'sklad_id')->toArray();
        $missing = array_values(array_diff($ids, array_keys($names)));

        if (! empty($missing)) {
            try {
                $names = array_merge($service->fetchAssortmentNames(), $names);
            } catch (RuntimeException $e) {
                $this->warn('Не удалось получить справочник номенклатуры: '.$e->getMessage());
            }

            $missing = array_values(array_diff($missing, array_keys($names)));
        }

        // Позиции, которых нет в ассортименте (архивные) — уточняем по прямой ссылке.
        foreach (array_slice($missing, 0, self::MAX_DIRECT_LOOKUPS) as $id) {
            $name = $service->fetchEntityName($hrefs[$id]);

            if ($name !== null) {
                $names[$id] = $name;
            }
        }

        return $names;
    }

    /**
     * Привести материалы/продукты к виду: id, название, количество.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, string>  $names
     * @return array<int, array<string, mixed>>
     */
    private function normalizeItems(array $items, MySkladProcessingPlanService $service, array $names = []): array
    {
        $result = [];

        foreach ($items as $item) {
            $href = $item['assortment']['meta']['href'] ?? ($item['product']['meta']['href'] ?? null);
            $id = $service->extractId($href);

            $result[] = [
                'id' => $id,
                'name' => $id !== null ? ($names[$id] ?? null) : null,
                'quantity' => (float) ($item['quantity'] ?? 0),
            ];
        }

        return $result;
    }

    /**
     * Строки основной таблицы.
     *
     * @param  array<int, array<string, mixed>>  $plans
     * @param  array<string, string>  $processes
     * @return array<int, array<int, string>>
     */
    private function buildRows(array $plans, array $processes): array
    {
        $rows = [];
        $index = 1;

        foreach ($plans as $plan) {
            $processId = $plan['processId'] ?? null;

            $rows[] = [
                (string) $index,
                substr((string) ($plan['id'] ?? ''), 0, 8),
                $this->truncate((string) ($plan['name'] ?? ''), 52),
                $this->truncate($processId !== null ? ($processes[$processId] ?? '—') : '—', 24),
                (string) ($plan['stagesCount'] ?? 0),
                (string) ($plan['materialsCount'] ?? 0),
                (string) ($plan['productsCount'] ?? 0),
                number_format((float) ($plan['cost'] ?? 0), 2, ',', ' '),
                (string) ($plan['updated'] ?? ''),
            ];

            $index++;
        }

        return $rows;
    }

    /**
     * Вывести состав одной техкарты.
     *
     * @param  array<int, array<string, mixed>>  $plans
     * @param  array<string, mixed>  $detail
     * @param  array<string, string>  $processes
     */
    private function printDetails(array $plans, string $planId, array $detail, array $processes): void
    {
        $plan = collect($plans)->firstWhere('id', $planId) ?? [];

        $this->line('<options=bold>'.$this->truncate((string) ($plan['name'] ?? $planId), 80).'</>');
        $this->line('  Техпроцесс: '.($processes[$plan['processId'] ?? ''] ?? '—'));

        if (! empty($plan['uuidHref'])) {
            $this->line('  Ссылка: '.$plan['uuidHref']);
        }

        $this->line('  Материалы ('.count($detail['materials'] ?? []).'):');

        foreach ($detail['materials'] ?? [] as $material) {
            $this->line('    - '.$this->itemLabel($material));
        }

        $this->line('  Результат ('.count($detail['products'] ?? []).'):');

        foreach ($detail['products'] ?? [] as $product) {
            $this->line('    - '.$this->itemLabel($product));
        }

        if (! empty($detail['stages'])) {
            $this->line('  Этапы ('.count($detail['stages']).'):');

            foreach ($detail['stages'] as $stage) {
                $this->line('    - стоимость: '.number_format((float) ($stage['cost'] ?? 0), 2, ',', ' ')
                    .' · нормо-час: '.number_format((float) ($stage['standardHour'] ?? 0), 2, ',', ' '));
            }
        }

        $this->newLine();
    }

    /**
     * Подпись позиции состава: название (id) × количество.
     *
     * @param  array<string, mixed>  $item
     */
    private function itemLabel(array $item): string
    {
        $name = $item['name'] ?? null;
        $id = $item['id'] ?? null;

        $title = $name !== null
            ? $this->truncate($name, 60)
            : ($id !== null ? substr((string) $id, 0, 8) : '—');

        $quantity = (float) ($item['quantity'] ?? 0);
        $quantity = $quantity == (int) $quantity ? (string) (int) $quantity : rtrim(rtrim(number_format($quantity, 3, ',', ' '), '0'), ',');

        return $title.' × '.$quantity;
    }

    /**
     * JSON-структура для машинной обработки.
     *
     * @param  array<int, array<string, mixed>>  $plans
     * @param  array<string, string>  $processes
     * @param  array<string, array<string, mixed>>  $details
     * @return array<string, mixed>
     */
    private function buildJsonPayload(array $plans, array $processes, array $details): array
    {
        $payload = [];

        foreach ($plans as $plan) {
            $plan['processName'] = $processes[$plan['processId'] ?? ''] ?? null;
            $plan['id'] = $plan['id'] ?? null;
            $payload[] = $plan;
        }

        return [
            'total' => count($plans),
            'processes' => $processes,
            'plans' => $payload,
            'details' => $details,
        ];
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
