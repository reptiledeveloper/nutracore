<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $order_id)
 */
class OrderItems extends Model{

    protected $table = 'order_items';

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    // ✅ Each item belongs to one product
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }
    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'variant_id', 'id');
    }
}
