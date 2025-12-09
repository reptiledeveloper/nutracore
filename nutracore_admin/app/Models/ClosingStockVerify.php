<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class ClosingStockVerify extends Model{

    protected $table = 'closing_stock_verify';

    protected $guarded = ['id'];



    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'variant_id');
    }
}
