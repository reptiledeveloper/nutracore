<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VariantImage; // Assuming VariantImage is the correct model name

class ProductVarient extends Model
{

    protected $table = 'product_varients';

    protected $guarded = ['id'];


    public function images()
    {
        return $this->hasMany(VariantImage::class, 'varient_id','product_id');
    }
}
