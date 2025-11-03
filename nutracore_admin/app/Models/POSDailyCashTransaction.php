<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POSDailyCashTransaction extends Model{

    protected $table = 'pos_daily_cash_transaction';

    protected $guarded = ['id'];


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendors::class, 'vendor_id');
    }
}
