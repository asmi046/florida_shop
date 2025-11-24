<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'title',
        'img',
        'slug',
        'sku',
        'price',
        'quantity',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Связь с заказом (многие к одному)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Автоматический расчет итоговой суммы перед сохранением
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            $orderItem->total = $orderItem->price * $orderItem->quantity;
        });
    }

    /**
     * Accessor для форматированной цены
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, ',', ' ') . ' ₽';
    }

    /**
     * Accessor для форматированной итоговой суммы
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' ₽';
    }
}
