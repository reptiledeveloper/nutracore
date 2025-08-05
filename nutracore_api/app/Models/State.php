<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class State extends Model{

    protected $table = 'states';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [];




    public function country(){
    	return $this->belongsTo('App\Models\Country');
    }

}