<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model{

    protected $table = 'stock_logs';

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
