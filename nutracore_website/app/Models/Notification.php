<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int $int)
 * @method static insertGetId(array $dbArray)
 */
class Notification extends Model{

    protected $table = 'notifications';

    protected $guarded = ['id'];
}
