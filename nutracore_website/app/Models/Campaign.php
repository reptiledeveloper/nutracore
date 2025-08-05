<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model{

    protected $table = 'campaigns';

    protected $guarded = ['id'];

}
