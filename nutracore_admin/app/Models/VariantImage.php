<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ProductVarient;
class VariantImage extends Model{

    protected $table = 'product_images';

    protected $guarded = ['id'];


     public function variant() {
        return $this->belongsTo(ProductVarient::class);
    }
}
