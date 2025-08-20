<?php

namespace App\Imports;

use App\Helpers\CustomHelper;
use App\Models\Products;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // --- 1. Update or Insert Product ---
            $user = User::updateOrCreate(
                ['phone' => $row['phone'] ?? 0],
                [
                    'name' => $row['name'] ?? '',
                    'cashback_wallet' => $row['nccash'] ?? 0,
                ]
            );
        }
    }


    public function creditNcCash($user, $amount)
    {
        $dbArray1 = [];
        $dbArray1['userID'] = $user->id;
        $dbArray1['txn_no'] = "NC" . rand(1111, 9999999);
        $dbArray1['amount'] = $amount;
        $dbArray1['wallet_type'] = "cashback_wallet";
        $dbArray1['type'] = "CREDIT";
        $dbArray1['note'] = "Earn NC Cash From Import ";
        $dbArray1['against_for'] = 'cashback_wallet';
        $dbArray1['paid_by'] = 'admin';
        $dbArray1['orderID'] = 0;
        CustomHelper::SaveTransaction($dbArray1);
    }
}
