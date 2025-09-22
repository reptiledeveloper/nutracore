<?php

namespace App\Http\Controllers;

use App\Exports\ClosingStockExport;
use App\Exports\SampleExport;
use App\Exports\StocksExport;
use App\Exports\StocksExportAll;
use App\Imports\ClosingStockDataImport;
use App\Imports\ProductImport;
use App\Imports\StockDataImport;
use App\Models\Products;
use App\Models\Stock;
use App\Models\StockBatch;
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
        if ($request->filled('search')) {
            $q->where('sku', $request->search);
        }
        if ($request->filled('vendor_id')) {
            $q->where('store_id', $request->vendor_id);
        }

        $stocks = $q->where('is_delete', 0)->orderBy('expiry_date')->paginate(100)->withQueryString();


        return view('stocks.index', [
            'stocks' => $stocks,
            'days' => $days
        ]);
    }

    public function delete_data(Request $request)
    {
        $stock_ids = $request->stock_ids ?? [];

        if (empty($stock_ids) || !is_array($stock_ids)) {
            return back()->with('alert-danger', 'No stock items selected for deletion.');
        }

        // Remove 'all' if it's accidentally submitted
        $filtered_ids = array_filter($stock_ids, fn($id) => $id !== 'all');

        // Delete stocks
        Stock::whereIn('id', $filtered_ids)->update(['is_delete' => 1]);
        StockBatch::whereIn('stock_id', $filtered_ids)->update(['is_delete' => 1]);
        return back()->with('alert-success', 'Selected stock items deleted successfully.');
    }


    public function closingStockList(Request $request)
    {
        $sellerId = $request->input('vendor_id');
        $search = $request->input('search');
        $query = DB::table('stock_batches as sl')
            ->join('product_varients as pv', 'pv.id', '=', 'sl.variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('vendors as s', 's.id', '=', 'sl.store_id')
            ->select(
                's.id as seller_id',
                's.name as seller_name',
                'p.id as product_id',
                'p.name as product_name',
                'pv.id as variant_id',
                'pv.varient_sku as sku',
                'pv.unit',
                DB::raw('SUM(sl.quantity) as closing_stock') // sum of all batches
            )
            ->where('sl.is_delete', 0)
            ->groupBy('s.id', 'p.id', 'pv.id');

        if (!empty($sellerId)) {
            $query->where('s.id', $sellerId);
        }

        if (!empty($search)) {
            $query->where('pv.varient_sku', $search);
        }

        $stocks = $query->paginate(10);


        $sellers = CustomHelper::getVendors(); // For filter dropdown

        return view('stocks.closing_stock', compact('stocks', 'sellers'));
    }
    public function closing_stock_export(Request $request)
    {
        $sellerId = $request->input('vendor_id');
        $search   = $request->input('search');

        $fileName = 'closing_stock_'.date('Ymd_His').'.xlsx';
        return Excel::download(new ClosingStockExport($sellerId, $search), $fileName);

    }


    public function stockLogs(Request $request)
    {
        $product_id = $request->product_id ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $search = $request->search ?? '';
        if(!empty($search)){
            $product_id = Products::where('sku', $search)->first()->id ?? '';
        }
        $logs = StockLog::with(['product', 'variant', 'store'])
            ->latest();
        if (!empty($product_id)) {
            $logs->where('product_id', $product_id);
        }
        if (!empty($vendor_id)) {
            $logs->where('store_id', $vendor_id);
        }
        $logs = $logs->paginate(20);

        return view('stocks.logs', compact('logs'));
    }


    public function import(Request $request)
    {
        $data = [];
        $method = $request->method();
        if ($method == 'POST') {
            $request->validate([
                'file' => 'required',
                'store_id' => 'required',
            ]);

            Excel::import(new StockDataImport($request->store_id), $request->file('file'));
            return back()->with('success', ' Imported successfully!');
        }

        return back()->with('success', 'Imported successfully!');

    }
    public function update_closing_stock(Request $request)
    {
        $data = [];
        $method = $request->method();
        if ($method == 'POST') {
            $request->validate([
                'file' => 'required',
            ]);

            Excel::import(new ClosingStockDataImport(), $request->file('file'));
            return back()->with('success', ' Imported successfully!');
        }
        return back()->with('success', 'Imported successfully!');

    }

    public function export(Request $request)
    {
        $exportArr = [];
        $products = Products::where('is_delete', 0)->get();
        if (!empty($products)) {
            foreach ($products as $product) {
                $varients = CustomHelper::getAdminProductVarients($product->id);
                if (!empty($varients) && count($varients) > 0) {
                    foreach ($varients as $varient) {
                        $excelArr = [];
                        $excelArr['Product ID'] = $product->id ?? '';
                        $excelArr['ProductName'] = $product->name ?? '';
                        $excelArr['Variant ID'] = $varient->id ?? '';
                        $excelArr['VarientName'] = $varient->unit ?? '';
                        $excelArr['SKU'] = $varient->varient_sku ?? '';
                        $excelArr['Batch Number'] = '';
                        $excelArr['Quantity'] = '';
                        $excelArr['Mfg Date'] = '';
                        $excelArr['Expiry Date'] = '';
                        $excelArr['PurchasePrice'] = '';
                        $exportArr[] = $excelArr;
                    }
                } else {
                    $excelArr = [];
                    $excelArr['Product ID'] = $product->id ?? '';
                    $excelArr['ProductName'] = $product->name ?? '';
                    $excelArr['Variant ID'] = '';
                    $excelArr['VarientName'] = '';
                    $excelArr['SKU'] = $product->sku ?? '';
                    $excelArr['Batch Number'] = '';
                    $excelArr['Quantity'] = '';
                    $excelArr['Mfg Date'] = '';
                    $excelArr['Expiry Date'] = '';
                    $excelArr['PurchasePrice'] = '';
                    $exportArr[] = $excelArr;
                }
            }
        }

        if (!empty($exportArr)) {
            $headings = array_keys($exportArr[0]);
            $fileName = 'Stock Sample-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $headings), $fileName);
        } else {
            return back();
        }

    }

    public function export_all(Request $request)
    {
        $filters = $request->only(['days', 'batch_no', 'product_id', 'variant_id', 'search', 'vendor_id']);

        return Excel::download(new StocksExportAll($filters), 'stocks.xlsx');

    }


}
