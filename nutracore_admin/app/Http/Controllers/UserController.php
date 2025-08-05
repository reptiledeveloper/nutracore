<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;
use App\Models\User;
use App\Models\Banner;
use App\Models\Admin;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class UserController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $users = User::where('is_delete', 0)->latest();
        if (!empty($search)) {
            $users->where('name', 'like', '%' . $search . '%');
            $users->orWhere('phone', 'like', '%' . $search . '%');
        }
        $users = $users->paginate(20);
        $data['users'] = $users;
        return view('users.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $users = '';
        if (is_numeric($id) && $id > 0) {
            $users = User::find($id);
            if (empty($users)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/users');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/users';
            }
            $rules = [];
            if (is_numeric($id) && $id > 0) {
                $rules['phone'] = 'required';
            } else {
                $rules['phone'] = 'required|unique:users,phone';
            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'User has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'User has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add User';

        if (!empty($users)) {
            $page_heading = 'Update User';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['users'] = $users;

        return view('users.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';

        $users = new User();

        if (is_numeric($id) && $id > 0) {
            $exist = User::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $users = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $users->$key = $val;
        }

        $isSaved = $users->save();

        if ($isSaved) {
            $this->saveImage($request, $users, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $users, $oldImg = '')
    {

        $file = $request->file('image');
        if ($file) {
            $path = 'users';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $users->image = $uploaded_data;
                $users->save();
            }
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = User::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'User has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


    public function view(Request $request)
    {
        $id = $request->id ?? '';
        $users = User::where('is_delete', 0)->where('id', $id)->first();
        $data['users'] = $users;
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {
            $createdCat = $this->save($request, $id);
            if ($createdCat) {
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'User has been updated successfully.';
                }
                return back()->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }
        return view('users.view', $data);
    }

    public function orders(Request $request)
    {
        $id = $request->id ?? '';
        $users = User::where('is_delete', 0)->where('id', $id)->first();

        $orders = Order::where('userID', $id)->where('is_delete', 0)->latest()->paginate(10);
        $data['users'] = $users;
        $data['orders'] = $orders;

        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {

        }
        return view('users.orders', $data);
    }

    public function transactions(Request $request)
    {
        $id = $request->id ?? '';
        $users = User::where('is_delete', 0)->where('id', $id)->first();
        $data['users'] = $users;
        $transactions = Transaction::where('userID', $id)->latest()->paginate(50);
        $data['transactions'] = $transactions;
        $method = $request->method();
        if ($method == 'post' || $method == 'POST') {

        }
        return view('users.transactions', $data);
    }


    public function update_wallet(Request $request)
    {
        $user_id = $request->user_id ?? '';
        $amount = $request->amount ?? '';
        $type = $request->type ?? '';
        $wallet_type = $request->wallet_type ?? '';
        $remarks = $request->remarks ?? '';
        $expire_date = $request->expire_date ?? '';
        $user = User::where('id', $user_id)->first();
        $wallet = $user->wallet ?? 0;
        $cashback_wallet = $user->cashback_wallet ?? 0;
        if ($wallet_type == 'wallet') {
            if ($type == 'CREDIT') {
                $new_wallet = (int)$wallet + $amount;
                $user->wallet = $new_wallet;
                $user->save();
                $dbArray1 = [];
                $dbArray1['userID'] = $user_id;
                $dbArray1['txn_no'] = "BBC" . rand(1111, 9999999);
                $dbArray1['amount'] = $amount;
                $dbArray1['wallet_type'] = $wallet_type;
                $dbArray1['type'] = $type;
                $dbArray1['note'] = $request->remarks??'';
                $dbArray1['against_for'] = 'wallet';
                $dbArray1['paid_by'] = 'admin';
                $dbArray1['orderID'] = 0;
                $dbArray1['expired_at'] = $expire_date;
                CustomHelper::SaveTransaction($dbArray1);
                return back()->with('alert-success', 'Wallet Updated SuccessFully');
            }
            if ($type == 'DEBIT') {
                if ($wallet >= $amount) {
                    $new_wallet = (int)$wallet - $amount;
                    $user->wallet = $new_wallet;
                    $user->save();
                    $dbArray1 = [];
                    $dbArray1['userID'] = $user_id;
                    $dbArray1['txn_no'] = "BBC" . rand(1111, 9999999);
                    $dbArray1['amount'] = $amount;
                    $dbArray1['wallet_type'] = $wallet_type;
                    $dbArray1['type'] = $type;
                    $dbArray1['note'] = $request->remarks??'';
                    $dbArray1['against_for'] = 'wallet';
                    $dbArray1['paid_by'] = 'admin';
                    $dbArray1['orderID'] = 0;
                    $dbArray1['expired_at'] = $expire_date;
                    CustomHelper::SaveTransaction($dbArray1);
                    return back()->with('alert-success', 'Wallet Updated SuccessFully');
                } else {
                    return back()->with('alert-danger', 'Insufficient Balance');
                }
            }
        }
        if ($wallet_type == 'cashback_wallet') {
            if ($type == 'CREDIT') {
                $new_wallet = (int)$cashback_wallet + $amount;
                $user->cashback_wallet = $new_wallet;
                $user->save();
                $dbArray1 = [];
                $dbArray1['userID'] = $user_id;
                $dbArray1['txn_no'] = "BBC" . rand(1111, 9999999);
                $dbArray1['amount'] = $amount;
                $dbArray1['wallet_type'] = $wallet_type;
                $dbArray1['type'] = $type;
                $dbArray1['note'] = $request->remarks??'';
                $dbArray1['against_for'] = 'cashback_wallet';
                $dbArray1['paid_by'] = 'admin';
                $dbArray1['orderID'] = 0;
                $dbArray1['expired_at'] = $expire_date;
                CustomHelper::SaveTransaction($dbArray1);
                return back()->with('alert-success', 'Wallet Updated SuccessFully');
            }
            if ($type == 'DEBIT') {
                if ($cashback_wallet >= $amount) {
                    $new_wallet = (int)$cashback_wallet - $amount;
                    $user->cashback_wallet = $new_wallet;
                    $user->save();
                    $dbArray1 = [];
                    $dbArray1['userID'] = $user_id;
                    $dbArray1['txn_no'] = "BBC" . rand(1111, 9999999);
                    $dbArray1['amount'] = $amount;
                    $dbArray1['wallet_type'] = $wallet_type;
                    $dbArray1['type'] = $type;
                    $dbArray1['note'] = $request->remarks??'';
                    $dbArray1['against_for'] = 'cashback_wallet';
                    $dbArray1['paid_by'] = 'admin';
                    $dbArray1['orderID'] = 0;
                    $dbArray1['expired_at'] = $expire_date;
                    CustomHelper::SaveTransaction($dbArray1);
                    return back()->with('alert-success', 'Wallet Updated SuccessFully');
                } else {
                    return back()->with('alert-danger', 'Insufficient Balance');
                }
            }
        }
        return back()->with('alert-success', 'Wallet Updated Successfully');
    }

    public function search(Request $request)
    {
        $search = $request->q ?? '';
        $itemArr = [];
        $pagination = false;
        if (!empty($search)) {
            $products = User::where('status', 1)->where('is_delete', 0);
            $products->where('name', 'like', '%' . $search . '%');
            $products->orWhere('phone', 'like', '%' . $search . '%');
            $products = $products->paginate(10);
            if (!empty($products)) {
                foreach ($products as $product) {
                    $dbArray = [];
                    $dbArray['id'] = $product->id ?? '';
                    $name = $product->name ?? '';
                    $name .= '-' . $product->phone ?? '';
                    $dbArray['text'] = $name ?? '';
                    $itemArr[] = $dbArray;
                }
            }
            if ($products->lastPage() > 1) {
                $pagination = true;
            }
        }
        $paginationArr['more'] = $pagination;
        echo json_encode(['items' => $itemArr, 'pagination' => $paginationArr]);

    }
}
