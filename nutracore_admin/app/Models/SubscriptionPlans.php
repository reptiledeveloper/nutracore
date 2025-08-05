<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlans extends Model{

    protected $table = 'subscription_plans';

    protected $guarded = ['id'];

}
