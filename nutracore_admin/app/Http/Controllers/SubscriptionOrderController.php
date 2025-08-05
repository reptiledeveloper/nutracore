<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Banner;
use App\Models\Blocks;
use App\Models\Company;
use App\Models\GenerateSubscriptionOrder;
use App\Models\SubscriptionOrder;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;
use Yajra\DataTables\DataTables;


class SubscriptionOrderController extends Controller
{


    private string $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $subscription_orders = SubscriptionOrder::where('is_delete', 0)->latest();

        $subscription_orders = $subscription_orders->paginate(10);
        $data['orders'] = $subscription_orders;
        return view('subscription_orders.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $banner = '';
        if (is_numeric($id) && $id > 0) {
            $banner = Banner::find($id);
            if (empty($banner)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/banners');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/banners';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Banner has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Banner has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Banner';

        if (!empty($banner)) {
            $page_heading = 'Update Banner';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['banners'] = $banner;

        return view('banners.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';

        $admin = new Banner();

        if (is_numeric($id) && $id > 0) {
            $exist = Banner::find($id);
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
            $this->saveImage($request, $admin, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $banner, $oldImg = '')
    {

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
            $is_delete = SubscriptionOrder::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'SubscriptionOrder has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function update_subscription(Request $request)
    {
        $id = $request->id ?? '';
        $method = $request->method();
        $dbArray = [];
        if ($method == 'post' || $method == 'POST') {
            if (!empty($request->start_date)) {
                $dbArray['start_date'] = $request->start_date;
            }
            if (!empty($request->end_date)) {
                $dbArray['end_date'] = $request->end_date;
            }
            if (!empty($request->type)) {
                $dbArray['type'] = $request->type;
            }
            if (!empty($request->agent_id)) {
                $dbArray['agent_id'] = $request->agent_id;
            }
            SubscriptionOrder::where('id', $id)->update($dbArray);
            return back();
        }
        return back();
    }

    public function generate_subscription_order(Request $request)
    {

        $date = $request->date??'';
        if(!empty($date)){
            $subscription_orders = SubscriptionOrder::whereDate('start_date','<=',$date)->whereDate('end_date','>=',$date)->where('order_status',1)->get();
            if(!empty($subscription_orders)){
                foreach ($subscription_orders as $subscription_order){
                    if($subscription_order->type == 'daily'){
                        $this->saveDailyOrder($subscription_order,$date);
                    }
                    if($subscription_order->type == 'alternative'){
                        $this->saveAlternativeOrder($subscription_order,$date);
                    }
                    if($subscription_order->type == 'weekly'){
                        $this->saveWeeklyOrder($subscription_order, $date);
                    }
                }
            }
            return back();
        }

    }


    public function saveWeeklyOrder($subscription_order, $date)
    {

        $day_value = date('l', strtotime($date));
        $week_data = json_decode($subscription_order->subscription_data) ?? '';
        if (!empty($week_data)) {
            foreach ($week_data as $week_d) {

                if ($day_value == $week_d->day) {
                    if ((int)$week_d->qty > 0) {
                        $exist = GenerateSubscriptionOrder::where('subscription_id', $subscription_order->id)->where('date', $date)->first();
                        $dbArray = [];
                        $dbArray['date'] = $date;
                        $dbArray['product_id'] = $subscription_order->product_id ?? '';
                        $dbArray['varient_id'] = $subscription_order->varient_id ?? '';
                        $dbArray['seller_id'] = $subscription_order->seller_id ?? '';
                        $dbArray['unit'] = $subscription_order->unit ?? '';
                        $dbArray['unit_value'] = $subscription_order->unit_value ?? '';
                        $dbArray['subscription_price'] = $subscription_order->subscription_price ?? '';
                        $dbArray['qty'] = $week_d->qty;
                        $dbArray['selling_price'] = $subscription_order->selling_price;
                        $dbArray['mrp'] = $subscription_order->mrp;
                        $total_cart_price = (int)$week_d->qty * (int)$subscription_order->subscription_price;
                        $dbArray['total_price'] = $total_cart_price;
                        $dbArray['user_id'] = $subscription_order->user_id;
                        $dbArray['subscription_id'] = $subscription_order->id ?? '';
                        $dbArray['type'] = $subscription_order->type ?? '';
                        $dbArray['start_date'] = $subscription_order->start_date ?? '';
                        $dbArray['end_date'] = $subscription_order->end_date ?? '';
                        $dbArray['address_id'] = $subscription_order->address_id ?? '';
                        $dbArray['subscription_data'] = $subscription_order->subscription_data ?? '';
                        $dbArray['taken_subscription_id'] = $subscription_order->taken_subscription_id ?? '';
                        $dbArray['agent_id'] = $subscription_order->agent_id ?? '';
                        $dbArray['from_time'] = $subscription_order->from_time ?? '';
                        $dbArray['to_time'] = $subscription_order->to_time ?? '';
                        if (empty($exist)) {
                            GenerateSubscriptionOrder::insertGetId($dbArray);
                        } else {
                            GenerateSubscriptionOrder::where('id', $exist->id)->update($dbArray);
                        }
                    }
                }
            }
        }
    }

    public function saveAlternativeOrder($subscription_order,$date)
    {
        if ($subscription_order->isAlternateDay($date)) {
            $exist = GenerateSubscriptionOrder::where('subscription_id',$subscription_order->id)->where('date',$date)->first();
            $dbArray = [];
            $dbArray['date'] = $date;
            $dbArray['product_id'] = $subscription_order->product_id??'';
            $dbArray['varient_id'] = $subscription_order->varient_id??'';
            $dbArray['seller_id'] = $subscription_order->seller_id??'';
            $dbArray['unit'] = $subscription_order->unit ?? '';
            $dbArray['unit_value'] = $subscription_order->unit_value ?? '';
            $dbArray['subscription_price'] = $subscription_order->subscription_price ?? '';
            $dbArray['qty'] = $subscription_order->qty;
            $dbArray['selling_price'] = $subscription_order->selling_price;
            $dbArray['mrp'] = $subscription_order->mrp;
            $total_cart_price = (int)$subscription_order->qty * (int)$subscription_order->subscription_price;
            $dbArray['total_price'] = $total_cart_price;
            $dbArray['user_id'] = $subscription_order->user_id;
            $dbArray['subscription_id'] = $subscription_order->id ?? '';
            $dbArray['type'] = $subscription_order->type ?? '';
            $dbArray['start_date'] = $subscription_order->start_date ?? '';
            $dbArray['end_date'] = $subscription_order->end_date ?? '';
            $dbArray['address_id'] = $subscription_order->address_id ?? '';
            $dbArray['subscription_data'] = $subscription_order->subscription_data ?? '';
            $dbArray['taken_subscription_id'] = $subscription_order->taken_subscription_id ?? '';
            $dbArray['agent_id'] = $subscription_order->agent_id ?? '';
            $dbArray['from_time'] = $subscription_order->from_time ?? '';
            $dbArray['to_time'] = $subscription_order->to_time ?? '';
            if(empty($exist)){
                GenerateSubscriptionOrder::insertGetId($dbArray);
            }else{
                GenerateSubscriptionOrder::where('id',$exist->id)->update($dbArray);
            }
        }
    }
    public function saveDailyOrder($subscription_order,$date)
    {
        $exist = GenerateSubscriptionOrder::where('subscription_id',$subscription_order->id)->where('date',$date)->first();
        $dbArray = [];
        $dbArray['date'] = $date;
        $dbArray['product_id'] = $subscription_order->product_id??'';
        $dbArray['varient_id'] = $subscription_order->varient_id??'';
        $dbArray['seller_id'] = $subscription_order->seller_id??'';
        $dbArray['unit'] = $subscription_order->unit ?? '';
        $dbArray['unit_value'] = $subscription_order->unit_value ?? '';
        $dbArray['subscription_price'] = $subscription_order->subscription_price ?? '';
        $dbArray['qty'] = $subscription_order->qty;
        $dbArray['selling_price'] = $subscription_order->selling_price;
        $dbArray['mrp'] = $subscription_order->mrp;
        $total_cart_price = (int)$subscription_order->qty * (int)$subscription_order->subscription_price;
        $dbArray['total_price'] = $total_cart_price;
        $dbArray['user_id'] = $subscription_order->user_id;
        $dbArray['subscription_id'] = $subscription_order->id ?? '';
        $dbArray['type'] = $subscription_order->type ?? '';
        $dbArray['start_date'] = $subscription_order->start_date ?? '';
        $dbArray['end_date'] = $subscription_order->end_date ?? '';
        $dbArray['address_id'] = $subscription_order->address_id ?? '';
        $dbArray['subscription_data'] = $subscription_order->subscription_data ?? '';
        $dbArray['taken_subscription_id'] = $subscription_order->taken_subscription_id ?? '';
        $dbArray['agent_id'] = $subscription_order->agent_id ?? '';
        $dbArray['from_time'] = $subscription_order->from_time ?? '';
        $dbArray['to_time'] = $subscription_order->to_time ?? '';
        if(empty($exist)){
            GenerateSubscriptionOrder::insertGetId($dbArray);
        }else{
            GenerateSubscriptionOrder::where('id',$exist->id)->update($dbArray);
        }
    }

}
