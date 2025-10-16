<?php

namespace App\Http\Controllers;

use App\Exports\ClosingStockExport;
use App\Exports\ClosingStockExport1;
use App\Exports\SampleExport;
use App\Exports\StockBatchExport;
use App\Exports\StocksExport;
use App\Exports\StocksExportAll;
use App\Imports\ClosingStockDataImport;
use App\Imports\ProductImport;
use App\Imports\StockDataImport;
use App\Models\Products;
use App\Models\ProductVarient;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\StockLog;
use App\Models\StockTransfer;
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

    public function closingStockListold(Request $request)
    {
        $vendor_id = $request->input('vendor_id');
        $search = $request->input('search');

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
        if (!empty($search)) {
            $stocks->where(function ($q) use ($search) {
                $q->where('pv.varient_sku', 'like', '%' . $search . '%')
                    ->orWhere('p.sku', 'like', '%' . $search . '%')
                    ->orWhere('p.name', 'like', '%' . $search . '%')
                    ->orWhere('s.name', 'like', '%' . $search . '%');
            });
        }

// 🏪 Filter by vendor (store)
        if (!empty($vendor_id)) {
            $stocks->where('s.id', $vendor_id);
        }

// ✅ Order and paginate
        $stocks = $stocks->groupBy('sl.store_id', 'sl.product_id', 'sl.variant_id')->orderBy('sl.id', 'asc')->paginate(100);

        $sellers = CustomHelper::getVendors();
        return view('stocks.closing_stock', compact('stocks', 'sellers'));
    }

    public function closingStockList(Request $request)
    {
        $sellerId = $request->input('vendor_id');
        $search = $request->input('search');
        $productId = $request->input('product_id');

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
            ->leftJoin('stock_batches as sl', function ($join) {
                $join->on('sl.product_id', '=', 'p.id')
                    ->where(function ($q) {
                        $q->whereNull('sl.variant_id')
                            ->orWhere('sl.variant_id', 0);
                    })
                    ->where('sl.is_delete', 0);
            })
            ->leftJoin('vendors as s', 's.id', '=', 'sl.store_id')
            ->select(
                DB::raw('COALESCE(s.id, 0) as seller_id'),
                DB::raw('COALESCE(s.name, "N/A") as seller_name'),
                'p.id as product_id',
                'p.name as product_name',
                DB::raw('0 as variant_id'),
                'p.sku as sku',
                DB::raw(' "" as unit'),
                DB::raw('COALESCE(SUM(sl.quantity), 0) as closing_stock')
            )
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('product_varients as pv')
                    ->whereRaw('pv.product_id = p.id');
            })
            ->groupBy(
                'seller_id', 'seller_name',
                'p.id', 'p.name', 'p.sku'
            );

        // 🔍 Apply filters
        if (!empty($search)) {
            $variantStocks->where(function ($q) use ($search) {
                $q->where('pv.varient_sku', $search)
                    ->orWhere('p.sku', $search)
                    ->orWhere('p.name', 'like', '%' . $search . '%');
            });

            $noVariantStocks->where(function ($q) use ($search) {
                $q->where('p.sku', $search)
                    ->orWhere('p.name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($productId)) {
            $variantStocks->where('p.id', $productId);
            $noVariantStocks->where('p.id', $productId);
        }

        if (!empty($sellerId)) {
            $variantStocks->where('s.id', $sellerId);
            // for noVariantStocks, seller is always 0/N/A
        }

        // Merge results
        $stocks = $variantStocks->unionAll($noVariantStocks)
            ->orderBy('product_name')
            ->paginate(500);

        $sellers = CustomHelper::getVendors(); // For filter dropdown

        return view('stocks.closing_stockold', compact('stocks', 'sellers'));
    }


    public function closing_stock_export(Request $request)
    {
        $sellerId = $request->input('vendor_id');
        $search = $request->input('search');

        $fileName = 'closing_stock_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new StockBatchExport($sellerId, $search), $fileName);

    }


    public function stockLogs(Request $request)
    {
        $product_id = $request->product_id ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $search = $request->search ?? '';

        $logs = StockLog::select('stock_logs.*')
            ->leftJoin('products', 'products.id', '=', 'stock_logs.product_id')
            ->leftJoin('product_varients', 'product_varients.id', '=', 'stock_logs.variant_id')
            ->with(['product', 'variant', 'store'])
            ->latest();

        if (!empty($search)) {
            $logs->where(function ($q) use ($search) {
                $q->where('products.sku', $search)
                    ->orWhere('product_varients.varient_sku', $search);
            });
        }

        if (!empty($product_id)) {
            $logs->where('stock_logs.product_id', $product_id);
        }

        if (!empty($vendor_id)) {
            $logs->where('stock_logs.store_id', $vendor_id);
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

    public function update_cs_batch(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $transfer = null;
        if ($request->isMethod('post')) {

            $rules = [
                'to_location' => 'required',
                'from_location' => 'required',
                'batch_id' => 'required',
                'product_id' => 'required',
            ];

            $request->validate($rules);
//
            $saved = $this->updateCS($request, $id);
//
//            if ($saved) {
//                $alert_msg = 'Stock transfer(s) added successfully.';
//                if ($id > 0) {
//                    $alert_msg = 'Stock transfer(s) updated successfully.';
//                }
//                return back()->with('alert-success', $alert_msg);
//            } else {
//                return back()->with('alert-danger', 'Something went wrong, please try again or emails the administrator.');
//            }
        }

        $page_heading = 'Update Closing Stock';
        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['transfer'] = $transfer;
        $data['stocks'] = Stock::latest()->get();
        $data['products'] = Products::with('variants')->orderBy('name','ASC')->get();

        return view('stocks.update_cs_update',$data);

    }
    public function updateCS($request,$id = 0)
    {
        $sku = $request->sku??'';
        $product_id = $request->product_id??'';
        $variant_id = $request->variant_id??'';
        $batch_id = $request->batch_id??'';
        $qty = $request->qty??'';

    }
    public function get_closing_stock(Request $request)
    {
        $productId = $request->product_id;
        $variantId = $request->varient_id;
        $batchId = $request->batch_id;
        $storeId = $request->store_id;

        $stock = Stock::where('id',$batchId)->first();
        $batch_no = '';
        if(!empty($stock)){
            $batch_no = $stock->batch_number??'';
        }


        $qty = \DB::table('stock_batches')
            ->where('product_id', $productId)
            ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
            ->when($batch_no, fn($q) => $q->where('batch_number', $batch_no))
            ->where('store_id', $storeId)
            ->sum('quantity');

        return response()->json(['stock' => $qty]);
    }

}
