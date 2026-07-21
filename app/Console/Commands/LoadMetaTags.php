<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTag;
use Illuminate\Console\Command;

class LoadMetaTags extends Command
{
    protected $signature = 'meta:load {file? : Путь к CSV файлу} {--mode=categories : Режим загрузки (categories|products)}';

    protected $description = 'Загрузка мета тегов из CSV файла';

    public function handle()
    {
        $file = $this->argument('file') ?? public_path('loadet_data/new_meta_tags.csv');
        $mode = $this->option('mode');

        if (!in_array($mode, ['categories', 'products'])) {
            $this->error('Неверный режим. Используйте "categories" или "products"');
            return 1;
        }

        if (!file_exists($file)) {
            $this->error("Файл не найден: {$file}");
            return 1;
        }

        $this->info("Режим: {$mode}");
        $this->info("Файл: {$file}");
        $this->newLine();

        $rows = $this->readCsv($file);
        $header = array_shift($rows);

        $updated = 0;
        $notFound = 0;
        $skipped = 0;

        $this->info('Обработка данных...');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            if ($mode === 'categories') {
                if (empty($row[1])) {
                    $skipped++;
                    continue;
                }

                $url = $row[1];
                $model = $this->determineModelByUrl($url);

                if (!$model) {
                    $this->warn("Строка {$rowNumber}: Не удалось определить модель по URL {$url}");
                    $notFound++;
                    continue;
                }

                $slug = $this->extractSlug($url);

                if (empty($slug)) {
                    $this->warn("Строка {$rowNumber}: Не удалось извлечь slug из URL {$url}");
                    $notFound++;
                    continue;
                }

                $title = $row[3] ?? null;
                $seoTitle = $row[4] ?? null;
                $seoDescription = $row[5] ?? null;

                $result = $this->updateMeta($model, $slug, $title, $seoTitle, $seoDescription);

                if ($result) {
                    $updated++;
                    $this->line("Строка {$rowNumber}: Обновлен {$model} '{$slug}'");
                } else {
                    $notFound++;
                    $this->warn("Строка {$rowNumber}: {$model} не найден '{$slug}'");
                }
            } elseif ($mode === 'products') {
                if (empty($row[2])) {
                    $skipped++;
                    continue;
                }

                $url = $row[2];
                $slug = $this->extractSlug($url);

                if (empty($slug)) {
                    $this->warn("Строка {$rowNumber}: Не удалось извлечь slug из URL {$url}");
                    $notFound++;
                    continue;
                }

                $title = $row[3] ?? null;
                $seoTitle = $row[4] ?? null;
                $seoDescription = $row[5] ?? null;

                $result = $this->updateMeta('Product', $slug, $title, $seoTitle, $seoDescription);

                if ($result) {
                    $updated++;
                    $this->line("Строка {$rowNumber}: Обновлен Product '{$slug}'");
                } else {
                    $notFound++;
                    $this->warn("Строка {$rowNumber}: Product не найден '{$slug}'");
                }
            }
        }

        $this->newLine();
        $this->info('Отчет о выполнении:');
        $this->table(['Метрика', 'Значение'], [
            ['Обновлено', $updated],
            ['Не найдено', $notFound],
            ['Пропущено', $skipped],
            ['Всего обработано', $updated + $notFound + $skipped],
        ]);

        return 0;
    }

    private function readCsv($file)
    {
        $rows = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }
        return $rows;
    }

    private function determineModelByUrl($url)
    {
        if (str_contains($url, '/catalog/')) {
            return 'Category';
        } elseif (str_contains($url, '/tags/')) {
            return 'ProductTag';
        }
        return null;
    }

    private function extractSlug($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path === false) {
            return null;
        }

        $segments = explode('/', trim($path, '/'));
        return end($segments);
    }

    private function updateMeta($modelName, $slug, $title, $seoTitle, $seoDescription)
    {
        $model = match ($modelName) {
            'Category' => Category::where('slug', $slug)->first(),
            'ProductTag' => ProductTag::where('slug', $slug)->first(),
            'Product' => Product::where('slug', $slug)->first(),
            default => null
        };

        if (!$model) {
            return false;
        }

        if ($title !== null) {
            $model->title = $title;
        }

        if ($seoTitle !== null) {
            $model->seo_title = $seoTitle;
        }

        if ($seoDescription !== null) {
            $model->seo_description = $seoDescription;
        }

        return $model->save();
    }
}