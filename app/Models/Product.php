<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'price',
        'tax_percentage',
        'stock_quantity',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];


    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    /**
     * Whether the product has sufficient stock for the given quantity.
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    /**
     * Scope: products whose stock is strictly below the given threshold.
     */
    public function scopeLowStock($query, int $threshold): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('stock_quantity', '<', $threshold);
    }
}
