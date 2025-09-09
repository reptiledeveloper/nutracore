<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;

use App\Models\Brand;
use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class ReturnController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $data = [];
        $search = $request->search ?? '';
        $orders = Order::where('return_status', '!=', null)->orderBy('id', 'desc');

        $orders = $orders->paginate(10);
        $data['orders'] = $orders;
        return view('return_request.index', $data);
    }
    public function order_items(Request $request)
    {
        $data = [];
        $search = $request->search ?? '';
        $orders = OrderItems::where('return_status', '!=', null)->orderBy('id', 'desc');

        $orders = $orders->paginate(10);
        $data['orders'] = $orders;
        return view('return_request.order_items', $data);
    }


    public function update_status(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Order::where('id', $id)->update(['return_status' => $request->return_status ?? "pending", 'admin_remarks' => $request->admin_remarks ?? '']);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Order has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }
    public function update_item_status(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = OrderItems::where('id', $id)->update(['return_status' => $request->return_status ?? "pending", 'admin_remarks' => $request->admin_remarks ?? '']);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Order has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
