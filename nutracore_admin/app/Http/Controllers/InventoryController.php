<?php
namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductStock;
use App\Models\StockTransaction;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;


class InventoryController extends Controller
{


    private string $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }



    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $data = [];
        $category_id = $request->category_id ?? '';
        $sub_category_id = $request->sub_category_id ?? '';
        $products = Products::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($category_id)) {
            $products->where('category_id', $category_id);
        }
        if (!empty($sub_category_id)) {
            $products->where('subcategory_id', $sub_category_id);
        }
        $products = $products->paginate(50);
        $data['products'] = $products;
        return view('inventory_management.index', $data);
    }

    public function stock_in(Request $request): \Illuminate\Http\RedirectResponse
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $dbArray = [];
            $dbArray['product_id'] = $request->product_id ?? 0;
            $dbArray['varient_id'] = $request->varient_id ?? 0;
            $dbArray['vendor_id'] = $request->vendor_id ?? 0;
            $exist = ProductStock::where('product_id', $request->product_id)->where('varient_id', $request->varient_id)->where('vendor_id', $request->vendor_id)->first();
            if (empty($exist)) {
                $dbArray['no_of_stock'] = $request->no_of_stock ?? 0;
                ProductStock::insert($dbArray);
                $dbArray['type'] = 'stock_in';
                $dbArray['remarks'] = $request->remarks ?? '';
                StockTransaction::insert($dbArray);
            } else {
                $no_of_stock = $exist->no_of_stock ?? 0;
                $no_of_stock = (int)$no_of_stock + (int)$request->no_of_stock;
                $dbArray['no_of_stock'] = $no_of_stock ?? 0;
                ProductStock::where('id', $exist->id)->update($dbArray);
                $dbArray['type'] = 'stock_in';
                $dbArray['no_of_stock'] = $request->no_of_stock ?? 0;
                $dbArray['remarks'] = $request->remarks ?? '';
                StockTransaction::insert($dbArray);
            }
        }
        return back();
    }

    public function stock_out(Request $request): \Illuminate\Http\RedirectResponse
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $dbArray = [];
            $dbArray['product_id'] = $request->product_id ?? 0;
            $dbArray['varient_id'] = $request->varient_id ?? 0;
            $dbArray['vendor_id'] = $request->vendor_id ?? 0;
            $no_of_stock = $request->no_of_stock ?? 0;
            $exist = ProductStock::where('product_id', $request->product_id)->where('varient_id', $request->varient_id)->where('vendor_id', $request->vendor_id)->first();
            $stock_avail = \App\Helpers\CustomHelper::getNoOfStock($request->product_id ?? 0, $request->varient_id ?? 0, $request->vendor_id ?? 0);
            if ($stock_avail >= $no_of_stock) {
                $no_of_stock_avail = (int)$stock_avail - (int)$no_of_stock;
                ProductStock::where('id', $exist->id)->update(['no_of_stock' => $no_of_stock_avail]);
                $dbArray['type'] = 'stock_out';
                $dbArray['remarks'] = $request->remarks ?? '';
                $dbArray['no_of_stock'] = $request->no_of_stock ?? 0;
                StockTransaction::insert($dbArray);
            }
        }
        return back();
    }

    public function stock_transfer(Request $request): \Illuminate\Http\RedirectResponse
    {
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $dbArray = [];
            $product_id = $request->product_id ?? 0;
            $varient_id = $request->varient_id ?? 0;
            $remarks = $request->remarks ?? '';
            $from_vendor_id = $request->from_vendor_id ?? '';
            $to_vendor_id = $request->to_vendor_id ?? '';
            $no_of_stock = $request->no_of_stock ?? 0;
            $dbArray['product_id'] = $product_id;
            $dbArray['varient_id'] = $varient_id;

            $exist = ProductStock::where('product_id', $product_id)->where('varient_id', $varient_id)->where('vendor_id', $from_vendor_id)->first();
            $stock_avail = \App\Helpers\CustomHelper::getNoOfStock($product_id, $varient_id, $from_vendor_id);
            if ($stock_avail >= $no_of_stock) {
                $no_of_stock_avail = (int)$stock_avail - (int)$no_of_stock;
                ProductStock::where('id', $exist->id)->update(['no_of_stock' => $no_of_stock_avail]);
                $dbArray['type'] = 'stock_transfer';
                $dbArray['vendor_id'] = $from_vendor_id;
                $dbArray['remarks'] = $remarks;
                $dbArray['to_vendor_id'] = $to_vendor_id;
                $dbArray['no_of_stock'] = $request->no_of_stock ?? 0;
                StockTransaction::insert($dbArray);
                $to_exist = ProductStock::where('product_id', $product_id)->where('varient_id', $varient_id)->where('vendor_id', $to_vendor_id)->first();
                if (!empty($to_exist)) {
                    $stock_avail = \App\Helpers\CustomHelper::getNoOfStock($product_id, $varient_id, $to_vendor_id);
                    $no_of_stock_avail = (int)$stock_avail + (int)$no_of_stock;
                    ProductStock::where('id', $to_exist->id)->update(['no_of_stock' => $no_of_stock_avail]);
                    $dbArray['type'] = 'stock_in';
                    $dbArray['vendor_id'] = $to_vendor_id;
                    $dbArray['remarks'] = $remarks;
                    $dbArray['no_of_stock'] = $no_of_stock;
                    StockTransaction::insert($dbArray);
                }else{
                    $dbArray = [];
                    $dbArray['product_id'] = $product_id;
                    $dbArray['varient_id'] = $varient_id;
                    $dbArray['no_of_stock'] = $request->no_of_stock ?? 0;
                    $dbArray['vendor_id'] = $to_vendor_id;
                    ProductStock::insert($dbArray);
                    $dbArray['type'] = 'stock_in';
                    $dbArray['remarks'] = $remarks;
                    StockTransaction::insert($dbArray);
                }
            }
        }
        return back();
    }


}