<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class CancelledSubscription extends Model{

    protected $table = 'subscribtion_cancel';

    protected $guarded = ['id'];

}
