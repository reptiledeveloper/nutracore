<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCart extends Model{

    protected $table = 'product_cart_subscription';

    protected $guarded = ['id'];

}
