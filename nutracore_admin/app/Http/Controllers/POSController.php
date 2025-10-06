<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Offers;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\POS;
use App\Models\POSDailyCash;
use App\Models\Products;
use App\Models\SubscriptionPlans;
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
use App\Models\Subscriptions;
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
        $data = [];
        $order_status = $request->order_status ?? '';
        $search = $request->search ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $orderID = $request->orderID ?? '';
        $date = $request->date ?? '';
        $agent_id = $request->agent_id ?? '';
        $orders = Order::where('is_delete', 0)->where('order_from','POS')->orderBy('id', 'desc');
        if (!empty($order_status)) {
            $orders->where('status', $order_status);
        }
        if (!empty($search)) {
            $orders->where('id', $search);
        }
        if (!empty($vendor_id)) {
            $orders->where('vendor_id', $vendor_id);
        }
        if (!empty($agent_id)) {
            $orders->where('agent_id', $agent_id);
        }
        if (!empty($date)) {
            //            $orders->whereDate('delivery_date',$date);
            $orders->whereDate('created_at', $date);
        }
        if (!empty($orderID)) {
            $orders->where('id', $orderID);
        }
        $orders = $orders->paginate(30);
        $data['orders'] = $orders;
        return view('orders.index', $data);
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
        echo "<pre>";
        print_r(
            $request->toArray()
        );
        die;
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
        $cart_price = $request->cart_price ?? 0;
        $cart_price = (int)$cart_price;
        $freebees_product = DB::table('freebees_product')
        ->where('from_amount', '<=', $cart_price)
        ->where('to_amount', '>=', $cart_price)
        ->where('is_delete', 0)
        ->get();
        if (!empty($freebees_product)) {
            foreach ($freebees_product as $pro) {
                $product = Products::find($pro->product_id ?? '');
                $pro->product_name = $product->name ?? '';
                $pro->image = CustomHelper::getImageUrl('products', $product->image) ?? '';
            }
        }

        return response()->json($freebees_product);
    }

    public function getCoupons(Request $request)
    {

        $coupons = Offers::where('is_delete', 0)->get();

        return response()->json($coupons);
    }

    public function getFreebiesProductDetails(Request $request)
    {

        $freebees_product = DB::table('freebees_product')->where('id', $request->id)->first();
        $product = [];
        if (!empty($freebees_product)) {
            $product = Products::where('id', $freebees_product->product_id)->first();
        }


        return response()->json($product);
    }

    public function getMembershipPlans(Request $request)
    {
        $user_id = $request->user_id ?? null;
        if (empty($user_id)) {
            $cartValue['message'] = "User ID is required";
            return response()->json($cartValue, 200);
        }
        $user = User::where('id',$request->user_id)->first();
        $subscription_plansArr = [];
        if (CustomHelper::checkSubscription($user) == 0) {

            $subscription_plans = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->orderBy('duration', "ASC")->get();
            if(!empty($subscription_plans)){
                foreach($subscription_plans as $subs_plan){
                    if(!empty($subs_plan->max_applied_time)){
                        $exist_count = Subscriptions::where('user_id',$user_id)->where('subscription_id',$subs_plan->id)->count();
                        if($exist_count < $subs_plan->max_applied_time){
                            $subscription_plansArr[] = $subs_plan;
                        }
                    }else{
                        $subscription_plansArr[] = $subs_plan;
                    }
                }
            }
        }

        return response()->json($subscription_plansArr);
    }

    public function applyCoupon(Request $request)
    {
        $id = $request->id ?? null;
        $cart_total = $request->total_amount ?? null;
        $user_id = $request->user_id ?? null;

        $cartValue = [
            'result' => false,
            'message' => '',
        ];

        // ✅ Validation for required fields
        if (empty($user_id)) {
            $cartValue['message'] = "User ID is required";
            return response()->json($cartValue, 200);
        }

        if (empty($cart_total) || $cart_total <= 0) {
            $cartValue['message'] = "Cart total must be greater than 0";
            return response()->json($cartValue, 200);
        }

        // ✅ Get coupon
        $coupon = Offers::where('id', $id)->first();
        if (!$coupon) {
            $cartValue['message'] = "Invalid Coupon";
            return response()->json($cartValue, 200);
        }

        $coupon_code = $coupon->offer_code ?? '';
        $offers = Offers::where('offer_code', $coupon_code)
        ->where('is_active', 'Y')
        ->whereDate('end_date', '>=', date('Y-m-d'))
        ->first();

        if (empty($offers)) {
            $cartValue['message'] = "Coupon expired or inactive";
            return response()->json($cartValue, 200);
        }

        // ✅ check usage limit
        if (!empty($offers->no_of_times)) {
            $ordercount = Order::where('userID', $user_id)
            ->where('coupon_code', $offers->offer_code)
            ->count();

            if ((int)$ordercount >= (int)$offers->no_of_times) {
                $cartValue['message'] = "You have applied this coupon max times";
                return response()->json($cartValue, 400);
            }
        }

        // ✅ check min cart value
        if ((int)$cart_total < (int)$offers->min_cart_value) {
            $cartValue['message'] = "Minimum cart value required is " . $offers->min_cart_value;
            return response()->json($cartValue, 200);
        }

        // ✅ apply discount
        if ($offers->offer_type == 'FIXED') {
            $total_price = (int)$cart_total - (int)$offers->offer_value;
            $cartValue['total_price'] = max($total_price, 0);
            $cartValue['coupon_discount'] = (int)$offers->offer_value;
        }

        if ($offers->offer_type == 'PERCENTAGE') {
            $percent_val = ($cart_total * $offers->offer_value) / 100;
            if ($percent_val >= $offers->max_discount) {
                $percent_val = $offers->max_discount;
            }
            $total_price = (int)$cart_total - (int)$percent_val;
            $cartValue['total_price'] = max($total_price, 0);
            $cartValue['coupon_discount'] = (int)$percent_val;
        }

        $cartValue['coupon_code'] = $coupon_code;
        $cartValue['result'] = true;
        $cartValue['message'] = $coupon_code . " successfully applied";

        return response()->json($cartValue, 200);
    }

    public function send_redeem_nc_cash_otp(Request $request)
    {
        $user_name = "User";
        $mobile = $request->userPhone ?? '';
//        $code = $request->nc_cash_val ?? '';
        $otp = rand(1111,9999);
        User::updateOrCreate([
            'phone' => $mobile,
        ], [
            'otp' => $otp,
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"68df609a695f0d275a15d4f5\",\n  \"sender\": \"NUTRCR\",\n  \"mobiles\": \"91$mobile\",\n  \"var\": \"$otp\"}",
            CURLOPT_HTTPHEADER => [
                "authkey: 431621ABncLfiKpzo6875ff9bP1",
                "content-type: application/JSON"
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }
    public function verify_redeem_nc_cash_otp(Request $request)
    {
        $mobile = $request->userPhone ?? '';
        $nc_cash_otp = $request->nc_cash_otp ?? '';
        $success = false;
        $exist = User::where(['phone' => $mobile, 'otp' => $nc_cash_otp])->where('is_delete', 0)->first();
        if(!empty($exist)){
            $success = true;
        }
        return json_encode(['success'=>$success]);
    }

    public  function generateNextInvoiceNo()
    {
        // Get the last invoice number among non-deleted orders
        $lastOrder = Order::where('is_delete', 0)
        ->orderBy('id', 'desc')
        ->first();

        if ($lastOrder && $lastOrder->invoice_no) {
            // Extract the numeric part and increment
            $lastNumber = (int) substr($lastOrder->invoice_no, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1; // start from 1 if no previous orders
        }

        // Format as INV000001, INV000002, etc.
        $invoiceNo = 'INV' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $invoiceNo;
    }
    public function savePos(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'user_id'        => 'required|integer',
                'order_type'        => 'required',
                'subtotal'       => 'required|numeric',
                'payment_method' => 'required',
                'items'          => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.qty'        => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();
            $user = User::find($request->user_id);
            // Create Order
            $order = new Order();
            $order->userID          = $request->user_id;
            $order->vendor_id       = 0; // if vendor_id available, map it here
            $order->address_id      = 0; // if using address system, replace
            $order->delivery_type   = 'home_delivery'; // or pickup_store
            $order->customer_name   = $user->name??'Guest'; // replace with user table if needed
            $order->contact_no      = $request->userPhone ?? '';
            $order->house_no        = '';
            $order->apartment       = '';
            $order->landmark        = '';
            $order->location        = '';
            $order->latitude        = '';
            $order->longitude       = '';
            $order->coupon_code     = $request->coupon_code ?? '';
            $order->coupon_discount = $request->coupon_discount ?? 0;
            $order->delivery_charges = $request->delivery_charges ?? 0;
            $order->order_amount    = $request->subtotal;
            $order->total_amount    = $request->subtotal - ($request->coupon_discount ?? 0) + ($request->delivery_charges ?? 0);
            $order->payment_method  = $request->payment_method;
            $order->instruction     = '';
            $order->status          = 'DELIVERED';
            $order->freebees_id     = $request->freebie_id ?? null;
            $order->freebees_price  = 0;
            $order->invoice_no  = self::generateNextInvoiceNo();
            $order->unique_id  = Order::generateOrderId();

            $order->order_from  = 'POS';
            $order->subscription_id = $request->subscription_id ?? null;
            $order->wallet          =  0;
            $order->applied_cashback = $request->appliedncCash ?? 0;
            $order->flatDiscountValue = $request->flatDiscountValue ?? 0;
            $order->flat_discount_percent = $request->flat_discount_percent ?? 0;
            $order->order_type = $request->order_type ?? '';
            $order->payment_method_values = json_encode($request->payment_method_values)??'';
            $order->save();

            // Save Order Items
            foreach ($request->items as $item) {
                $orderItem = new OrderItems();
                $orderItem->order_id              = $order->id;
                $orderItem->product_id            = $item['product_id'];
                $orderItem->variant_id            = $item['variant_id'] ?? 0;
                $orderItem->qty                   = $item['qty'];
                $orderItem->price                 = $item['price'];
                $orderItem->net_price             = $item['net_price'];
                $orderItem->subscription_price    = $item['subscription_price'] ?? null;
                $orderItem->net_subscription_price = $item['net_subscription_price'] ?? null;
                $orderItem->status                = 'DELIVERED';
                $orderItem->save();
            }

            DB::commit();
            if(!empty($request->subscription_id)){
                $user = User::where('id', $request->user_id)->first();
                if (!empty($user)) {
                    $subscription_start = $user->subscription_start ?? '';
                    $subscription_end = $user->subscription_end ?? '';

                    $order->is_subscribe = 1;
                    $order->save();


                    $subscription_plans = SubscriptionPlans::where('id', $request->subscription_id)->first();
                    if (!empty($subscription_plans)) {
                        $duration = (int)$subscription_plans->duration ?? 0;
                        if (empty($subscription_start)) {
                            $subscription_start = date('Y-m-d');
                            $subscription_end = date('Y-m-d', strtotime("+" . $duration . " months", strtotime(date('Y-m-d'))));
                        } else {
                            $subscription_end = date('Y-m-d', strtotime("+" . $duration . " months", strtotime($subscription_end)));
                        }
                        $discount = $subscription_plans->max_discount ?? 0;
                        $total_discount = $user->total_discount + $discount;
                        User::where('id', $user->id)->update(['subscription_start' => $subscription_start, 'subscription_end' => $subscription_end, 'subscription_id' => $request->subscription_id, 'total_discount' => $total_discount]);
                        $subsc = new Subscriptions();
                        $txn_id =  'NCPOS'.rand(11111,9999999);
                        $subsc->user_id = $user->id ?? '';
                        $subsc->subscription_id = $request->subscription_id ?? '';
                        $subsc->txn_id = $txn_id ?? '';
                        $subsc->paid_status = 1;
                        $subsc->taken_by = "Self";
                        if (empty($subscription_start)) {
                            $start_date = date('Y-m-d');
                        } else {
                            $start_date = $subscription_end;
                        }
                        $subsc->start_date = $start_date;
                        $subsc->end_date = date('Y-m-d', strtotime("+" . $duration . " months", strtotime($start_date)));
                        $subsc->save();

                        $data = [];
                        $data['userID'] = $user->id ?? '';
                        $data['txn_no'] = $txn_id;
                        $data['amount'] = $subscription_plans->price ?? 0;
                        $data['type'] = 'DEBIT';
                        $data['note'] = 'Take Subscription';
                        $data['against_for'] = 'subscription';
                        $data['paid_by'] = 'user';
                        $data['orderID'] = 0;
                        CustomHelper::saveTransaction($data);
                    }
                }
            }

            $invoice_url = '';
            if($request->is_print == 1){
                $invoice_url = route('orders.generateInvoicePdf',['id'=>$order->id]);
            }
            $this->updateNCCashAfterOrder($order->id);

            return response()->json([
                'success'  => true,
                'order_id' => $order->id,
                'invoice_url' => $invoice_url,
                'message'  => 'Order saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }

    }

    public function updateNCCashAfterOrder($order_id)
    {
        $order = Order::find($order_id);
        if((int)$order->applied_cashback > 0){
            $user_data = User::find($order->userID);
            $new_wallet = (int)$user_data->cashback_wallet - (int)$order->applied_cashback;
            User::where('id', $user_data->id)->update(['cashback_wallet' => $new_wallet]);
            ///////Save Transaction Needed
            ////Save Transaction////
            $dbArray = [];
            $dbArray['userID'] = $user_data->id;
            $dbArray['type'] = 'DEBIT';
            $dbArray['amount'] = (int)$order->applied_cashback ?? 0;
            $dbArray['against_for'] = 'cashback_wallet';
            $dbArray['wallet_type'] = 'cashback_wallet';
            $dbArray['remarks'] = "Amount Debited From NC Cash";
            $transaction_id = Transaction::insertGetId($dbArray);
            Transaction::where('id', $transaction_id)->update(['txn_no' => "NC" . rand(111111, 9999999999)]);
        }

        if (!empty($order)) {
            $user_data = User::find($order->userID);
            $new_wallet = (int)$user_data->cashback_wallet - (int)$order->applied_cashback;
            User::where('id', $user_data->id)->update(['cashback_wallet' => $new_wallet]);
            ///////Save Transaction Needed
            ////Save Transaction////
            $dbArray = [];
            $dbArray['userID'] = $user_data->id;
            $dbArray['type'] = 'DEBIT';
            $dbArray['amount'] = (int)$order->applied_cashback ?? 0;
            $dbArray['against_for'] = 'cashback_wallet';
            $dbArray['wallet_type'] = 'cashback_wallet';
            $dbArray['remarks'] = "Amount Debited From NC Cash";
            $transaction_id = Transaction::insertGetId($dbArray);
            Transaction::where('id', $transaction_id)->update(['txn_no' => "NC" . rand(111111, 9999999999)]);
        }
    }

}
