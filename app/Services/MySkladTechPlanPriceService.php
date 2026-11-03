<?php

namespace App\Services;

use App\Models\Product;

class MySkladTechPlanPriceService
{
    /**
     * Применить коэффициент наценки к цене МойСклад.
     */
    public function calculate(float $msPrice, float $markup = 1.0): float
    {
        return round($msPrice * $markup, 2);
    }

    /**
     * Записать новую цену в товар.
     */
    public function apply(Product $product, float $price, bool $dryRun, bool $moveToOldPrice = false): void
    {
        if ($dryRun) {
            return;
        }

        if ($moveToOldPrice && (float) $product->price > 0 && (float) $product->price !== $price) {
            $product->old_price = $product->price;
        }

        $product->price = $price;
        $product->save();
    }
}
