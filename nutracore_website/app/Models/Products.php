<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\ProductVarient; // Assuming ProductVarient is the correct model name
use App\Models\Attributes; // Assuming ProductVarient is the correct model name

class Products extends Model
{

    protected $table = 'products';

    protected $guarded = ['id'];


public function variants()
{
    return $this->hasMany(ProductVarient::class, 'product_id', 'id');
}
public function varients()
{
    return $this->hasMany(ProductVarient::class, 'product_id', 'id');
}

    public function attributes()
    {
        return $this->belongsToMany(Attributes::class)->withPivot('values');
    }
}
