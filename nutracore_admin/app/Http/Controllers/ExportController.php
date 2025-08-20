<?php

namespace App\Http\Controllers;
use App\Exports\SampleExport;
use App\Helpers\CustomHelper;
use App\Models\Category;

use App\Models\StockDataImport;
use App\Models\DeliveryAgents;
use App\Models\Sellers;
use App\Models\User;
use App\Models\Products;
use App\Models\Transaction;
use App\Exports\StockDataExport;
use Auth;
use DB;
use Google\Service\ShoppingContent\ProductsCustomBatchResponse;


use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;
use Maatwebsite\Excel\Facades\Excel;






class ExportController extends Controller
{


    private string $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function delivery_agent(Request $request)
    {

        $exportArr = [];
        $agents = DeliveryAgents::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Email'] = $agent->email ?? '';
                $excelArr['Phone'] = $agent->phone ?? '';
                $excelArr['Address'] = $agent->address ?? '';
                $excelArr['Vehicle Type'] = $agent->vehicle_type ?? '';
                $excelArr['Vehicle No'] = $agent->vehicle_no ?? '';
                $excelArr['Vehicle Name'] = $agent->vehicle_name ?? '';
                $excelArr['Bank Name'] = $agent->bank_name ?? '';
                $excelArr['Account No'] = $agent->account_no ?? '';
                $excelArr['IFSC Code'] = $agent->ifsc_code ?? '';
                $excelArr['Wallet'] = $agent->wallet ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Delivery Agents-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function sellers(Request $request)
    {

        $exportArr = [];
        $agents = Sellers::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['Business Name'] = $agent->name ?? '';
                $excelArr['Name'] = $agent->user_name ?? '';
                $excelArr['Email'] = $agent->user_email ?? '';
                $excelArr['Phone'] = $agent->user_phone ?? '';
                $excelArr['GST No'] = $agent->tax_number ?? '';
                $excelArr['Address'] = $agent->address ?? '';
                $excelArr['Bank Name'] = $agent->bank_name ?? '';
                $excelArr['Account No'] = $agent->account_no ?? '';
                $excelArr['IFSC Code'] = $agent->ifsc_code ?? '';
                $excelArr['Delivery Time'] = $agent->delivery_time ?? '';
                $excelArr['Open Time'] = $agent->open_time ?? '';
                $excelArr['Close Time'] = $agent->close_time ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Sellers-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function categories(Request $request)
    {

        $exportArr = [];
        $agents = Category::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['ID'] = $agent->id ?? '';
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Slug'] = $agent->slug ?? '';
                $excelArr['Is Subscribe'] = $agent->is_subscribe ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'Categories-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function subcategories(Request $request)
    {

        $exportArr = [];

        $agents = Category::where('status', 1)->where('parent_id', '!=', 0)->where('is_delete', 0);

        $agents = Category::where('status', 1)->where('parent_id','!=',0)->where('is_delete', 0);

        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['ID'] = $agent->id ?? '';
                $excelArr['Parent Category'] = CustomHelper::getCategoryName($agent->parent_id) ?? '';
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Slug'] = $agent->slug ?? '';
                $excelArr['Is Subscribe'] = $agent->is_subscribe ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'SubCategories-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }

    public function users(Request $request)
    {

        $exportArr = [];
        $agents = User::where('status', 1)->where('is_delete', 0);
        $agents->chunk(50, function ($agents) use (&$exportArr) {
            foreach ($agents as $agent) {
                $excelArr = [];
                $excelArr['ID'] = $agent->id ?? '';
                $excelArr['Name'] = $agent->name ?? '';
                $excelArr['Email'] = $agent->email ?? '';
                $excelArr['Phone'] = $agent->phone ?? '';
                $excelArr['NCCash'] = $agent->cashback_wallet ?? '';
                $excelArr['Join On'] = date('d M Y', strtotime($agent->created_at)) ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $fileNames = array_keys($exportArr[0]);
            $fileName = 'User-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new SampleExport($exportArr, $fileNames), $fileName);
        } else {
            return back();
        }
    }





    public function stock_data(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $data = [];
        $category_id = $request->category_id ?? '';
        $sub_category_id = $request->sub_category_id ?? '';
        $vendor_id = $request->vendor_id ?? 0;
        $products = Products::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($category_id)) {
            $products->where('category_id', $category_id);
        }
        if (!empty($sub_category_id)) {
            $products->where('subcategory_id', $sub_category_id);
        }
        $products = $products->paginate(50);
        $data['products'] = $products;
        $exportArr = [];
        if (!empty($products)) {
            foreach ($products as $product) {
                $varients = CustomHelper::getProductVarients($product->id ?? '');
                if (!empty($varients)) {
                    foreach ($varients as $varient) {
                        $stock_avail = CustomHelper::getNoOfStock($product->id, $varient->id, $vendor_id);
                        $excelArr = [];
                        $excelArr['ID'] = $product->id ?? '';
                        $excelArr['VarientID'] = $varient->id ?? '';
                        $excelArr['VendorID'] = (string) $vendor_id;
                        $excelArr['Category'] = CustomHelper::getCategoryName($product->category_id ?? '');
                        $excelArr['SubCategory'] = CustomHelper::getCategoryName($product->subcategory_id ?? '');
                        $excelArr['ProductName'] = $product->name ?? '';
                        $excelArr['Varient'] = $varient->unit ?? '';
                        $excelArr['StockAvailable'] = (string) $stock_avail ?? 0;
                        $exportArr[] = $excelArr;
                    }
                }
            }
        }

        if (!empty($exportArr)) {
            $headings = array_keys($exportArr[0]);
            $fileName = 'Product Stock Data-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new StockDataExport($exportArr, $headings), $fileName);
        } else {
            return back();
        }
    }


    public function transaction(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $start_date = $request->start_date ?? '';
        $end_date = $request->end_date ?? '';
        $exportArr = [];

        $transactionsArr = Transaction::latest('id');
        if (!empty($start_date)) {
            $transactionsArr->whereDate('created_at', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $transactionsArr->whereDate('created_at', '<=', $end_date);
        }
        $transactionsArr->chunk(50, function ($transactions) use (&$exportArr) {
            foreach ($transactions as $transaction) {
                $user = CustomHelper::getUserDetails($transaction->userID);
                $excelArr = [];
                $excelArr['UserName'] = $user->name ?? '';
                $excelArr['UserPhone'] = $user->phone ?? '';
                $excelArr['Txn No'] = $transaction->txn_no ?? '';
                $excelArr['Amount'] = $transaction->amount ?? '';
                $excelArr['Type'] = $transaction->type ?? '';
                $excelArr['Note'] = $transaction->note ?? '';
                $excelArr['Order ID'] = $transaction->orderID ?? '';
                $excelArr['TimeStamp'] = $transaction->created_at ?? '';
                $exportArr[] = $excelArr;
            }
        });
        if (!empty($exportArr)) {
            $headings = array_keys($exportArr[0]);
            $fileName = 'Transaction Data-' . date('Y-m-d-H-i-s') . '.xlsx';
            return Excel::download(new StockDataExport($exportArr, $headings), $fileName);
        } else {
            return back();
        }
    }


    public function stock_data_import(Request $request)
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $rules = [];
            $rules['file'] = 'required';
            $request->validate($rules);

            Excel::import(new StockDataImport, request()->file('file'));
            return back();
        }

    }






}
