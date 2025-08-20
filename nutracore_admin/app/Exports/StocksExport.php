<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class StocksExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $days = (int)$this->request->get('expiry_in_days', 0);
        $q = Stock::with(['product', 'variant']);

        // Expiry filter
        if ($days > 0) {
            $today = Carbon::today();
            $q->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays($days)]);
        }

        // Batch number filter
        if ($this->request->filled('batch_no')) {
            $q->where('batch_number', 'like', '%' . $this->request->batch_no . '%');
        }

        // Product filter
        if ($this->request->filled('product_id')) {
            $q->where('product_id', $this->request->product_id);
        }

        // Variant filter
        if ($this->request->filled('variant_id')) {
            $q->where('variant_id', $this->request->variant_id);
        }

        // Retrieve all matching data
        $stocks = $q->orderBy('expiry_date')->get();

        return $stocks->map(function ($stock) {
            return [
                'Product ID' => $stock->product->id ?? '',
                'Product Name' => $stock->product->name ?? 'N/A',
                'Variant ID' => $stock->variant->id ?? '',
                'Variant Name' => $stock->variant->unit ?? 'N/A',
                'SKU' => $stock->variant->varient_sku ?? 'N/A',
                'Batch Number' => $stock->batch_number,
                'Quantity' => $stock->quantity,
                'Expiry Date' => $stock->expiry_date,
                'PurchasePrice' => $stock->purchase_price,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Variant ID',
            'Variant Name',
            'SKU',
            'Batch Number',
            'Quantity',
            'Expiry Date',
            'PurchasePrice',
        ];
    }
}
