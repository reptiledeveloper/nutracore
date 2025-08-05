<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model{

    protected $table = 'collections';

    protected $guarded = ['id'];

}
