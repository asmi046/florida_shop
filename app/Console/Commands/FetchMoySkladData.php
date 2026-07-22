<?php

namespace App\Console\Commands;

use App\Models\MySkladAssortiment;
use App\Models\MySkladStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchMoySkladData extends Command
{
    protected $signature = 'moysklad:fetch {--limit=10000 : Количество записей}';

    protected $description = 'Получение данных из сервиса МойСклад';

    const SESSION_TYPE = 'get_data';

    public function handle()
    {
        $login = config('mysklad.login');
        $password = config('mysklad.password');
        $limit = $this->option('limit');

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
        $this->line("Лимит записей: {$limit}");
        $this->newLine();

        $this->info('Очистка таблиц перед загрузкой новых данных...');
        MySkladAssortiment::truncate();
        MySkladStock::truncate();
        $this->info('Таблицы очищены.');
        $this->newLine();

        $this->info('1. Получение ассортимента...');
        $response = Http::withBasicAuth($login, $password)
            ->withHeaders([
                'Accept' => 'application/json;charset=utf-8',
                'Accept-Encoding' => 'gzip',
            ])
            ->get('https://api.moysklad.ru/api/remap/1.2/entity/assortment', [
                'limit' => $limit,
            ]);

        if (! $response->successful()) {
            $this->error('Ошибка при запросе ассортимента: '.$response->status());
            $this->error($response->body());
            Log::channel('my_sklad')->error('Ошибка при запросе ассортимента', [
                'session_type' => self::SESSION_TYPE,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return 1;
        }

        $assortmentData = $response->json();
        $rows = $assortmentData['rows'] ?? [];

        $this->info('Сохранение ассортимента в базу данных...');
        $this->withProgressBar($rows, function ($row) {
            MySkladAssortiment::updateOrCreate(
                ['sklad_id' => $row['id']],
                [
                    'type' => $row['meta']['type'] ?? null,
                    'name' => $row['name'] ?? null,
                    'code' => $row['code'] ?? null,
                    'externalCode' => $row['externalCode'] ?? null,
                    'pathName' => $row['pathName'] ?? null,
                    'components_href' => $row['components']['meta']['href'] ?? null,
                    'components_size' => $row['components']['meta']['size'] ?? null,
                ]
            );
        });

        $assortmentCount = count($rows);
        $this->newLine();
        $this->info("Ассортимент: обработано {$assortmentCount} записей");
        $this->newLine();

        $this->info('2. Получение остатков...');
        $stockResponse = Http::withBasicAuth($login, $password)
            ->withHeaders([
                'Accept' => 'application/json;charset=utf-8',
                'Accept-Encoding' => 'gzip',
            ])
            ->get('https://api.moysklad.ru/api/remap/1.2/report/stock/all/current', [
                'stockType' => 'freeStock',
                'include' => 'zeroLines',
            ]);

        if (! $stockResponse->successful()) {
            $this->error('Ошибка при запросе остатков: '.$stockResponse->status());
            $this->error($stockResponse->body());
            Log::channel('my_sklad')->error('Ошибка при запросе остатков', [
                'session_type' => self::SESSION_TYPE,
                'status' => $stockResponse->status(),
                'body' => $stockResponse->body(),
            ]);

            return 1;
        }

        $stockData = $stockResponse->json();
        $stockRows = $stockData ?? [];

        $this->info('Сохранение остатков в базу данных...');
        $this->withProgressBar($stockRows, function ($row) {
            MySkladStock::updateOrCreate(
                ['assortmentId' => $row['assortmentId']],
                [
                    'freeStock' => $row['freeStock'] ?? 0,
                ]
            );
        });

        $stockCount = count($stockRows);
        $this->newLine();
        $this->info("Остатки: обработано {$stockCount} записей");
        $this->newLine();

        $endTime = now();

        Log::channel('my_sklad')->info('Завершение получения данных из МойСклад', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'assortment_count' => $assortmentCount,
            'stock_count' => $stockCount,
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        $this->info('Все данные успешно получены и сохранены в базу данных.');
        $this->info('Время выполнения: '.$startTime->diffForHumans($endTime, true));

        return 0;
    }
}
