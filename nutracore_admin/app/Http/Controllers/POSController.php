<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\POS;
use App\Models\POSDailyCash;
use App\Models\Products;
use App\Models\SubscriptionPlans;
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


class POSController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $pos = POS::where('is_delete', 0)->latest()->paginate(10);
        $data['pos'] = $pos;
        return view('pos.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;
        $pos = '';
        if (is_numeric($id) && $id > 0) {
            $pos = POS::find($id);
            if (empty($pos)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/pos');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/pos';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Order has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Order has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or emails the administrator.');
            }
        }


        $page_heading = 'Add Order';

        if (!empty($pos)) {
            $page_heading = 'Update Order';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['pos'] = $pos;

        return view('pos.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'image_text', 'product_id']);
        $oldImg = '';
        $admin = new POS();
        if (is_numeric($id) && $id > 0) {
            $exist = POS::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $admin = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);
        foreach ($data as $key => $val) {
            $admin->$key = $val;
        }

        $isSaved = $admin->save();

        if ($isSaved) {
//            $this->saveImage($request, $admin, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $banner, $oldImg = '')
    {

        $image_text = $request->image_text ?? '';
        if (!empty($image_text)) {
            $image_val = $image_text[0] ?? "";
            if (!empty($image_val)) {
                $banner->banner_img = $image_val;
                $banner->save();
            }
        }
        $file = $request->file('image');
        if ($file) {
            $path = 'banners';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $banner->banner_img = $uploaded_data;
                $banner->save();
            }
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = POS::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Order has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function update_pos_daily_cash(Request $request)
    {
        $date = $request->date ?? '';
        $store_id = $request->store_id ?? '';
        $today_balance = $request->today_balance ?? '';
        $exist = POSDailyCash::whereDate('date', $date)->first();
        if (empty($exist)) {
            $pos = new POSDailyCash();
            $pos->date = $date;
            $pos->today_balance = $today_balance;
            $pos->store_id = $store_id;
            $pos->save();
        }
        return back();

    }

    public function user_search(Request $request)
    {
        $search = $request->get('q', '');

        $users = User::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
            ->select('id', 'name')
            ->limit(20)
            ->get();

        return response()->json($users);
    }

    public function getFreebiesProduct(Request $request)
    {
        $cart_price = $request->cart_price ??0;
        $cart_price = (int)$cart_price;
        $freebees_product = DB::table('freebees_product')
            ->where('from_amount', '<=', $cart_price)
            ->where('to_amount', '>=', $cart_price)
            ->where('is_delete', 0)
            ->get();
        if (!empty($freebees_product)) {
            foreach ($freebees_product as $pro) {
                $product = Products::find($pro->product_id??'');
                $pro->product_name = $product->name ?? '';
                $pro->image = CustomHelper::getImageUrl('products',$product->image) ?? '';
            }
        }

        return response()->json($freebees_product);
    }

    public function getMembershipPlans(Request $request)
    {
        $subscription_plans = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->get();

        return response()->json($subscription_plans);
    }


}
