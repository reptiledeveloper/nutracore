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

//        echo "<pre>";
//        print_r($rows);
//        die;
        foreach ($rows as $row) {
            $phone = trim($row['phone']) ?? 0;
            $existing = DB::table('users')->where('phone', $phone)->first();

            if ($existing) {
                \DB::enableQueryLog(); // Enable query log

                $data = DB::table('users')->where('id', $existing->id)->update([
                    'cashback_wallet' => $row['nccash'] ?? 0,
                ]);
                //dd(\DB::getQueryLog()); // Show results of log

                echo $row['nccash'];
                print_r($existing);
                die;
                $userId = $existing->id;
            } else {
                $userId = DB::table('users')->insertGetId([
                    'phone' => $phone,
                    'name' => $row['name'] ?? '',
                    'cashback_wallet' => $row['nccash'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

// Then pass a pseudo-user object to creditNcCash
            $user = (object)['id' => $userId];
            self::creditNcCash($user, $row['nccash'] ?? 0);

        }

        die;
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
