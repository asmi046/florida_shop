<?php

namespace App\Console\Commands;

use App\Models\MySkladAssortiment;
use App\Models\MySkladStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchMoySkladData extends Command
{
    protected $signature = 'moysklad:fetch {--limit=1000 : Максимум записей за один запрос (макс. 1000)}';

    protected $description = 'Получение данных из сервиса МойСклад';

    const SESSION_TYPE = 'get_data';

    /**
     * Размер чанка для пакетной вставки в базу.
     */
    private const CHUNK_SIZE = 500;

    public function handle()
    {
        $login = config('mysklad.login');
        $password = config('mysklad.password');
        $limit = max(1, min((int) $this->option('limit'), 1000));

        if (empty($login) || empty($password)) {
            $this->error('Логин или пароль МойСклад не настроены. Проверьте файл .env');
            Log::channel('my_sklad')->error('Не настроены логин или пароль МойСклад', [
                'session_type' => self::SESSION_TYPE,
                'error' => 'credentials_missing',
            ]);

            return 1;
        }

        $startTime = now();

        Log::channel('my_sklad')->info('Начало получения данных из МойСклад', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
        ]);

        $this->info('Отправка запроса к API МойСклад...');
        $this->line('Лимит записей за запрос: '.$limit);
        $this->newLine();

        $this->info('1. Получение ассортимента...');
        $assortmentRows = $this->fetchAssortment($login, $password, $limit);

        if ($assortmentRows === null) {
            return 1;
        }

        $this->info('Получено позиций номенклатуры: '.count($assortmentRows));
        $this->newLine();

        $this->info('2. Получение остатков...');
        $stockRows = $this->fetchStocks($login, $password);

        if ($stockRows === null) {
            return 1;
        }

        $this->info('Получено позиций остатков: '.count($stockRows));
        $this->newLine();

        // Очистка выполняется только после того, как все данные успешно получены,
        // иначе при ошибке API таблицы остались бы пустыми.
        $this->info('Очистка таблиц перед загрузкой новых данных...');
        MySkladAssortiment::truncate();
        MySkladStock::truncate();
        $this->info('Таблицы очищены.');
        $this->newLine();

        $this->info('Сохранение ассортимента в базу данных...');
        $this->insertAssortment($assortmentRows);

        $this->info('Сохранение остатков в базу данных...');
        $this->insertStocks($stockRows);

        $endTime = now();

        Log::channel('my_sklad')->info('Завершение получения данных из МойСклад', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'assortment_count' => count($assortmentRows),
            'stock_count' => count($stockRows),
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        $this->info('Все данные успешно получены и сохранены в базу данных.');
        $this->info('Время выполнения: '.$startTime->diffForHumans($endTime, true));

        return 0;
    }

    /**
     * Получить номенклатуру с постраничной выдачей.
     *
     * API МойСклад молча ограничивает limit значением 1000, поэтому страницы
     * обходятся по meta.nextHref — иначе часть номенклатуры потерялась бы.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function fetchAssortment(string $login, string $password, int $limit): ?array
    {
        $rows = [];
        $offset = 0;
        $url = 'https://api.moysklad.ru/api/remap/1.2/entity/assortment';

        while (true) {
            $response = Http::withBasicAuth($login, $password)
                ->withHeaders([
                    'Accept' => 'application/json;charset=utf-8',
                    'Accept-Encoding' => 'gzip',
                ])
                ->get($url, [
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

            if (! $response->successful()) {
                $this->error('Ошибка при запросе ассортимента: '.$response->status());
                $this->error($response->body());
                Log::channel('my_sklad')->error('Ошибка при запросе ассортимента', [
                    'session_type' => self::SESSION_TYPE,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json() ?? [];
            $page = $data['rows'] ?? [];

            if (empty($page)) {
                break;
            }

            foreach ($page as $row) {
                $rows[] = $row;
            }

            $nextHref = $data['meta']['nextHref'] ?? null;

            if (empty($nextHref)) {
                break;
            }

            $offset = (int) ($data['meta']['offset'] ?? $offset) + count($page);
            $url = $nextHref;
        }

        return $rows;
    }

    /**
     * Получить актуальные остатки.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function fetchStocks(string $login, string $password): ?array
    {
        $response = Http::withBasicAuth($login, $password)
            ->withHeaders([
                'Accept' => 'application/json;charset=utf-8',
                'Accept-Encoding' => 'gzip',
            ])
            ->get('https://api.moysklad.ru/api/remap/1.2/report/stock/all/current', [
                'stockType' => 'freeStock',
                'include' => 'zeroLines',
            ]);

        if (! $response->successful()) {
            $this->error('Ошибка при запросе остатков: '.$response->status());
            $this->error($response->body());
            Log::channel('my_sklad')->error('Ошибка при запросе остатков', [
                'session_type' => self::SESSION_TYPE,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $data = $response->json();

        // Отчёт по остаткам приходит массивом строк, а не объектом с rows.
        return array_is_list($data) ? $data : ($data['rows'] ?? []);
    }

    /**
     * Пакетная вставка ассортимента.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertAssortment(array $rows): void
    {
        $now = now();
        $chunks = array_chunk($rows, self::CHUNK_SIZE);

        $this->withProgressBar($chunks, function ($chunk) use ($now) {
            $data = [];

            foreach ($chunk as $row) {
                $data[] = [
                    'sklad_id' => $row['id'],
                    'type' => $row['meta']['type'] ?? null,
                    'name' => $row['name'] ?? null,
                    'code' => $row['code'] ?? null,
                    'externalCode' => $row['externalCode'] ?? null,
                    'pathName' => $row['pathName'] ?? null,
                    'components_href' => $row['components']['meta']['href'] ?? null,
                    'components_size' => $row['components']['meta']['size'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            MySkladAssortiment::insert($data);
        });

        $this->newLine();
        $this->info('Ассортимент: сохранено '.count($rows).' записей');
        $this->newLine();
    }

    /**
     * Пакетная вставка остатков.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertStocks(array $rows): void
    {
        $now = now();
        $chunks = array_chunk($rows, self::CHUNK_SIZE);

        $this->withProgressBar($chunks, function ($chunk) use ($now) {
            $data = [];

            foreach ($chunk as $row) {
                if (empty($row['assortmentId'])) {
                    continue;
                }

                $data[] = [
                    'assortmentId' => $row['assortmentId'],
                    'freeStock' => (int) ($row['freeStock'] ?? 0),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($data)) {
                MySkladStock::insert($data);
            }
        });

        $this->newLine();
        $this->info('Остатки: сохранено '.count($rows).' записей');
        $this->newLine();
    }
}
