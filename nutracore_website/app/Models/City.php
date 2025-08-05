<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class City extends Model{

    protected $table = 'cities';

    protected $guarded = ['id'];

}
