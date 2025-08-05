<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model{

    protected $table = 'faqs';

    protected $guarded = ['id'];

}
