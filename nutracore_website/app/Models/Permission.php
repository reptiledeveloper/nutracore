<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int $int)
 * @method static insert(array $dbArray)
 */
class Permission extends Model
{
    protected $table = 'permissions';
    protected $guarded = ['id'];
    protected $fillable = [];

}
