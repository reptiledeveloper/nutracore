<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model{

    protected $table = 'product_cart';

    protected $guarded = ['id'];

}
