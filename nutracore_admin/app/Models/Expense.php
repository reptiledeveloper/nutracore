<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model{

    protected $table = 'expenses';

    protected $guarded = ['id'];

}
