<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model{

    protected $table = 'invoices';

    protected $guarded = ['id'];


    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
