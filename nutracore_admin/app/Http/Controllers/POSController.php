<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Expense;
use App\Models\Offers;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderStatus;
use App\Models\POS;
use App\Models\POSDailyCash;
use App\Models\POSDailyCashTransaction;
use App\Models\Products;
use App\Models\StockLog;
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
use Str;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class POSController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function add_expense(Request $request)
    {
        // 1️⃣ Validation
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'payment_method' => 'required|string|max:50',
            'store_id' => 'required|exists:vendors,id',
            'expense_date' => 'nullable|date',
            'id' => 'nullable|exists:expenses,id', // For edit
        ], [
            'amount.required' => 'Please enter the amount.',
            'payment_method.required' => 'Please select payment method.',
            'store_id.required' => 'Please select store.',
        ]);

        // 2️⃣ Determine if add or edit
        if ($request->id) {
            // Edit existing expense
            $expense = Expense::find($request->id);
            if (!$expense) {
                return back()->with('error', 'Expense not found.');
            }
        } else {
            // Add new expense
            $expense = new Expense();
            $expense->created_by = Auth::guard('admin')->user()->id ?? ""; // optional: track who added
        }

        // 3️⃣ Assign fields
        $expense->amount = $request->amount;
        $expense->category = $request->category;
        $expense->description = $request->description;
        $expense->payment_method = $request->payment_method;
        $expense->store_id = $request->store_id;
        $expense->expense_date = $request->expense_date ?? now()->toDateString();

        // 4️⃣ Save expense
        $expense->save();
        $date = date('Y-m-d');

        DB::table('pos_daily_cash_transaction')->insert(
            [
                'vendor_id' => $request->store_id ?? 0,
                'amount' => $request->amount ?? 0,
                'remarks' =>  $request->description??'',
                'date' => $date,
                'type' => 'debit',
                'status' => 1,
                'is_delete' => 0,
                'updated_at' => now(),
            ]
        );


        // 5️⃣ Return response
        return redirect()->back()->with('success', $request->id ? 'Expense updated successfully.' : 'Expense added successfully.');
    }

    public function delete_expense(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = Expense::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Expense has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
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
        $orders = Order::where('is_delete', 0)->where('order_from', 'POS')->orderBy('id', 'desc');
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


        $ordersData = Order::where('is_delete', 0)->where('order_from', 'POS')->get();
        foreach ($ordersData as $order) {
            self::update_pos_daily_cash_transaction($order);
        }


        $data['orders'] = $orders;
        return view('orders.index', $data);
    }

    public function cash_management(Request $request)
    {
        $data = [];
        $pos_daily_cash = POSDailyCash::latest();

        $pos_daily_cash = $pos_daily_cash->paginate(30);
        $data['pos_daily_cash'] = $pos_daily_cash;
        return view('pos.cash_management', $data);
    }

    public function cash_transactions(Request $request)
    {

        $startDate = Carbon::now()->subMonths(3)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $startDate = $request->start_date ?? $startDate;
        $endDate = $request->end_date ?? $endDate;
        // Get all active records grouped by date and vendor
        $records = POSDailyCashTransaction::select(
            'date',
            'vendor_id',
            DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as total_sales"),
            DB::raw("SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) as total_expense"),
            DB::raw('COUNT(id) as total_transactions')
        )
            ->where('is_delete', 0)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date', 'vendor_id')
            ->orderByDesc('date')
            ->get();

        // Group by date for Blade looping
        $daily_cash_transactions = $records->groupBy('date');
        // Group by date for Blade looping
        $daily_cash_transactions = $records->groupBy('date');

        return view('pos.cash_transactions', compact('daily_cash_transactions'));
    }

    public function expense(Request $request)
    {
        $data = [];
        $expenses = Expense::where('is_delete', 0)->latest();

        $expenses = $expenses->paginate(30);
        $data['expenses'] = $expenses;
        return view('pos.expenses', $data);
    }

    public function credit_note(Request $request)
    {
        $data = [];
        $order_status = $request->order_status ?? '';
        $search = $request->search ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $orderID = $request->orderID ?? '';
        $date = $request->date ?? '';
        $agent_id = $request->agent_id ?? '';
        $orders = Order::where('is_delete', 0)->where('order_from', 'POS')->where('status', 'CANCEL')->orderBy('id', 'desc');
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
        $data['create_new_cancel'] = "1";
        return view('orders.index', $data);
    }

    public function cancel_order(Request $request)
    {
        $data = [];
        $orders = [];
        $invoice_no = $request->invoice_no ?? '';
        if (!empty($invoice_no)) {
            $orders = Order::where('invoice_no', $invoice_no)->first();
        }


        $data['orders'] = $orders;
        return view('pos.cancel_order', $data);
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
        $date = $request->date ?? now()->toDateString();
        $store_id = $request->store_id ?? '';
        $today_balance = $request->today_balance ?? 0;

        // Generate unique session ID for this opening
        $session_id = Str::uuid(); // Use Illuminate\Support\Str

        $pos = new POSDailyCash();
        $pos->date = $date;
        $pos->store_id = $store_id;
        $pos->session_id = $session_id;
        $pos->updated_by = Auth::guard('admin')->user()->id ?? '';
        $pos->today_balance = $today_balance;
        $pos->save();

        // Store current session in session for easy reference
        session(['store_id' => $store_id, 'pos_session_id' => $session_id]);

        return back();
    }


    public function close(Request $request)
    {
        $request->validate([
            'today_last_balance' => 'required|numeric',
            'store_id' => 'required',
            'closing_note' => 'required',
            'session_id' => 'required',
        ], [
            'today_last_balance.required' => 'Please enter the physical drawer amount.',
        ]);

        $store_id = $request->store_id;
        $session_id = $request->session_id;
        $date = $request->date??date('Y-m-d');

        // Find the specific open session
        $pos = POSDailyCash::where('store_id', $store_id)
            ->where('date', $date)
            ->first();

        if (!$pos) {
            return response()->json([
                'status' => false,
                'message' => 'POS session not found.',
            ], 404);
        }

        $pos->today_last_balance = $request->today_last_balance;
        $pos->closing_note = $request->closing_note ?? null;
        $pos->updated_by = Auth::guard('admin')->user()->id ?? '';
        $pos->save();
        session(['store_id' => "", 'pos_session_id' => ""]);
        return back();
    }


    public
    function user_search(Request $request)
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

    public
    function getFreebiesProduct(Request $request)
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

    public
    function getCoupons(Request $request)
    {

        $coupons = Offers::where('is_delete', 0)->get();

        return response()->json($coupons);
    }

    public
    function getFreebiesProductDetails(Request $request)
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
        $user = User::where('id', $request->user_id)->first();
        $subscription_plansArr = [];
        if (CustomHelper::checkSubscription($user) == 0) {
            $subscription_plans = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->orderBy('duration', "ASC")->get();
            if (!empty($subscription_plans)) {
                foreach ($subscription_plans as $subs_plan) {
                    if (!empty($subs_plan->max_applied_time)) {
                        $exist_count = Subscriptions::where('user_id', $user_id)->where('subscription_id', $subs_plan->id)->count();
                        if ($exist_count < $subs_plan->max_applied_time) {
                            $subscription_plansArr[] = $subs_plan;
                        }
                    } else {
                        $subscription_plansArr[] = $subs_plan;
                    }
                }
            }
        }

        return response()->json($subscription_plansArr);
    }

    public
    function applyCoupon(Request $request)
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

    public
    function send_redeem_nc_cash_otp(Request $request)
    {
        $user_name = "User";
        $mobile = $request->userPhone ?? '';
//        $code = $request->nc_cash_val ?? '';
        $otp = rand(1111, 9999);
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

    public
    function verify_redeem_nc_cash_otp(Request $request)
    {
        $mobile = $request->userPhone ?? '';
        $nc_cash_otp = $request->nc_cash_otp ?? '';
        $success = false;
        $exist = User::where(['phone' => $mobile, 'otp' => $nc_cash_otp])->where('is_delete', 0)->first();
        if (!empty($exist)) {
            $success = true;
        }
        return json_encode(['success' => $success]);
    }

    public
    function generateNextInvoiceNo()
    {
        // Get the last invoice number among non-deleted orders
        $lastOrder = Order::where('is_delete', 0)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && $lastOrder->invoice_no) {
            // Extract the numeric part and increment
            $lastNumber = (int)substr($lastOrder->invoice_no, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1; // start from 1 if no previous orders
        }

        // Format as INV000001, INV000002, etc.
        $invoiceNo = 'INV' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $invoiceNo;
    }

    public
    function savePos(Request $request)
    {

        $lockKey = 'pos_lock_user_' . $request->user_id;
        $lock = Cache::lock($lockKey, 10); // 5 seconds lock

        if (!$lock->get()) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a few seconds before submitting again.'
            ], 200); // Too Many Requests
        }

        try {
            // Validate required fields
            $request->validate([
                'vendor_id' => 'required',
                'user_id' => 'required|integer',
                'order_type' => 'required',
                'subtotal' => 'required|numeric',
                'payment_method' => 'required',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer',
                'items.*.qty' => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();
            $user = User::find($request->user_id);
            // Create Order
            $order_status = 'PLACED';
            if ($request->order_type == 'walk_in') {
                $order_status = 'DELIVERED';
            }
            $order = new Order();
            $order->userID = $request->user_id;
            $order->vendor_id = $request->vendor_id ?? ''; // if vendor_id available, map it here
            $order->address_id = 0; // if using address system, replace
            $order->delivery_type = 'home_delivery'; // or pickup_store
            $order->customer_name = $user->name ?? 'Guest'; // replace with user table if needed
            $order->contact_no = $request->userPhone ?? '';
            $order->house_no = '';
            $order->apartment = '';
            $order->landmark = '';
            $order->location = '';
            $order->latitude = '';
            $order->longitude = '';
            $order->coupon_code = $request->coupon_code ?? '';
            $order->coupon_discount = $request->coupon_discount ?? 0;
            $order->delivery_charges = $request->delivery_charges ?? 0;
            $order->order_amount = $request->subtotal;
            $order->total_amount = $request->subtotal - ($request->coupon_discount ?? 0) + ($request->delivery_charges ?? 0);
            $order->payment_method = $request->payment_method;
            $order->delivery_date = date('Y-m-d');
            $order->instruction = '';
            $order->status = $order_status;
            $order->freebees_id = $request->freebie_id ?? null;
            $order->freebees_price = 0;
            $order->invoice_no = self::generateNextInvoiceNo();
            $order->created_by = Auth::guard('admin')->user()->id??'';
            $order->unique_id = Order::generateOrderId();

            $order->order_from = 'POS';
            $order->subscription_id = $request->subscription_id ?? null;
            $order->wallet = 0;
            $order->applied_cashback = $request->appliedncCash ?? 0;
            $order->flatDiscountValue = $request->flatDiscountValue ?? 0;
            $order->flat_discount_percent = $request->flat_discount_percent ?? 0;
            $order->order_type = $request->order_type ?? '';
            $order->payment_method_values = $request->payment_method_values ?? '';
            $order->is_subscribe = CustomHelper::checkSubscription($user);
            $order->save();

            // Save Order Items
            foreach ($request->items as $item) {
                $orderItem = new OrderItems();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->variant_id = $item['variant_id'] ?? 0;
                $orderItem->qty = $item['qty'];
                $orderItem->price = $item['price'];
                $orderItem->discount = $item['discount']??0;
                $orderItem->mrp = $item['mrp']??0;
                $orderItem->net_price = $item['net_price'];
                $orderItem->subscription_price = $item['subscription_price'] ?? null;
                $orderItem->net_subscription_price = $item['net_subscription_price'] ?? null;
                $orderItem->status = 'DELIVERED';
                $orderItem->save();
            }

            DB::commit();
            if (!empty($request->subscription_id)) {
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
                        $txn_id = 'NCPOS' . rand(11111, 9999999);
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
            if ($request->is_print == 1) {
                $invoice_url = route('orders.generateInvoicePdf', ['id' => $order->id]);
            }
            $this->updateNCCashAfterOrder($order->id);
            $this->updateStock($order->id);

            $order_data = Order::find($order->id);
            $this->creditNcCash($order_data);
            CustomHelper::sendInvoiceWP($user, $order_data);

            self::update_pos_daily_cash_transaction($order);


            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'invoice_url' => $invoice_url,
                'message' => 'Order saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }

    public function update_pos_daily_cash_transaction($order)
    {
        $date = date('Y-m-d', strtotime($order->created_at));
        $vendorId = $order->vendor_id;
        $paymentMethod = strtolower($order->payment_method);
        $is_delete = $order->is_delete ?? 0;
        if ($paymentMethod === 'cod') {
            DB::table('pos_daily_cash_transaction')->updateOrInsert(
                ['order_id' => $order->id], // condition (unique by order_id)
                [
                    'vendor_id' => $vendorId,
                    'amount' => $order->total_amount,
                    'date' => $date,
                    'type' => 'credit',
                    'status' => 1,
                    'is_delete' => $is_delete,
                    'updated_at' => now(),
                ]
            );
        }
        if ($paymentMethod === 'multipay') {
            $payment_method_values = json_decode($order->payment_method_values);
            if (!empty($payment_method_values->cash) && (float)$payment_method_values->cash > 0) {
                DB::table('pos_daily_cash_transaction')->updateOrInsert(
                    ['order_id' => $order->id],
                    [
                        'vendor_id' => $vendorId,
                        'amount' => $payment_method_values->cash,
                        'date' => $date,
                        'type' => 'credit',
                        'status' => 1,
                        'is_delete' => $is_delete,
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }


    public
    function updateStock($order_id)
    {
        $order = Order::find($order_id);
        if (!empty($order)) {
            $order_items = OrderItems::where('order_id', $order_id)->get();
            if (!empty($order_items)) {
                foreach ($order_items as $order_item) {
                    $product_id = $order_item->product_id ?? '';
                    $variant_id = $order_item->variant_id ?? '';
                    $qty = $order_item->qty ?? '';
                    $exist = DB::table('stock_batches')->where('product_id', $product_id);
                    if (!empty($variant_id)) {
                        $exist->where('variant_id', $variant_id);
                    }
                    $exist = $exist->where('quantity', '>', 0)->orderBy('mfg_date', 'ASC')->first();
                    if (!empty($exist)) {
                        if ((int)$exist->quantity <= (int)$qty) {
                            $new_qty = (int)$exist->quantity - (int)$qty;
                            DB::table('stock_batches')->where('id', $exist->id)->update(['quantity' => $new_qty]);
                            StockLog::create([
                                'product_id' => $product_id,
                                'variant_id' => $variant_id,
                                'store_id' => $exist->store_id ?? '',
                                'action' => "sale",
                                'quantity' => $qty,
                                'closing_stock' => $new_qty,
                                'related_id' => 0,
                                'related_type' => "Sale",
                                'created_by' => auth()->id(),
                                'order_id' => $order_id,
                            ]);
                        } else {

                        }
                    }
                }
            }
        }

    }

    public
    function updateNCCashAfterOrder($order_id)
    {
        $order = Order::find($order_id);
        if ((int)$order->applied_cashback > 0) {
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

    public
    function creditNcCash($order)
    {
        $user = User::find($order->userID);
        $amount = self::getNcCashPercent($user, $order->order_amount ?? '');
        $cashback_wallet = $user->cashback_wallet ?? 0;
        $new_wallet = (int)$cashback_wallet + (int)$amount;
        $user->cashback_wallet = $new_wallet;
        $user->save();
        $order->nc_cash_earned = $amount;
        $order->save();
        $dbArray1 = [];
        $dbArray1['userID'] = $user->id;
        $dbArray1['txn_no'] = "NC" . rand(1111, 9999999);
        $dbArray1['amount'] = $amount;
        $dbArray1['wallet_type'] = "cashback_wallet";
        $dbArray1['type'] = "CREDIT";
        $dbArray1['note'] = "Earn NC Cash From Order " . $order->id ?? '';
        $dbArray1['against_for'] = 'cashback_wallet';
        $dbArray1['paid_by'] = 'order';
        $dbArray1['orderID'] = 0;
        CustomHelper::SaveTransaction($dbArray1);
    }

    public
    function getNcCashPercent($user, $amount)
    {
        $is_active = 0;

        $subscription_end_date = '';
        if (!empty($user)) {
            $exist_subscription = Subscriptions::where('user_id', $user->id)->where('paid_status', 1)->latest()->first();
            if (!empty($exist_subscription)) {
                $current_date = date('Y-m-d');
                if (strtotime($user->subscription_end) >= strtotime($current_date)) {
                    $is_active = 1;
                }
            }
        }

        $type = ($is_active == 1) ? 'subscribe' : 'not_subscribe';
        \DB::enableQueryLog(); // Enable query log

        $total_order_amount = Order::where('userID', $user->id)->where('status', 'DELIVERED')->sum('total_amount');
        $active_loyalty = DB::table('loyality_system')
            ->where('status', 1)
            ->where('is_delete', 0)
            ->where('type', $type)
            ->where('from_amount', '<=', $total_order_amount)
            ->where(function ($q) use ($total_order_amount) {
                $q->where('to_amount', '>=', $total_order_amount)
                    ->orWhereNull('to_amount'); // for open-ended slabs like Platinum
            })
            ->orderBy('from_amount', 'desc') // pick the highest matching tier
            ->first();
        if (!empty($active_loyalty)) {
            return round(((int)$amount * (int)$active_loyalty->cashback) / 100);
        }
        return 0;

    }

    public function cancel_order_save(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'pos_cancel_type' => 'required|string',
            'pos_cancel_remarks' => 'required|string',
        ], [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'Invalid Order ID.',
            'pos_cancel_type.required' => 'Cancel type is required.',
            'pos_cancel_remarks.required' => 'Cancel remarks are required.',
        ]);

        // 2️⃣ Retrieve order
        $order = \App\Models\Order::find($request->order_id);

        if (empty($order->pos_cancel_type)) {
            $refund_amount = 0;
            $order_items = OrderItems::where('order_id', $request->order_id)->where('status', '!=', 'CANCEL')->get();
            if (!empty($order_items)) {
                foreach ($order_items as $order_item) {
                    $qty = $order_item->qty ?? 0;
                    $price = $order_item->price ?? 0;
                    $subscription_price = $order_item->subscription_price ?? 0;
                    if ($order->is_subscribe == 1) {
                        $refund_amount += ($qty * $subscription_price);
                    } else {
                        $refund_amount += ($qty * $price);
                    }
                }
            }
            // 3️⃣ Update order fields
            $order->pos_cancel_type = $request->pos_cancel_type;
            $order->pos_cancel_remarks = $request->pos_cancel_remarks;
            $order->pos_cancelled_at = now();
            $order->status = 'CANCEL';
            $order->refund_amount = $refund_amount;
            $order->is_refund = 1;
            $order->save();

            $user = User::where('id', $order->userID)->first();
            $new_credit_balance = $user->credit_balance + $refund_amount;
            $user->credit_balance = $new_credit_balance;
            $user->save();
            $dbArray = [];
            $dbArray['userID'] = $order->userID ?? '';
            $dbArray['type'] = 'CREDIT';
            $dbArray['amount'] = (int)$refund_amount ?? 0;
            $dbArray['against_for'] = 'credit_balance';
            $dbArray['wallet_type'] = 'credit_balance';
            $dbArray['remarks'] = "Amount Credited From POS Order";
            $dbArray['note'] = "Amount Credited From POS Order";
            $dbArray['orderID'] = $order->id;
            $transaction_id = Transaction::insertGetId($dbArray);
            Transaction::where('id', $transaction_id)->update(['txn_no' => "NC" . rand(111111, 9999999999)]);

            OrderItems::where('order_id', $request->order_id)->update(['status' => 'CANCEL']);

            $dbArray = [];
            $dbArray['order_id'] = $request->order_id;
            $dbArray['status'] = 'CANCEL';
            $dbArray['updated_by'] = 'admin_' . Auth::guard('admin')->user()->id ?? '';
            OrderStatus::where('order_id', $request->order_id)->insert($dbArray);
        }


        return back();
    }

}
