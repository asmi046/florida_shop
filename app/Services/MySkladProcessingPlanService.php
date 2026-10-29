<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MySkladProcessingPlanService
{
    /**
     * Базовый URL remap API МойСклад.
     */
    public const BASE_URL = 'https://api.moysklad.ru/api/remap/1.2';

    /**
     * Максимальный лимит выборки за один запрос (ограничение API).
     */
    public const MAX_LIMIT = 1000;

    /**
     * Кэш справочника номенклатуры (id => name) на время выполнения.
     *
     * @var array<string, string>|null
     */
    private ?array $assortmentNames = null;

    /**
     * Кэш полного индекса номенклатуры (id => name/code/externalCode).
     *
     * @var array<string, array<string, mixed>>|null
     */
    private ?array $assortmentIndex = null;

    /**
     * Пауза между запросами к API, миллисекунды.
     */
    private int $throttleMs = 0;

    /**
     * Время последнего запроса к API.
     */
    private float $lastRequestAt = 0.0;

    /**
     * Таймаут запроса, секунд.
     */
    private const TIMEOUT = 30;

    /**
     * Количество попыток при сетевых ошибках и ответе 429.
     */
    private const RETRIES = 3;

    /**
     * Получить список техкарт (processingplan).
     *
     * @param  int  $limit  максимум записей (обрезается до MAX_LIMIT)
     * @param  string|null  $search  серверный поиск по названию
     * @param  bool  $archived  включить архивные техкарты
     * @return array<int, array<string, mixed>>
     *
     * @throws RuntimeException
     */
    public function fetchPlans(int $limit = self::MAX_LIMIT, ?string $search = null, bool $archived = false): array
    {
        $limit = max(1, min($limit, self::MAX_LIMIT));
        $offset = 0;
        $plans = [];

        while (count($plans) < $limit) {
            $query = [
                'limit' => min($limit - count($plans), self::MAX_LIMIT),
                'offset' => $offset,
            ];

            if (! empty($search)) {
                $query['search'] = $search;
            }

            if ($archived) {
                $query['archived'] = 'true';
            }

            $response = $this->get(self::BASE_URL.'/entity/processingplan', $query);
            $data = $this->decode($response);

            $rows = $data['rows'] ?? [];

            if (empty($rows)) {
                break;
            }

            foreach ($rows as $row) {
                $plans[] = $this->normalizePlan($row);

                if (count($plans) >= $limit) {
                    break;
                }
            }

            $nextHref = $data['meta']['nextHref'] ?? null;

            if (empty($nextHref)) {
                break;
            }

            $offset = (int) ($data['meta']['offset'] ?? $offset) + count($rows);
        }

        return $plans;
    }

    /**
     * Получить справочник техпроцессов: id => name.
     *
     * @return array<string, string>
     */
    public function fetchProcesses(): array
    {
        $response = $this->get(self::BASE_URL.'/entity/processingprocess', ['limit' => self::MAX_LIMIT]);
        $data = $this->decode($response);

        $processes = [];

        foreach ($data['rows'] ?? [] as $row) {
            $id = $row['id'] ?? null;

            if ($id !== null) {
                $processes[$id] = $row['name'] ?? '';
            }
        }

        return $processes;
    }

    /**
     * Получить детали техкарты: материалы, этапы и результат (продукты).
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function fetchDetails(string $planId): array
    {
        return [
            'materials' => $this->fetchCollection(self::BASE_URL.'/entity/processingplan/'.$planId.'/materials'),
            'stages' => $this->fetchCollection(self::BASE_URL.'/entity/processingplan/'.$planId.'/stages'),
            'products' => $this->fetchCollection(self::BASE_URL.'/entity/processingplan/'.$planId.'/products'),
        ];
    }

    /**
     * Получить справочник номенклатуры: id => name.
     * Используется для расшифровки состава техкарты.
     *
     * @return array<string, string>
     */
    public function fetchAssortmentNames(): array
    {
        if ($this->assortmentNames !== null) {
            return $this->assortmentNames;
        }

        $names = [];

        foreach ($this->fetchAssortmentIndex() as $id => $item) {
            $names[$id] = $item['name'] ?? '';
        }

        return $this->assortmentNames = $names;
    }

    /**
     * Получить полный индекс номенклатуры: id => [name, code, externalCode].
     * Используется для связи техкарты с товаром магазина по code/externalCode.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fetchAssortmentIndex(): array
    {
        if ($this->assortmentIndex !== null) {
            return $this->assortmentIndex;
        }

        $index = [];
        $offset = 0;

        while (true) {
            $response = $this->get(self::BASE_URL.'/entity/assortment', [
                'limit' => self::MAX_LIMIT,
                'offset' => $offset,
            ]);

            $data = $this->decode($response);
            $rows = $data['rows'] ?? [];

            if (empty($rows)) {
                break;
            }

            foreach ($rows as $row) {
                $id = $row['id'] ?? null;

                if ($id !== null) {
                    $index[$id] = [
                        'name' => $row['name'] ?? '',
                        'code' => $row['code'] ?? null,
                        'externalCode' => $row['externalCode'] ?? null,
                        'archived' => (bool) ($row['archived'] ?? false),
                        'type' => $row['meta']['type'] ?? null,
                    ];
                }
            }

            if (empty($data['meta']['nextHref'] ?? null)) {
                break;
            }

            $offset = (int) ($data['meta']['offset'] ?? $offset) + count($rows);
        }

        return $this->assortmentIndex = $index;
    }

    /**
     * Получить результат техкарты (готовые продукты) в сыром виде.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchPlanProducts(string $planId): array
    {
        return $this->fetchCollection(self::BASE_URL.'/entity/processingplan/'.$planId.'/products');
    }

    /**
     * Получить материалы техкарты в сыром виде.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchPlanMaterials(string $planId): array
    {
        return $this->fetchCollection(self::BASE_URL.'/entity/processingplan/'.$planId.'/materials');
    }

    /**
     * Получить название сущности по прямой ссылке.
     * Используется как запасной вариант, когда номенклатуры нет в ассортименте
     * (например, архивная позиция).
     */
    public function fetchEntityName(string $href): ?string
    {
        try {
            $response = $this->get($href);
            $data = $this->decode($response);
        } catch (RuntimeException $e) {
            Log::channel('my_sklad')->warning('Не удалось получить сущность по ссылке', [
                'href' => $href,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        $name = $data['name'] ?? null;

        return is_string($name) ? $name : null;
    }

    /**
     * Забрать коллекцию (rows) с постраничной выдачей.
     */
    private function fetchCollection(string $url): array
    {
        $items = [];
        $offset = 0;

        do {
            $response = $this->get($url, ['limit' => self::MAX_LIMIT, 'offset' => $offset]);
            $data = $this->decode($response);

            $rows = $data['rows'] ?? [];

            if (empty($rows)) {
                break;
            }

            foreach ($rows as $row) {
                $items[] = $row;
            }

            $offset = (int) ($data['meta']['offset'] ?? $offset) + count($rows);

            if (empty($data['meta']['nextHref'] ?? null)) {
                break;
            }
        } while (count($items) < self::MAX_LIMIT);

        return $items;
    }

    /**
     * Привести строку техкарты к плоскому виду.
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizePlan(array $row): array
    {
        return [
            'id' => $row['id'] ?? null,
            'name' => $row['name'] ?? '',
            'externalCode' => $row['externalCode'] ?? null,
            'archived' => (bool) ($row['archived'] ?? false),
            'updated' => $row['updated'] ?? null,
            'cost' => (float) ($row['cost'] ?? 0),
            'costDistributionType' => $row['costDistributionType'] ?? null,
            'processId' => $this->extractId($row['processingProcess']['meta']['href'] ?? null),
            'stagesCount' => (int) ($row['stages']['meta']['size'] ?? 0),
            'materialsCount' => (int) ($row['materials']['meta']['size'] ?? 0),
            'parametricMaterialsCount' => (int) ($row['parametricMaterials']['meta']['size'] ?? 0),
            'productsCount' => (int) ($row['products']['meta']['size'] ?? 0),
            'uuidHref' => $row['meta']['uuidHref'] ?? null,
        ];
    }

    /**
     * Извлечь UUID из ссылки вида .../entity/processingplan/{uuid}.
     */
    public function extractId(?string $href): ?string
    {
        if (empty($href)) {
            return null;
        }

        if (preg_match('/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i', $href, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Выполнить GET-запрос с повторами при 429 и сетевых ошибках.
     *
     * @param  array<string, mixed>  $query
     *
     * @throws RuntimeException
     */
    private function get(string $url, array $query = []): Response
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            $this->waitForThrottle();

            try {
                $response = $this->client()->get($url, $query);
                $this->lastRequestAt = microtime(true);
            } catch (\Throwable $e) {
                if ($attempt >= self::RETRIES) {
                    Log::channel('my_sklad')->error('Сетевая ошибка при обращении к API МойСклад', [
                        'url' => $url,
                        'attempts' => $attempt,
                        'error' => $e->getMessage(),
                    ]);

                    throw new RuntimeException('Не удалось выполнить запрос к МойСклад: '.$e->getMessage(), 0, $e);
                }

                $this->sleep($attempt);

                continue;
            }

            if ($response->status() === 429 && $attempt < self::RETRIES) {
                Log::channel('my_sklad')->warning('Получен ответ 429 (лимит запросов), повторяем', [
                    'url' => $url,
                    'attempt' => $attempt,
                    'retry_after' => $response->header('Retry-After'),
                ]);

                $delay = (int) ($response->header('Retry-After') ?: 0);
                $this->sleep($attempt, $delay);

                continue;
            }

            if ($response->serverError() && $attempt < self::RETRIES) {
                Log::channel('my_sklad')->warning('Ошибка сервера МойСклад, повторяем', [
                    'url' => $url,
                    'status' => $response->status(),
                    'attempt' => $attempt,
                ]);

                $this->sleep($attempt);

                continue;
            }

            return $response;
        }
    }

    /**
     * HTTP-клиент с авторизацией и заголовками.
     */
    private function client(): PendingRequest
    {
        return Http::withBasicAuth(config('mysklad.login'), config('mysklad.password'))
            ->withHeaders([
                'Accept' => 'application/json;charset=utf-8',
                'Accept-Encoding' => 'gzip',
            ])
            ->timeout(self::TIMEOUT)
            ->connectTimeout(self::TIMEOUT);
    }

    /**
     * Разобрать ответ API.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    private function decode(Response $response): array
    {
        if (! $response->successful()) {
            $body = $response->body();

            Log::channel('my_sklad')->error('Ошибка при обращении к API МойСклад', [
                'status' => $response->status(),
                'body' => mb_substr($body, 0, 1000),
            ]);

            throw new RuntimeException('Ошибка API МойСклад ('.$response->status().'): '.mb_substr($body, 0, 500));
        }

        return $response->json() ?? [];
    }

    /**
     * Пауза перед повтором запроса.
     */
    private function sleep(int $attempt, int $delay = 0): void
    {
        $seconds = $delay > 0 ? $delay : min(2 ** $attempt, 10);

        usleep($seconds * 1000 * 1000);
    }

    /**
     * Установить паузу между запросами к API (лимит МойСклад — 1 запрос в секунду).
     */
    public function setThrottleMs(int $milliseconds): void
    {
        $this->throttleMs = max(0, $milliseconds);
    }

    /**
     * Выдержать паузу, если с прошлого запроса прошло меньше заданного времени.
     */
    private function waitForThrottle(): void
    {
        if ($this->throttleMs <= 0 || $this->lastRequestAt === 0.0) {
            return;
        }

        $elapsed = (microtime(true) - $this->lastRequestAt) * 1000;
        $wait = $this->throttleMs - $elapsed;

        if ($wait > 0) {
            usleep((int) ($wait * 1000));
        }
    }
}
