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

class StockDataImport implements ToModel, WithHeadingRow
{
    private $invoiceId;
    private $storeId;

    public function __construct($storeId)
    {

        $this->storeId = $storeId;
    }

    public function model(array $row)
    {

        // Extract and clean data from the Excel row
        $product_id = trim($row['product_id']) ?? null;
        $variant_id = trim($row['variant_id']) ?? null;
        $batch_number = trim($row['batch_number']) ?? null;
        $quantity = (int) trim($row['quantity']) ?? 0;
        $purchase_price = (float) trim($row['purchaseprice']) ?? 0;

        $mfg_date = (float) trim($row['mfg_date']) ?? 0;
        $sku = trim($row['sku']) ?? null;
        $total_price = $quantity * $purchase_price;
        // Handle dates
        $mfg_date = $this->parseDate($row['mfg_date']);
        $expiry_date = $this->parseDate($row['expiry_date']);

        // Check if required data is present
        if (!$product_id || !$batch_number || $quantity <= 0) {
            return null; // Skip invalid rows
        }

//        print_r($mfg_date);
//        print_r($expiry_date);
//        die;

        // 1. Create a new Stock record
        $stockItem = new Stock();

        $stockItem->product_id = $product_id;
        $stockItem->variant_id = $variant_id;
        $stockItem->batch_number = $batch_number;
        $stockItem->mfg_date = $mfg_date;
        $stockItem->expiry_date = $expiry_date;
        $stockItem->quantity = $quantity;
        $stockItem->purchase_price = $purchase_price;
        $stockItem->total_price = $total_price;
        $stockItem->store_id = $this->storeId;
        $stockItem->sku = $sku;
        $stockItem->save();

        // 2. Create or update StockBatch
        StockBatch::updateOrCreate(
            [
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'batch_number' => $batch_number,
            ],
            [
                'mfg_date' => $mfg_date,
                'store_id' => $this->storeId,
                'expiry_date' => $expiry_date,
                'quantity' => DB::raw('quantity + ' . $quantity),
                'purchase_price' => $purchase_price,
            ]
        );

        // 3. Log the stock transaction (assuming the helper exists)
        CustomHelper::logStock(
            $product_id,
            $variant_id,
            $this->storeId,
            'purchase',
            $quantity,
            "",
            'Purchase'
        );

        // Return the created model instance
        return $stockItem;
    }

    /**
     * Helper function to parse dates from Excel or other formats.
     *
     * @param string|int $date
     * @return string|null
     */
    private function parseDate($value)
    {
        if (!$value) return null;

        try {
            // If it's a numeric Excel date, convert to Carbon
            if (is_numeric($value)) {
                return \Carbon\Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
            }

            // If it's a plain date string, parse normally
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
