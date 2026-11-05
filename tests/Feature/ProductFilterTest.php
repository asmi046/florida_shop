<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Цена тестовых товаров подобрана так, чтобы их можно было отсечь
     * от данных, которые уже лежат в базе.
     */
    protected const TEST_PRICE = 777777;

    protected int $counter = 0;

    public function test_unknown_query_parameters_do_not_break_the_catalog(): void
    {
        $queries = [
            'apply=1',
            'filters=1',
            'paramToArray=1',
            'brand=Whatever',
            'subcat[]=1',
            'foo=bar',
        ];

        foreach ($queries as $query) {
            $this->get('/catalog?'.$query)->assertOk();
        }
    }

    public function test_empty_filter_values_do_not_break_the_catalog(): void
    {
        $queries = [
            'osobennosti[]=',
            'osobennosti[]=&osobennosti[]=',
            'minprice=',
            'maxprice=',
            'order=',
        ];

        foreach ($queries as $query) {
            $this->get('/catalog?'.$query)->assertOk();
        }
    }

    public function test_osobennosti_combines_selected_values_with_or(): void
    {
        $new = $this->makeProduct(['new' => true]);
        $hit = $this->makeProduct(['hit' => true]);
        $plain = $this->makeProduct();

        $ids = $this->catalogProductIds([
            'minprice' => self::TEST_PRICE - 100,
            'osobennosti' => ['Новинки', 'Хит продаж'],
        ]);

        $this->assertContains($new->id, $ids);
        $this->assertContains($hit->id, $ids);
        $this->assertNotContains($plain->id, $ids);
    }

    public function test_osobennosti_returns_products_with_any_selected_feature(): void
    {
        $discount = $this->makeProduct(['old_price' => 100]);
        $plain = $this->makeProduct();

        $ids = $this->catalogProductIds([
            'minprice' => self::TEST_PRICE - 100,
            'osobennosti' => ['Скидка'],
        ]);

        $this->assertContains($discount->id, $ids);
        $this->assertNotContains($plain->id, $ids);
    }

    public function test_filter_is_applied_on_category_page(): void
    {
        $category = $this->makeCategory();

        $cheap = $this->makeProduct(['price' => self::TEST_PRICE]);
        $expensive = $this->makeProduct(['price' => self::TEST_PRICE + 1000]);

        $cheap->tovar_categories()->attach($category->id);
        $expensive->tovar_categories()->attach($category->id);

        $allIds = $this->categoryProductIds($category->slug);

        $this->assertContains($cheap->id, $allIds);
        $this->assertContains($expensive->id, $allIds);

        $filteredIds = $this->categoryProductIds($category->slug, ['minprice' => self::TEST_PRICE + 500]);

        $this->assertContains($expensive->id, $filteredIds);
        $this->assertNotContains($cheap->id, $filteredIds);
    }

    public function test_minprice_zero_does_not_hide_all_products(): void
    {
        $response = $this->get('/catalog?minprice=0');

        $response->assertOk();

        $this->assertGreaterThan(0, $response->viewData('allproduct')->total());
    }

    public function test_price_page_keeps_default_ordering_when_order_parameter_is_empty(): void
    {
        // Товары создаются в обратном порядке, чтобы отличить сортировку
        // по цене от сортировки по порядку добавления.
        $expensive = $this->makeProduct(['price' => 2411.37]);
        $cheap = $this->makeProduct(['price' => 2410.73]);

        $response = $this->get('/prices/do-2500?minprice=2400&order=');

        $response->assertOk();

        $prices = collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (float) $product->price)
            ->all();

        $cheapPosition = array_search((float) $cheap->price, $prices, true);
        $expensivePosition = array_search((float) $expensive->price, $prices, true);

        $this->assertNotFalse($cheapPosition);
        $this->assertNotFalse($expensivePosition);
        $this->assertLessThan($expensivePosition, $cheapPosition);
    }

    protected function makeProduct(array $attributes = []): Product
    {
        $this->counter++;

        return Product::create(array_merge([
            'sku' => 'TEST-FILTER-'.uniqid().'-'.$this->counter,
            'title' => 'Тестовый товар '.$this->counter,
            'slug' => 'test-filter-'.uniqid().'-'.$this->counter,
            'price' => self::TEST_PRICE,
            'old_price' => 0,
            'hit' => false,
            'new' => false,
        ], $attributes));
    }

    protected function makeCategory(): Category
    {
        return Category::create([
            'title' => 'Тестовая категория '.uniqid(),
            'slug' => 'test-category-'.uniqid(),
        ]);
    }

    protected function catalogProductIds(array $query = []): array
    {
        $response = $this->get('/catalog?'.http_build_query($query));

        $response->assertOk();

        return collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();
    }

    protected function categoryProductIds(string $slug, array $query = []): array
    {
        $response = $this->get('/catalog/'.$slug.'?'.http_build_query($query));

        $response->assertOk();

        return collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();
    }
}
