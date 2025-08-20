<?php

namespace App\Imports;

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
            $product = User::updateOrCreate(
                ['phone' => $row['phone'] ?? 0],
                [
                    'name' => $row['name'] ?? '',
                    'cashback_wallet' => $row['nccash'] ?? 0,
                ]
            );
        }
    }
}
