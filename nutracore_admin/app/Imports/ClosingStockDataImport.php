<?php

namespace App\Imports;

use App\Helpers\CustomHelper;
use App\Models\Stock;
use App\Models\StockBatch;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Assuming this helper is globally available

class ClosingStockDataImport implements ToModel, WithHeadingRow
{


    public function __construct()
    {


    }

    public function model(array $row)
    {

        // Extract and clean data from the Excel row
        $id = trim($row['id']) ?? null;
        $closing_stock = trim($row['closing_stock']) ?? null;

        $stock_batch = StockBatch::find($id);
        $old_qty = $stock_batch->quantity ?? '';
        $new_qty = $old_qty - $closing_stock;
        // 2. Create or update StockBatch
        if ($stock_batch) {
            $old_qty = $stock_batch->quantity ?? 0;
            $new_qty = $closing_stock;

            // Update StockBatch
            $stock_batch->update([
                'quantity' => $new_qty,
            ]);

            // Log the adjustment
            CustomHelper::logStock(
                $stock_batch->product_id,
                $stock_batch->variant_id,
                $stock_batch->store_id,
                'adjust',
                $old_qty - $new_qty,
                $stock_batch->related_id,
                'Adjust'
            );

            // ✅ Return the updated model
            return $stock_batch;
        }
        // 3. Log the stock transaction (assuming the helper exists)


        // Return the created model instance
        return null;
    }


}
