<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model{

    protected $table = 'transactions';

    protected $guarded = ['id'];

}
