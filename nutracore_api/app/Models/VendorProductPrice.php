<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class VendorProductPrice extends Model{

    protected $table = 'vendor_product_price';

    protected $guarded = ['id'];

}
