<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, int $int)
 */
class WalletOffers extends Model
{

    protected $table = 'wallet_offers';

    protected $guarded = ['id'];

}
