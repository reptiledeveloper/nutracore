<?php

namespace App\Exports;

use App\Models\StockBatch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClosingStockExport implements FromCollection, WithHeadings
{
    protected $product_id;
    protected $vendor_id;

    public function __construct($product_id = null, $vendor_id = null)
    {
        $this->product_id = $product_id;
        $this->vendor_id = $vendor_id;
    }

    public function collection()
    {
        $query = StockBatch::with(['product', 'variant', 'store'])->latest();

        if (!empty($this->product_id)) {
            $query->where('product_id', $this->product_id);
        }
        if (!empty($this->vendor_id)) {
            $query->where('store_id', $this->vendor_id);
        }

        return $query->get()->map(function ($batch) {
            return [
                'ID' => $batch->id ?? '',
                'Store' => $batch->store->name ?? '-',
                'SKU' => $batch->variant->varient_sku ?? $batch->product->sku ?? '-',
                'Batch' => $batch->batch_number ?? '-',
                'Product' => $batch->product->name ?? '-',
                'Variant' => $batch->variant->unit ?? '-',
                'Closing Stock' => $batch->quantity ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID','Store', 'SKU', 'Batch', 'Product', 'Variant', 'Closing Stock'];
    }
}

