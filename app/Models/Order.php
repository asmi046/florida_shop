<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'adress',
        'comment',
        'session_id',
        'user_id',
    ];

    public static function update_order_status($orderId, $payId , $orderStatus, $orderStatusText) {
        $element = self::where(["id" => $orderId])->first();
        $element->pay_order = $payId;
        $element->pay_status = $orderStatus;
        $element->pay_status_text = $orderStatusText;
        $element->save();
    }

    public function orderProducts() {
        return $this->belongsToMany(Product::class);
    }
}
