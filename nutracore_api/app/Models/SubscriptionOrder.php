<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class SubscriptionOrder extends Model{

    protected $table = 'subscription_orders';

    protected $guarded = ['id'];

}
