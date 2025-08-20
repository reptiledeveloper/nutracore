<?php

namespace App\Http\Controllers;

use App\Exports\StocksExport;
use App\Imports\ProductImport;
use App\Models\Stock;
use App\Models\StockLog;
use Attribute;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

use App\Models\Brand;
use App\Models\Attributes;
use Illuminate\Support\Carbon;
use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class StockController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $days = (int)$request->get('expiry_in_days', 0); // 0 = all
        $q = Stock::with(['product', 'variant']); // eager load relations

        // Expiry filter
        if ($days > 0) {
            $today = Carbon::today();
            $q->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$today, $today->copy()->addDays($days)]);
        }

        // Batch number filter
        if ($request->filled('batch_no')) {
            $q->where('batch_number', 'like', '%' . $request->batch_no . '%');
        }

        // Product filter
        if ($request->filled('product_id')) {
            $q->where('product_id', $request->product_id);
        }

        // Variant filter
        if ($request->filled('variant_id')) {
            $q->where('variant_id', $request->variant_id);
        }

        $stocks = $q->orderBy('expiry_date')->paginate(20)->withQueryString();

        return view('stocks.index', [
            'stocks' => $stocks,
            'days'   => $days
        ]);
    }



    public function closingStockList(Request $request)
    {
        $sellerId = $request->input('vendor_id');

        $query = DB::table('stock_batches as sb')
            ->join('product_varients as pv', 'pv.id', '=', 'sb.variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('vendors as s', 's.id', '=', 'sb.store_id')
            ->select(
                's.id as seller_id',
                's.name as seller_name',
                'p.id as product_id',
                'p.name as product_name',
                'pv.id as variant_id',
                'pv.unit',
                DB::raw('SUM(sb.quantity) as closing_stock')
            )
            ->groupBy('s.id', 'p.id', 'pv.id');

        if (!empty($sellerId)) {
            $query->where('s.id', $sellerId);
        }

        $stocks = $query->paginate(10);

        $sellers = CustomHelper::getVendors(); // For filter dropdown

        return view('stocks.closing_stock', compact('stocks', 'sellers'));
    }


    public function stockLogs()
    {
        $logs = StockLog::with(['product','variant','store'])
            ->latest()
            ->paginate(20);

        return view('stocks.logs', compact('logs'));
    }


    public function import(Request $request)
    {
        $data = [];
        $method = $request->method();
        if($method == 'POST'){
            $request->validate([
                'file' => 'required',
            ]);

            Excel::import(new ProductImport, $request->file('file'));
            return back()->with('success', ' Imported successfully!');
        }

        return back()->with('success', 'Imported successfully!');

    }

    public function export(Request $request)
    {
        return Excel::download(new StocksExport($request), 'stocks.xlsx');
    }



}
