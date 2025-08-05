<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static select(string $string, string $string1)
 */
class ProductImage extends Model{

    protected $table = 'product_images';

    protected $guarded = ['id'];

}
