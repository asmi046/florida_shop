<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;

class ProcessingPlanFieldTest extends TestCase
{
    private ?User $admin = null;

    private ?Product $product = null;

    protected function tearDown(): void
    {
        if ($this->admin !== null) {
            $this->admin->delete();
            $this->admin = null;
        }

        if ($this->product !== null) {
            Product::where('id', $this->product->id)->update(['processingPlanId' => null]);
            $this->product = null;
        }

        parent::tearDown();
    }

    public function test_edit_screen_contains_processing_plan_field(): void
    {
        $this->admin = $this->makeAdmin();

        $product = Product::firstOrFail();
        $this->product = $product;

        $response = $this->actingAs($this->admin)->get('/admin/products/'.$product->id.'/edit');

        $response->assertOk();
        $response->assertSee('processingPlanId', false);
        $response->assertSee('Техкарта МойСклад');
    }

    public function test_create_screen_contains_processing_plan_field(): void
    {
        $this->admin = $this->makeAdmin();

        $response = $this->actingAs($this->admin)->get('/admin/products/create');

        $response->assertOk();
        $response->assertSee('processingPlanId', false);
        $response->assertSee('Техкарта МойСклад');
    }

    public function test_save_info_persists_processing_plan_id(): void
    {
        $this->admin = $this->makeAdmin();

        $product = Product::firstOrFail();
        $this->product = $product;
        $planId = '001ebe48-cbe6-11f1-0a80-139f0000aca5';

        $page = $this->actingAs($this->admin)->get('/admin/products/'.$product->id.'/edit');
        $page->assertOk();

        preg_match('/name="_state"[^>]*value="([^"]+)"/', $page->getContent(), $matches);
        $this->assertNotEmpty($matches, 'Не найдено поле состояния экрана');

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->actingAs($this->admin)
            ->post('/admin/products/'.$product->id.'/edit/save_info', [
                '_state' => html_entity_decode($matches[1]),
                'sku' => $product->sku,
                'title' => $product->title,
                'price' => $product->price,
                'processingPlanId' => $planId,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'processingPlanId' => $planId,
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
                'platform.screens.product' => true,
            ],
        ]);
    }
}
