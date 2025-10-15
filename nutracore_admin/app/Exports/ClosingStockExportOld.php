<?php

namespace App\Exports;

use App\Models\StockBatch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class ClosingStockExportOld implements FromCollection, WithHeadings
{
    protected $search;
    protected $vendor_id;

    public function __construct($vendor_id = null, $search = null)
    {
        $this->search = $search;
        $this->vendor_id = $vendor_id;
    }

    public function collection()
    {
        $stocks = DB::table('stock_logs as sl')
            ->leftJoin('vendors as s', 's.id', '=', 'sl.store_id')
            ->leftJoin('products as p', 'p.id', '=', 'sl.product_id')
            ->leftJoin('product_varients as pv', 'pv.id', '=', 'sl.variant_id')
            ->where('sl.is_delete', 0)
            ->select([
                'sl.id as id',
                'p.id as product_id',
                'pv.id as varient_id',
                's.id as vendor_id',
                's.name as store_name',
                'p.sku as product_sku',
                'p.name as product_name',
                'pv.unit as variant_name',
                'pv.varient_sku as sku',
                'sl.closing_stock as closing_stock',
            ]);

        // 🔍 Apply search filter
        if (!empty($this->search)) {
            $stocks->where(function ($q) {
                $search = $this->search;
                $q->where('pv.varient_sku', 'like', "%{$search}%")
                    ->orWhere('p.sku', 'like', "%{$search}%")
                    ->orWhere('p.name', 'like', "%{$search}%")
                    ->orWhere('s.name', 'like', "%{$search}%");
            });
        }

        // 🏪 Filter by vendor (store)
        if (!empty($this->vendor_id)) {
            $stocks->where('s.id', $this->vendor_id);
        }

        // ✅ Group by store, product, and variant
        $stocks = $stocks->groupBy('sl.store_id', 'sl.product_id', 'sl.variant_id')
            ->orderBy('sl.id', 'asc')
            ->get();

        // 🧮 Transform data for export
        return $stocks->map(function ($stock, $i) {
            $closing_stock = \App\Helpers\CustomHelper::getClosingStock(
                $stock->product_id ?? '',
                $stock->varient_id ?? '',
                $stock->vendor_id ?? ''
            );

            return [
                'S.No'           => $i + 1,
                'Store Name'     => $stock->store_name ?? '',
                'SKU'            => $stock->sku ?? $stock->product_sku ?? '',
                'Product Name'   => $stock->product_name ?? '',
                'Variant Name'   => $stock->variant_name ?? '-',
                'Closing Stock'  => $closing_stock ?? 0,
            ];
        });
    }


    public function headings(): array
    {
        return ['ID','Store', 'SKU', 'Product', 'Variant', 'Closing Stock'];
    }
}

