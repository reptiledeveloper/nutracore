<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $order_id)
 */
class Wishlist extends Model{

    protected $table = 'wishlist';

    protected $guarded = ['id'];


}
