<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MatchProductsWithMoySklad extends Command
{
    protected $signature = 'moysklad:match-products
                            {--file=public/moysklad_assortment.json : Путь к JSON-файлу ассортимента}
                            {--threshold=0.80 : Минимальный порог совпадения для авто-связывания}
                            {--dry-run : Только показать результат без записи в БД}
                            {--overwrite : Обновлять даже уже заполненные code/externalCode}';

    protected $description = 'Сопоставление товаров Product с ассортиментом МойСклад по нечёткому совпадению названий';

    const SESSION_TYPE = 'match_products';

    public function handle(): int
    {
        $file = $this->option('file');
        $threshold = (float) $this->option('threshold');
        $dryRun = (bool) $this->option('dry-run');
        $overwrite = (bool) $this->option('overwrite');

        $startTime = now();

        Log::channel('my_sklad')->info('Начало сопоставления товаров с МойСклад', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'threshold' => $threshold,
            'dry_run' => $dryRun,
            'overwrite' => $overwrite,
        ]);

        $fullPath = base_path($file);
        if (! File::exists($fullPath)) {
            $this->error("Файл не найден: {$fullPath}");
            return 1;
        }

        $this->info('1. Загрузка данных из JSON...');
        $data = json_decode(File::get($fullPath), true);
        $rows = $data['rows'] ?? [];

        if (empty($rows)) {
            $this->warn('В файле нет записей rows.');
            return 0;
        }

        // Индекс: нормализованное имя + метаданные строки
        $moyskladIndex = [];
        foreach ($rows as $row) {
            if (empty($row['name'])) {
                continue;
            }
            $moyskladIndex[] = [
                'name' => $row['name'],
                'code' => $row['code'] ?? null,
                'externalCode' => $row['externalCode'] ?? null,
            ];
        }

        $this->info("   Загружено записей из МойСклад: " . count($moyskladIndex));

        // 2. Товары для сопоставления
        $query = Product::query();
        if (! $overwrite) {
            $query->where(function ($q) {
                $q->whereNull('code')
                  ->orWhere('code', '')
                  ->orWhereNull('externalCode')
                  ->orWhere('externalCode', '');
            });
        }
        $products = $query->get(['id', 'title', 'code', 'externalCode']);

        if ($products->isEmpty()) {
            $this->warn('Нет товаров, подходящих для сопоставления.');
            return 0;
        }

        $this->info("2. Товаров для сопоставления: {$products->count()}");
        $this->info("   Порог совпадения: {$threshold}" . ($dryRun ? '   [DRY-RUN]' : ''));
        $this->newLine();

        $matched = 0;
        $unmatched = 0;
        $lowConfidence = 0;
        $report = [];

        foreach ($products as $product) {
            $best = $this->findBestMatch($product->title, $moyskladIndex);

            if ($best === null || $best['score'] < $threshold) {
                $unmatched++;
                if ($best !== null) {
                    $lowConfidence++;
                    $report[] = [
                        $product->id,
                        $this->truncate($product->title, 40),
                        $this->truncate($best['name'], 40),
                        number_format($best['score'], 3) . ' ✗',
                        '-',
                    ];
                }
                continue;
            }

            // Проходит порог — обновляем
            $matched++;
            $report[] = [
                $product->id,
                $this->truncate($product->title, 40),
                $this->truncate($best['name'], 40),
                number_format($best['score'], 3) . ' ✓',
                $best['code'] ?? '-',
            ];

            if (! $dryRun) {
                $product->update([
                    'code' => $best['code'],
                    'externalCode' => $best['externalCode'],
                ]);

                Log::channel('my_sklad')->info('Товар сопоставлен', [
                    'session_type' => self::SESSION_TYPE,
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'moysklad_name' => $best['name'],
                    'score' => $best['score'],
                    'code' => $best['code'],
                    'externalCode' => $best['externalCode'],
                ]);
            }
        }

        // 3. Отчёт
        $this->table(
            ['ID', 'Product.title', 'МойСklad name', 'Score', 'code'],
            $report
        );
        $this->newLine();

        $endTime = now();
        $this->info('Сопоставление завершено.');
        $this->table(['Метрика', 'Значение'], [
            ['Всего товаров', $products->count()],
            ['Сопоставлено (>= порога)', $matched],
            ['Не сопоставлено', $unmatched],
            ['  из них с низкой уверенностью', $lowConfidence],
            ['Время выполнения', $startTime->diffForHumans($endTime, true)],
        ]);

        Log::channel('my_sklad')->info('Завершение сопоставления товаров', [
            'session_type' => self::SESSION_TYPE,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'total_products' => $products->count(),
            'matched' => $matched,
            'unmatched' => $unmatched,
            'low_confidence' => $lowConfidence,
            'duration_seconds' => $startTime->diffInSeconds($endTime),
        ]);

        return 0;
    }

    /**
     * Обрезка строки до указанной длины с многобайтовой безопасностью.
     */
    private function truncate(string $s, int $max): string
    {
        if (mb_strlen($s, 'UTF-8') <= $max) {
            return $s;
        }
        return mb_substr($s, 0, $max - 1, 'UTF-8') . '…';
    }

    /**
     * Найти наилучшее совпадение названия товара среди записей МойСклад.
     *
     * @param string $productTitle
     * @param array $moyskladIndex
     * @return array|null  ['name', 'code', 'externalCode', 'score']
     */
    private function findBestMatch(string $productTitle, array $moyskladIndex): ?array
    {
        $best = null;
        $bestScore = 0.0;

        foreach ($moyskladIndex as $row) {
            $score = $this->scoreForShop($productTitle, $row['name']);

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = [
                    'name' => $row['name'],
                    'code' => $row['code'],
                    'externalCode' => $row['externalCode'],
                    'score' => $score,
                ];
            }
        }

        return $best;
    }

    /**
     * Комбинированный коэффициент сходства для домена цветочного магазина.
     * 0.6 × Jaro-Winkler + 0.4 × косинус по словам,
     * с извлечением главного имени до скобок и нормализацией опечаток.
     */
    private function scoreForShop(string $productTitle, string $moyskladName): float
    {
        $s1 = $this->baseNormalize($this->normalizeTypos($this->extractMainName($productTitle)));
        $s2 = $this->baseNormalize($this->normalizeTypos($this->extractMainName($moyskladName)));

        if ($s1 === '' || $s2 === '') {
            return 0.0;
        }

        $jw = $this->jaroWinkler($s1, $s2);
        $cos = $this->cosineWords($s1, $s2);

        return $jw * 0.6 + $cos * 0.4;
    }

    /**
     * Берём только часть названия до первой скобки.
     * «Пиончики (букет из пионов)» → «Пиончики»
     */
    private function extractMainName(string $s): string
    {
        $parts = preg_split('/\s*[(\[«]/u', $s, 2);
        $main = trim($parts[0]);
        return $main !== '' ? $main : $s;
    }

    /**
     * Унификация частых опечаток и вариантов написания.
     */
    private function normalizeTypos(string $s): string
    {
        $s = preg_replace('/моно\s?букет|могобукет|моно-букет/iu', 'монобукет', $s);
        $s = preg_replace('/куствоой/iu', 'кустовой', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return $s;
    }

    /**
     * Базовая нормализация: нижний регистр, удаление пунктуации, лишних пробелов.
     */
    private function baseNormalize(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/[.,;:!?"\'(){}\[\]\/\\–—«»]/u', ' ', $s);
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    /**
     * Jaro-Winkler similarity (0..1) с UTF-8 безопасностью.
     */
    private function jaroWinkler(string $s1, string $s2): float
    {
        $chars1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
        $chars2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);

        $len1 = count($chars1);
        $len2 = count($chars2);

        if ($len1 === 0 && $len2 === 0) return 1.0;
        if ($len1 === 0 || $len2 === 0) return 0.0;

        $matchDistance = (int) floor(max($len1, $len2) / 2) - 1;
        if ($matchDistance < 0) $matchDistance = 0;

        $matched1 = array_fill(0, $len1, false);
        $matched2 = array_fill(0, $len2, false);

        $matches = 0;
        for ($i = 0; $i < $len1; $i++) {
            $start = max(0, $i - $matchDistance);
            $end = min($i + $matchDistance + 1, $len2);

            for ($j = $start; $j < $end; $j++) {
                if ($matched2[$j] || $chars1[$i] !== $chars2[$j]) {
                    continue;
                }
                $matched1[$i] = true;
                $matched2[$j] = true;
                $matches++;
                break;
            }
        }

        if ($matches === 0) return 0.0;

        $transpositions = 0;
        $k = 0;
        for ($i = 0; $i < $len1; $i++) {
            if (! $matched1[$i]) continue;
            while (! $matched2[$k]) $k++;
            if ($chars1[$i] !== $chars2[$k]) $transpositions++;
            $k++;
        }

        $jaro = (($matches / $len1)
                + ($matches / $len2)
                + (($matches - $transpositions / 2) / $matches)) / 3;

        // Winkler-модификация: бонус за общий префикс (до 4 символов)
        $prefix = 0;
        $limit = min(4, $len1, $len2);
        for ($i = 0; $i < $limit; $i++) {
            if ($chars1[$i] === $chars2[$i]) {
                $prefix++;
            } else {
                break;
            }
        }

        return $jaro + ($prefix * 0.1 * (1 - $jaro));
    }

    /**
     * Косинусное сходство по словам (bag of words), 0..1.
     */
    private function cosineWords(string $s1, string $s2): float
    {
        $words1 = array_count_values(preg_split('/\s+/u', $s1, -1, PREG_SPLIT_NO_EMPTY) ?: []);
        $words2 = array_count_values(preg_split('/\s+/u', $s2, -1, PREG_SPLIT_NO_EMPTY) ?: []);

        $all = array_unique(array_merge(array_keys($words1), array_keys($words2)));

        $dot = 0.0;
        $norm1 = 0.0;
        $norm2 = 0.0;

        foreach ($all as $w) {
            $c1 = $words1[$w] ?? 0;
            $c2 = $words2[$w] ?? 0;
            $dot += $c1 * $c2;
            $norm1 += $c1 * $c1;
            $norm2 += $c2 * $c2;
        }

        $norm1 = sqrt($norm1);
        $norm2 = sqrt($norm2);

        if ($norm1 === 0.0 || $norm2 === 0.0) return 0.0;

        return $dot / ($norm1 * $norm2);
    }
}
