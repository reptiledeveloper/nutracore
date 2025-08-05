<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SubscriptionOrder extends Model{
    protected $table = 'subscription_orders';

    protected $guarded = ['id'];



    public function isAlternateDay($currentDate): bool
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $currentDate = Carbon::parse($currentDate);

        // Ensure current date is within the range
        if ($currentDate->lt($startDate) || $currentDate->gt($endDate)) {
            return false;
        }

        // Calculate the difference in days
        $diffInDays = $startDate->diffInDays($currentDate);

        // Alternate day logic (even difference)
        return $diffInDays % 2 === 0;
    }


}
