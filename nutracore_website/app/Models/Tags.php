<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model{

    protected $table = 'tags';

    protected $guarded = ['id'];

}
