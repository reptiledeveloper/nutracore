<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model{

    protected $table = 'suppliers';

    protected $guarded = ['id'];

}
