<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{

    protected $table = 'products';
    protected $guarded = ['id'];

    public function varients()
    {
        return $this->hasMany(ProductVarient::class, 'product_id');
    }
}
