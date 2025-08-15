<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model{

    protected $table = 'stocks';

    protected $guarded = ['id'];
    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

}
