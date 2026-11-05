<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTagAdminTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Тест удаляет теги, а список для фильтра кэшируется на час: после
     * отката транзакции кэш нужно сбросить, иначе следующий запрос
     * получит пустой список.
     */
    protected function tearDown(): void
    {
        ProductTag::clearFilterCache();

        parent::tearDown();
    }

    public function test_tag_edit_screen_contains_products_field(): void
    {
        $admin = $this->makeAdmin();
        $tag = $this->makeTag();

        $response = $this->actingAs($admin)->get('/admin/product-tags/'.$tag->id.'/edit');

        $response->assertOk();
        $response->assertSee('name="products[]"', false);
    }

    public function test_save_persists_products_of_tag(): void
    {
        $admin = $this->makeAdmin();
        $tag = $this->makeTag();
        $product = Product::firstOrFail();

        $page = $this->actingAs($admin)->get('/admin/product-tags/'.$tag->id.'/edit');
        $page->assertOk();

        preg_match('/name="_state"[^>]*value="([^"]+)"/', $page->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Не найдено поле состояния экрана');

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->actingAs($admin)
            ->post('/admin/product-tags/'.$tag->id.'/edit/save', [
                '_state' => html_entity_decode($matches[1]),
                'tag' => [
                    'title' => $tag->title,
                    'alt_title' => $tag->alt_title,
                    'slug' => $tag->slug,
                ],
                'products' => [$product->id],
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('product_product_tag', [
            'product_tag_id' => $tag->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_empty_tag_list_is_not_rendered_in_filter(): void
    {
        ProductTag::query()->delete();
        ProductTag::clearFilterCache();

        $response = $this->get('/catalog');

        $response->assertOk();
        $response->assertDontSee('<span>Цветы</span>', false);
    }

    protected function makeTag(): ProductTag
    {
        return ProductTag::create([
            'title' => 'Тестовый тег '.uniqid(),
            'alt_title' => 'Тестовый тег',
            'slug' => 'test-tag-'.uniqid(),
        ]);
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Temp Admin Check',
            'email' => 'temp_admin_'.uniqid().'@example.test',
            'password' => bcrypt('temp-password'),
            'permissions' => [
                'platform.index' => true,
                'platform.screens.product-tag' => true,
            ],
        ]);
    }
}
