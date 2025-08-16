<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model{

    protected $table = 'categorywise_commission';

    protected $guarded = ['id'];

}
