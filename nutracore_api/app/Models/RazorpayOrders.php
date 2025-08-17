<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $order_id)
 */
class RazorpayOrders extends Model
{

    protected $table = 'razorpay_orders';

    protected $guarded = ['id'];

}
