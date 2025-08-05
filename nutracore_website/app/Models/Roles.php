<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int $int)
 */
class Roles extends Model
{
    protected $table = 'roles';
    protected $guarded = ['id'];
    protected $fillable = [];

}
