<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockBatchExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('stock_batches as sb')
            ->leftJoin('products as p', 'sb.product_id', '=', 'p.id')
            ->leftJoin('product_varients as pv', 'sb.variant_id', '=', 'pv.id')
            ->leftJoin('vendors as s', 'sb.store_id', '=', 's.id')
            ->select(
                'sb.id as ID',
                'sb.store_id as STOREID',
                DB::raw('COALESCE(s.name, "-") as StoreName'),
                'sb.product_id as PRODUCTID',
                DB::raw('COALESCE(p.name, "-") as ProductName'),

                // ✅ Single SKU column — variant SKU preferred, product SKU fallback
                DB::raw('CASE
                        WHEN pv.varient_sku IS NOT NULL AND pv.varient_sku != ""
                        THEN pv.varient_sku
                        ELSE COALESCE(p.sku, "-")
                     END as SKU'),

                'sb.variant_id as VARIENTID',
                DB::raw('COALESCE(pv.unit, "-") as Unit'),
                DB::raw('SUM(sb.quantity) as ClosingStock')
            )
            ->where('sb.is_delete', 0)
            ->groupBy(
                'sb.product_id',
                'sb.variant_id',
                'sb.store_id',
                'sb.id',
                'p.name',
                'pv.varient_sku',
                'pv.unit',
                's.name',
                'p.sku'
            )
            ->orderBy('sb.id', 'asc');

        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'STOREID', 'Store Name', 'PRODUCTID', 'Product Name', 'SKU', 'VARIENTID', 'Unit', 'Closing Stock'];
    }

}
