<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'session_id',
        'user_id',
        'product_sku',
    ];


    public function tovar_data() {
        return $this->hasOne(Product::class, 'sku', 'product_sku');
    }

    public static function add($product_id) {
        
        if ($favorit = self::where(["session_id" => session()->getId(), "product_sku" => $product_id])->first()) {
            $favorit->delete();
            return false;
        } else {
            $favorit = self::create([
                "session_id" => session()->getId(),
                "user_id" => 0,
                "product_sku" => $product_id,
            ]);
            return true;
        }
    }

    public static function delete_favorite($product_id) {
        $element = self::where(["session_id" => session()->getId(), "product_sku" => $product_id])->first();
        $element->delete();
    }

    public static function favorites_clear() {
        return self::where("session_id", session()->getId())->delete();
    }
}
