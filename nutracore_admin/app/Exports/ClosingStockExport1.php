<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClosingStockExport1 implements FromCollection, WithHeadings
{
    protected $vendor_id;
    protected $search;
    protected $product_id;

    public function __construct($vendor_id = null, $search = null, $product_id = null)
    {
        $this->vendor_id = $vendor_id;
        $this->search = $search;
        $this->product_id = $product_id;
    }

    public function collection()
    {
        // 1️⃣ Products WITH variants
        $variantStocks = DB::table('products as p')
            ->join('product_varients as pv', 'pv.product_id', '=', 'p.id')
            ->leftJoin('stock_batches as sl', function ($join) {
                $join->on('sl.variant_id', '=', 'pv.id')
                    ->where('sl.is_delete', 0);
            })
            ->leftJoin('vendors as s', 's.id', '=', 'sl.store_id')
            ->select(
                DB::raw('COALESCE(s.id, 0) as seller_id'),
                DB::raw('COALESCE(s.name, "N/A") as seller_name'),
                'p.id as product_id',
                'p.name as product_name',
                DB::raw('pv.id as variant_id'),
                DB::raw('pv.varient_sku as sku'),
                DB::raw('pv.unit as unit'),
                DB::raw('COALESCE(SUM(sl.quantity), 0) as closing_stock')
            )
            ->groupBy(
                'seller_id', 'seller_name',
                'p.id', 'p.name',
                'pv.id', 'pv.varient_sku', 'pv.unit'
            );

        // 2️⃣ Products WITHOUT variants
        $noVariantStocks = DB::table('products as p')
            ->leftJoin('vendors as s', 's.id', '=', DB::raw('0')) // dummy join
            ->select(
                DB::raw('0 as seller_id'),
                DB::raw('"N/A" as seller_name'),
                'p.id as product_id',
                'p.name as product_name',
                DB::raw('0 as variant_id'),
                'p.sku as sku',
                DB::raw('"-" as unit'),
                DB::raw('(SELECT COALESCE(SUM(quantity),0)
                      FROM stock_batches
                      WHERE product_id = p.id AND (variant_id IS NULL OR variant_id = 0) AND is_delete = 0) as closing_stock')
            )
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('product_varients as pv')
                    ->whereRaw('pv.product_id = p.id');
            });

        // 🔍 Apply search filters
        if (!empty($this->search)) {
            $variantStocks->where(function ($q) {
                $search = $this->search;
                $q->where('pv.varient_sku', 'like', "%{$search}%")
                    ->orWhere('p.sku', 'like', "%{$search}%")
                    ->orWhere('p.name', 'like', "%{$search}%");
            });
            $noVariantStocks->where(function ($q) {
                $search = $this->search;
                $q->where('p.sku', 'like', "%{$search}%")
                    ->orWhere('p.name', 'like', "%{$search}%");
            });
        }

        // 🎯 Apply product filter
        if (!empty($this->product_id)) {
            $variantStocks->where('p.id', $this->product_id);
            $noVariantStocks->where('p.id', $this->product_id);
        }

        // 🏪 Apply vendor filter
        if (!empty($this->vendor_id)) {
            $variantStocks->where('s.id', $this->vendor_id);
        }

        // 🔗 Merge both queries
        $stocks = $variantStocks->unionAll($noVariantStocks)->get();

        // 🧮 Format output for Excel
        $data = [];
        $i = 1;

        foreach ($stocks as $row) {
            $data[] = [
                'S.No'           => $i++,
                'Store Name'     => $row->seller_name ?? 'N/A',
                'Product Name'   => $row->product_name ?? '',
                'SKU'            => $row->sku ?? '',
                'Unit'           => $row->unit ?? '-',
                'Closing Stock'  => (float) ($row->closing_stock ?? 0),
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return ['S.No', 'Store Name', 'Product Name', 'SKU', 'Unit', 'Closing Stock'];
    }
}
