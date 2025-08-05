<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $order_id)
 */
class Order extends Model{

    protected $table = 'orders';

    protected $guarded = ['id'];

    public static function generateOrderId()
    {
        // Define the prefix
        $prefix = 'BBC';

        // Get the latest order id from the database
        $latestOrder = self::where('unique_id','!=',null)->orderBy('id', 'desc')->first();

        if ($latestOrder) {
            // Extract the number part of the last order_id (e.g., '000001' from 'BBC000001')
            $lastOrderNumber = (int) substr($latestOrder->unique_id, 3); // Remove 'BBC'

            // Increment the number
            $newOrderNumber = $lastOrderNumber + 1;
        } else {
            // If no orders exist, start from 1
            $newOrderNumber = 1;
        }

        // Format the new order number with leading zeros
        $newOrderNumberFormatted = str_pad($newOrderNumber, 6, '0', STR_PAD_LEFT);

        // Combine prefix and formatted order number
        return $prefix . $newOrderNumberFormatted;
    }

}
