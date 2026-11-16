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
        'amount',
        'raion',
        'delivery_price',
        'adress',
        'polname',
        'polphone',
        'delivery',
        'podezd',
        'etazg',
        'kvartira',
        'data',
        'time',
        'comment',
        'session_id',
        'user_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_referrer',
        'confirm_address_with_recipient',
        'recipient_type',
        'anonymous_delivery',
        'deliver_asap',
        'agree_delivery_time_with_recipient',
        'delivery_type',
    ];

    public $with = [
        'orderProducts'
    ];

    protected $casts = [
        'confirm_address_with_recipient' => 'boolean',
        'anonymous_delivery' => 'boolean',
        'deliver_asap' => 'boolean',
        'agree_delivery_time_with_recipient' => 'boolean',
    ];

    public static function update_order_pay_id($orderId, $payId ) {
        $element = self::where(["id" => $orderId])->first();
        $element->pay_order = $payId;
        $element->save();
    }

    public static function update_order_status($payId , $orderStatus, $orderStatusText) {
        $element = self::where(["pay_order" => $payId])->first();
        $element->pay_order = $payId;
        $element->pay_status = $orderStatus;
        $element->pay_status_text = $orderStatusText;
        $element->save();
    }

    public function orderProducts() {
        return $this->belongsToMany(Product::class);
    }

    /**
     * Связь с позициями заказа (один ко многим)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Получить общую сумму заказа на основе позиций
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        return $this->items()->sum('total');
    }

    /**
     * Получить общее количество товаров в заказе
     *
     * @return int
     */
    public function getTotalItemsCount(): int
    {
        return $this->items()->sum('quantity');
    }
}
