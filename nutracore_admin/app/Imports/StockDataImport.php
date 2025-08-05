<?php

namespace App\Imports;

use App\Models\Agents;
use App\Models\ETA;
use App\Models\Pincode;
use App\Models\SouceZone;
use DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class StockDataImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $id = $row['id'] ?? '';
        $varientid = $row['varientid'] ?? '';
        $vendorid = $row['vendorid'] ?? '';
        $stockavailable = $row['stockavailable'] ?? '';

        if (!empty($id) && !empty($varientid)) {
            $exist = DB::table('product_stocks')->where('vendor_id', $vendorid)->where('product_id', $id)->where('varient_id', $varientid)->first();
            if (empty($exist)) {
                DB::table('product_stocks')->insert([
                    "vendor_id" => $vendorid, 'product_id' => $id, 'varient_id' => $varientid, 'no_of_stock' => $stockavailable,
                ]);
            } else {
                DB::table('product_stocks')->where('vendor_id', $vendorid)->where('product_id', $id)->where('varient_id', $varientid)->update([
                    'no_of_stock' => $stockavailable,
                ]);
            }
        }
    }
}

