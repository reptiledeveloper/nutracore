<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Chats extends Model{

    protected $table = 'chats';

    protected $guarded = ['id'];

}
