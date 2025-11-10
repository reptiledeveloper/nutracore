<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model{

    protected $table = 'stock_batches';

    protected $guarded = ['id'];

    public function product() {
        return $this->belongsTo(Products::class);
    }

    public function variant() {
        return $this->belongsTo(ProductVarient::class);
    }

    public function store() {
        return $this->belongsTo(Vendors::class);
    }
}
