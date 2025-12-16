<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class CountDownTimer extends Model{
    protected $table = 'product_countdowns';

    protected $guarded = ['id'];

}
