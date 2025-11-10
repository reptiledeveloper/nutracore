<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model{

    protected $table = 'suppliers';

    protected $guarded = ['id'];



    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'supplier_id', 'id');
    }

}
