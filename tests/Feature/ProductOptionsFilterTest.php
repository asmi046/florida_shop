<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionType;
use App\Models\ProductTag;
use App\Models\User;
use App\Support\ProductOptionCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductOptionsFilterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Цена тестовых товаров подобрана так, чтобы их можно было отсечь
     * от данных, которые уже лежат в базе.
     */
    protected const TEST_PRICE = 777777;

    protected int $counter = 0;

    public function test_filter_by_single_dimension_matches_any_selected_value(): void
    {
        $red = $this->option('color', 'Красный');
        $white = $this->option('color', 'Белый');

        $redProduct = $this->makeProduct();
        $redProduct->productOptions()->syncWithoutDetaching($red->id);

        $whiteProduct = $this->makeProduct();
        $whiteProduct->productOptions()->syncWithoutDetaching($white->id);

        $plainProduct = $this->makeProduct();

        $ids = $this->catalogIds(['color' => [$red->id, $white->id]]);

        $this->assertContains($redProduct->id, $ids);
        $this->assertContains($whiteProduct->id, $ids);
        $this->assertNotContains($plainProduct->id, $ids);
    }

    public function test_filter_by_two_dimensions_requires_both(): void
    {
        $red = $this->option('color', 'Красный');
        $box = $this->option('packing', 'Коробка');

        $bothProduct = $this->makeProduct();
        $bothProduct->productOptions()->syncWithoutDetaching([$red->id, $box->id]);

        $onlyColorProduct = $this->makeProduct();
        $onlyColorProduct->productOptions()->syncWithoutDetaching($red->id);

        $ids = $this->catalogIds([
            'color' => [$red->id],
            'packing' => [$box->id],
        ]);

        $this->assertContains($bothProduct->id, $ids);
        $this->assertNotContains($onlyColorProduct->id, $ids);
    }

    public function test_filter_is_applied_on_category_page(): void
    {
        $category = $this->makeCategory();
        $red = $this->option('color', 'Красный');

        $matched = $this->makeProduct();
        $matched->tovar_categories()->attach($category->id);
        $matched->productOptions()->syncWithoutDetaching($red->id);

        $notMatched = $this->makeProduct();
        $notMatched->tovar_categories()->attach($category->id);

        $ids = $this->categoryIds($category->slug, ['color' => [$red->id]]);

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($notMatched->id, $ids);
    }

    public function test_filter_is_applied_on_price_page(): void
    {
        $red = $this->option('color', 'Красный');

        $matched = $this->makeProduct(['price' => 2410.35]);
        $matched->productOptions()->syncWithoutDetaching($red->id);

        $notMatched = $this->makeProduct(['price' => 2411.65]);

        $response = $this->get('/prices/do-2500?minprice=2400&color[]='.$red->id);

        $response->assertOk();

        $ids = collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($notMatched->id, $ids);
    }

    public function test_filter_is_applied_on_tag_page(): void
    {
        $tag = ProductTag::create([
            'title' => 'Тестовый тег '.uniqid(),
            'slug' => 'test-tag-'.uniqid(),
            'alt_title' => 'Тестовый тег',
        ]);

        $red = $this->option('color', 'Красный');

        $matched = $this->makeProduct();
        $matched->tags()->attach($tag->id);
        $matched->productOptions()->syncWithoutDetaching($red->id);

        $notMatched = $this->makeProduct();
        $notMatched->tags()->attach($tag->id);

        $response = $this->get('/tags/'.$tag->slug.'?minprice='.(self::TEST_PRICE - 100).'&color[]='.$red->id);

        $response->assertOk();

        $ids = collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();

        $this->assertContains($matched->id, $ids);
        $this->assertNotContains($notMatched->id, $ids);
    }

    public function test_filter_ignores_unknown_and_malformed_values(): void
    {
        $red = $this->option('color', 'Красный');

        $product = $this->makeProduct();
        $product->productOptions()->syncWithoutDetaching($red->id);

        $response = $this->get('/catalog?color[]='.$red->id.'&color[]=999999&color[]=abc');

        $response->assertOk();

        $ids = collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();

        $this->assertContains($product->id, $ids);
    }

    public function test_filter_form_marks_selected_values(): void
    {
        $red = $this->option('color', 'Красный');

        $response = $this->get('/catalog?color[]='.$red->id);

        $response->assertOk();
        $response->assertSee('name="color[]" value="'.$red->id.'"', false);
    }

    public function test_product_edit_screen_contains_option_fields(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit');

        $response->assertOk();

        foreach (['color', 'packing', 'audience', 'event', 'composition'] as $dimension) {
            $response->assertSee('name="'.$dimension.'[]"', false);
        }
    }

    public function test_save_info_persists_selected_option_values(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::firstOrFail();

        $red = $this->option('color', 'Красный');
        $box = $this->option('packing', 'Коробка');

        $page = $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit');
        $page->assertOk();

        preg_match('/name="_state"[^>]*value="([^"]+)"/', $page->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Не найдено поле состояния экрана');

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->actingAs($admin)
            ->post('/admin/products/'.$product->id.'/edit/save_info', [
                '_state' => html_entity_decode($matches[1]),
                'sku' => $product->sku,
                'title' => $product->title,
                'price' => $product->price,
                'product_options_present' => 1,
                'color' => [$red->id],
                'packing' => [$box->id],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('product_product_option', [
            'product_id' => $product->id,
            'product_option_id' => $red->id,
        ]);

        $this->assertDatabaseHas('product_product_option', [
            'product_id' => $product->id,
            'product_option_id' => $box->id,
        ]);
    }

    public function test_save_info_detects_removed_option_values(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::firstOrFail();

        $red = $this->option('color', 'Красный');

        $product->productOptions()->syncWithoutDetaching($red->id);

        $page = $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit');
        $page->assertOk();

        preg_match('/name="_state"[^>]*value="([^"]+)"/', $page->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Не найдено поле состояния экрана');

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->actingAs($admin)
            ->post('/admin/products/'.$product->id.'/edit/save_info', [
                '_state' => html_entity_decode($matches[1]),
                'sku' => $product->sku,
                'title' => $product->title,
                'price' => $product->price,
                'product_options_present' => 1,
                'color' => [],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('product_product_option', [
            'product_id' => $product->id,
            'product_option_id' => $red->id,
        ]);
    }

    public function test_save_info_keeps_option_values_when_form_has_no_option_block(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::firstOrFail();

        $red = $this->option('color', 'Красный');
        $product->productOptions()->syncWithoutDetaching([$red->id]);

        $page = $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit');
        $page->assertOk();

        preg_match('/name="_state"[^>]*value="([^"]+)"/', $page->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Не найдено поле состояния экрана');

        // Сохранение формы без блока параметров не должно стирать параметры товара.
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->actingAs($admin)
            ->post('/admin/products/'.$product->id.'/edit/save_info', [
                '_state' => html_entity_decode($matches[1]),
                'sku' => $product->sku,
                'title' => $product->title,
                'price' => $product->price,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('product_product_option', [
            'product_id' => $product->id,
            'product_option_id' => $red->id,
        ]);
    }

    public function test_filter_shows_color_swatch_before_name(): void
    {
        $red = $this->option('color', 'Красный');
        $red->update(['meta' => ['hex' => '#D32F2F']]);

        ProductOptionCatalog::flush();

        $response = $this->get('/catalog?color[]='.$red->id);

        $response->assertOk();
        $response->assertSee(
            '<span><i class="color_swatch" style="background: #D32F2F"></i>Красный</span>',
            false
        );
    }

    public function test_filter_blocks_are_rendered_in_expected_order(): void
    {
        $response = $this->get('/catalog');

        $response->assertOk();

        preg_match_all('/<div class="acc_head open">\s*<span>([^<]+)<\/span>/', $response->getContent(), $matches);

        $titles = array_map('trim', $matches[1]);

        $expected = ['Стоимость', 'Состав', 'Упаковка', 'Цвет', 'Кому', 'Событие'];

        $this->assertSame($expected, array_slice($titles, 0, count($expected)));
    }

    public function test_admin_menu_contains_parameters_section(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin/product');

        $response->assertOk();
        $response->assertSee('Параметры товаров');
    }

    public function test_product_screen_distinguishes_composition_sources(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit');

        $response->assertOk();
        $response->assertSee('Состав для фильтров');
        $response->assertSee('Состав букета для страницы');
    }

    public function test_product_screen_marks_parameter_fields_as_filter_fields(): void
    {
        $admin = $this->makeAdmin();
        $product = Product::firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/products/'.$product->id.'/edit');

        $response->assertOk();

        foreach (['Состав', 'Упаковка', 'Цвет', 'Кому', 'Событие'] as $title) {
            $response->assertSee($title.' для фильтров');
        }
    }

    public function test_client_filter_keeps_short_composition_title(): void
    {
        $response = $this->get('/catalog');

        $response->assertOk();
        $response->assertSee('<span>Состав</span>', false);
        $response->assertDontSee('Состав для фильтров', false);
    }

    public function test_option_list_screen_is_available(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin/product-options');

        $response->assertOk();
        $response->assertSee('Параметры товаров');
    }

    /**
     * Значение параметра из справочника.
     */
    protected function option(string $typeSlug, string $title): ProductOption
    {
        $type = ProductOptionType::firstOrCreate(
            ['slug' => $typeSlug],
            ['title' => $typeSlug, 'position' => 1, 'show_in_filter' => true]
        );

        return ProductOption::firstOrCreate(
            ['product_option_type_id' => $type->id, 'slug' => Str::slug($title)],
            ['title' => $title, 'position' => 1, 'is_active' => true]
        );
    }

    protected function makeProduct(array $attributes = []): Product
    {
        $this->counter++;

        return Product::create(array_merge([
            'sku' => 'TEST-OPT-'.uniqid().'-'.$this->counter,
            'title' => 'Тестовый товар '.$this->counter,
            'slug' => 'test-opt-'.uniqid().'-'.$this->counter,
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

    protected function catalogIds(array $query = []): array
    {
        $query['minprice'] = $query['minprice'] ?? self::TEST_PRICE - 100;

        $response = $this->get('/catalog?'.http_build_query($query));

        $response->assertOk();

        return collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();
    }

    protected function categoryIds(string $slug, array $query = []): array
    {
        $query['minprice'] = $query['minprice'] ?? self::TEST_PRICE - 100;

        $response = $this->get('/catalog/'.$slug.'?'.http_build_query($query));

        $response->assertOk();

        return collect($response->viewData('allproduct')->items())
            ->map(fn (Product $product) => (int) $product->id)
            ->all();
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Temp Admin Check',
            'email' => 'temp_admin_'.uniqid().'@example.test',
            'password' => bcrypt('temp-password'),
            'permissions' => [
                'platform.index' => true,
                'platform.screens.product' => true,
                'platform.screens.product-options' => true,
            ],
        ]);
    }
}
