<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * @method static where(string $string, mixed $email)
 */
class Sellers extends Authenticatable{

    use Notifiable;

    protected $guard = 'vendors';

    protected $casts = ['password' => 'hashed'];

    protected $table = 'admins';

    protected $guarded = ['id'];

    protected $fillable = [];
}
