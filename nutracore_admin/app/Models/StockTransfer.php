<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model{

    protected $table = 'stock_transfers';

    protected $guarded = ['id'];

    public function stock() {
        return $this->belongsTo(Stock::class);
    }

}
