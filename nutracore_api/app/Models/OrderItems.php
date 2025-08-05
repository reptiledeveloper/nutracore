<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $order_id)
 */
class OrderItems extends Model{

    protected $table = 'order_items';

    protected $guarded = ['id'];


}
