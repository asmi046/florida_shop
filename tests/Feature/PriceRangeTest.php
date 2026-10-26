<?php

namespace Tests\Feature;

use App\Models\ProductTag;
use Tests\TestCase;

class PriceRangeTest extends TestCase
{
    public function test_unknown_price_range_returns_404()
    {
        $this->get('/prices/unknown-range')->assertNotFound();
    }

    public function test_price_range_page_is_rendered()
    {
        foreach (array_keys(config('prices')) as $slug) {
            $this->get('/prices/'.$slug)->assertOk();
        }
    }

    public function test_only_products_of_the_range_are_shown()
    {
        $response = $this->get('/prices/do-2500');
        $response->assertOk();

        $products = $response->viewData('allproduct')->items();

        foreach ($products as $product) {
            $this->assertLessThan(2500, $product->price);
        }
    }

    public function test_range_without_upper_border_keeps_all_expensive_products()
    {
        $response = $this->get('/prices/ot-20000');
        $response->assertOk();

        $products = $response->viewData('allproduct')->items();

        foreach ($products as $product) {
            $this->assertGreaterThanOrEqual(20000, $product->price);
        }
    }

    public function test_filter_narrows_products_inside_the_range()
    {
        $response = $this->get('/prices/ot-2500-do-5000?minprice=3000');
        $response->assertOk();

        $products = $response->viewData('allproduct')->items();

        foreach ($products as $product) {
            $this->assertGreaterThanOrEqual(3000, $product->price);
            $this->assertLessThan(5000, $product->price);
        }
    }

    public function test_price_selector_is_filled_with_current_range_borders()
    {
        $response = $this->get('/prices/ot-10000-do-15000');
        $response->assertOk();

        $response->assertSee('name="minprice" value="10000"', false);
        $response->assertSee('name="maxprice" value="15000"', false);
    }

    public function test_range_switcher_is_placed_in_price_selector_block()
    {
        $response = $this->get('/prices/ot-20000');
        $response->assertOk();

        $content = $response->getContent();

        $this->assertStringContainsString('price_selector_wrapper', $content);
        $this->assertStringContainsString('class="tag_lnk"', $content);
        $this->assertTrue(
            strpos($content, 'price_selector_wrapper') < strpos($content, route('price', 'do-2500'))
        );
    }

    public function test_range_switcher_is_shown_on_catalog_page()
    {
        $response = $this->get('/catalog');
        $response->assertOk();

        foreach (array_keys(config('prices')) as $slug) {
            $response->assertSee(route('price', $slug), false);
        }
    }

    public function test_range_switcher_is_shown_on_tag_page()
    {
        $tag = ProductTag::first();

        if ($tag === null) {
            $this->markTestSkipped('Нет тегов в базе');
        }

        $response = $this->get('/tags/'.$tag->slug);
        $response->assertOk();

        $response->assertSee(route('price', 'ot-20000'), false);
    }
}
