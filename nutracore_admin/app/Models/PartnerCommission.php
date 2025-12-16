<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model{

    protected $table = 'partner_commissions';

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
