<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class StocksExportAll implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $q = Stock::with(['product', 'variant']);

        if (!empty($this->filters['days']) && $this->filters['days'] > 0) {
            $today = now()->startOfDay();
            $q->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays($this->filters['days'])]);
        }

        if (!empty($this->filters['batch_no'])) {
            $q->where('batch_number', 'like', '%' . $this->filters['batch_no'] . '%');
        }

        if (!empty($this->filters['product_id'])) {
            $q->where('product_id', $this->filters['product_id']);
        }

        if (!empty($this->filters['variant_id'])) {
            $q->where('variant_id', $this->filters['variant_id']);
        }

        if (!empty($this->filters['search'])) {
            $q->where('sku', $this->filters['search']);
        }

        if (!empty($this->filters['vendor_id'])) {
            $q->where('store_id', $this->filters['vendor_id']);
        }

        return $q->orderBy('expiry_date');
    }

    public function headings(): array
    {
        return [
            'S.No',
            'STORE NAME',
            'SKU',
            'Product',
            'Variant',
            'Batch',
            'MFG',
            'Expiry',
            'Quantity',
            'Purchase Price',
        ];
    }

    public function map($stock): array
    {
        return [
            '', // S.No placeholder, will be added in controller or can be skipped
            \App\Helpers\CustomHelper::getVendorName($stock->store_id??'')??'',
            $stock->variant?->varient_sku ?? $stock->product?->sku ?? 'N/A',
            $stock->product?->name ?? 'N/A',
            $stock->variant?->unit ?? '-',
            $stock->batch_number ?? '-',
            $stock->mfg_date ? \Carbon\Carbon::parse($stock->mfg_date)->format('d-M-Y') : '-',
            $stock->expiry_date ? \Carbon\Carbon::parse($stock->expiry_date)->format('d-M-Y') : '-',
            $stock->quantity,
            number_format($stock->purchase_price, 2),
        ];
    }
}
