<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class SellerPermission extends Model{

    protected $table = 'seller_permissions';

    protected $guarded = ['id'];

}
