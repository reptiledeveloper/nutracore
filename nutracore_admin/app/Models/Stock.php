<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model{

    protected $table = 'stocks';

    protected $guarded = ['id'];
    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }


    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVarient::class, 'variant_id');
    }

}
