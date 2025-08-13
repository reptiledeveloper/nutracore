<?php

namespace App\Http\Controllers\V1;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\CancelledSubscription;
use App\Models\FAQ;
use App\Models\GenerateSubscriptionOrder;
use App\Models\Product;
use App\Models\RazorpayOrders;
use App\Models\Order;
use App\Models\Setting;
use App\Models\SubscriptionCart;
use App\Models\SubscriptionOrder;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use App\Models\User;
use App\Models\UserAddress;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;


class SubscriptionController extends Controller
{

    public User $user;
    public mixed $url;

    public function __construct()
    {
        $this->user = new User;
        date_default_timezone_set("Asia/Kolkata");
        $this->url = env('BASE_URL');
    }

    public function subscriptions(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [

        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $subscriptionsArr = null;
        $seller_id = $request->seller_id ?? '';
        $subscription_plans = SubscriptionPlans::where('is_delete', 0)->where('status', 1)->get();
         $minPricePerDay = PHP_FLOAT_MAX;
        $bestValuePlanId = null;
// First pass: Find plan with best price per day
foreach ($subscription_plans as $plan) {
    // Assume duration is in days. If months, convert to days.
    $durationInDays = $plan->duration * 30.44;

    if ($durationInDays > 0) {
        $pricePerDay = (int)$plan->price / $durationInDays;

        if ($pricePerDay < $minPricePerDay) {
            $minPricePerDay = $pricePerDay;
            $bestValuePlanId = $plan->id;
        }
    }
}

        if (!empty($subscription_plans)) {
            foreach ($subscription_plans as $plan) {
                $original_price = $plan->mrp ?? 0;
                $discounted_price = $plan->price ?? 0;
                $is_best_value = 0;
                if($plan->id == $bestValuePlanId){
                    $is_best_value = 1;
                }
                $plan->is_best_value = $is_best_value;
                $discount_percentage = (($original_price - $discounted_price) / $original_price) * 100;
                $plan->discount = round($discount_percentage);
            }
        }

        $subscriptionsArr['subscription_plans'] = $subscription_plans;
        $prime_benifitArr = [];
        $settings = Setting::find(1);
        $prime_benifits = json_decode($settings->prime_benifits) ?? '';
        if (!empty($prime_benifits)) {
            foreach ($prime_benifits as $prime_benifit) {
                $prime_benifit->icon = url('/public/assets/settings/' . $prime_benifit->icon);
                $prime_benifitArr[] = $prime_benifit;
            }
        }

        $faqs = FAQ::where('type', 'subscription')->where('is_delete',0)->get();
        $subscriptionsArr['prime_benifits'] = $prime_benifitArr;
        $subscriptionsArr['faqs'] = $faqs;


        $user_subscription = [];
        $subscribed_subscription = null;
        $is_prev_subscribed = 0;
        $is_active = 0;
        $subscription_end_date = '';
        $exist_subscription = Subscriptions::where('user_id', $user->id)->where('paid_status', 1)->latest()->first();
        if (!empty($exist_subscription)) {
            $is_prev_subscribed = 1;
            $current_date = date('Y-m-d');
            $subscribed_subscription = SubscriptionPlans::find($exist_subscription->subscription_id);
            if (strtotime($exist_subscription->end_date) >= strtotime($current_date)) {
                $is_active = 1;
                $subscription_end_date = $exist_subscription->end_date ?? '';
                if (!empty($subscription_end_date)) {
                    $subscription_end_date = date('d M Y', strtotime($subscription_end_date));
                }
            }
        }

        $banners = Banner::where('status', 1)->where('is_delete', 0)->where('type', 'subscription')->get()->makeHidden(['created_at', 'updated_at', 'is_delete', 'status']);
        if (!empty($banners)) {
            foreach ($banners as $banner) {
                $banner->banner_img = CustomHelper::getImageUrl('banners', $banner->banner_img);
            }
        }
        $subscription_order_details = [];
        // $subscription_order = SubscriptionOrder::where('user_id',$user->id)->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'))->where('order_status',1)->get();
        $subscription_order = SubscriptionOrder::where('user_id', $user->id)->where('order_status', 1)->get();
        if (!empty($subscription_order)) {
            foreach ($subscription_order as $subscription_ord) {
                $product = DB::table('products')->where('id', $subscription_ord->product_id)->first();
                $subscription_ord->product_name = $product->name ?? '';
                $subscription_ord->product_image = CustomHelper::getImageUrl('products', $product->image ?? '');
            }
        }
        $saved_amount = 0;
        $user_subscription['is_prev_subscribed'] = $is_prev_subscribed;
        $user_subscription['is_active'] = $is_active;
        $user_subscription['subscription_end_date'] = $subscription_end_date;
        $user_subscription['subscribed_subscription'] = $subscribed_subscription;
        $user_subscription['saved_amount'] = $saved_amount;
        $subscriptionsArr['user_subscription'] = $user_subscription;
        $subscriptionsArr['banners'] = $banners;
        $subscriptionsArr['subscription_order_details'] = $subscription_order_details;
        $subscriptionsArr['subscription_order'] = $subscription_order;
        $subscriptionsArr['subscription_descrpiption'] = '🔥  10% OFF every order <br>
                                            🚚  Free Express Delivery <br>
                                            🎁  Monthly Freebie Box <br>
                                            ⏰  Early Access & Secret Sales';
        $tire_system = [];
        $type = ($is_active == 1 )? 'subscribe' : 'not_subscribe';
        $total_order_amount = Order::where('userID', $user->id)->where('status', 'DELIVERED')->sum('total_amount');
        $active_loyalty = DB::table('loyality_system')
            ->where('status', 1)
            ->where('is_delete',0)
            ->where('type', $type)
            ->where('from_amount', '<=', $total_order_amount)
            ->where('to_amount', '>=', $total_order_amount)
            ->first();
        $tire_system = DB::table('loyality_system')
            ->where('status', 1)
            ->where('is_delete',0)
            ->where('type', $type)->get();

        $subscriptionsArr['tire_system'] = $tire_system;
        $subscriptionsArr['active_loyalty'] = $active_loyalty;

        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'subscriptions' => $subscriptionsArr,
        ], 200);
    }


    public function demo_api(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [

        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }

        return response()->json([
            'result' => true,
            'message' => "Successfully",
        ], 200);
    }

    public function loyality_points(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [

        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $loyality_points = DB::table('loyality_points')->where('status', 1)->get();
        return response()->json([
            'result' => true,
            'message' => "Successfully",
            "loyality_points" => $loyality_points
        ], 200);
    }

    public function take_subscription(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "subscription_id" => "required"
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }

        $subscription_id = $request->subscription_id ?? '';
        $subscription_plans = SubscriptionPlans::where('id', $subscription_id)->first();
        if (empty($subscription_plans)) {
            return response()->json([
                'result' => false,
                'message' => "Subscription Not Exist",
            ], 200);
        }
        $order_id = "";
        $amount = $subscription_plans->price ?? 0;
        $orders = $this->generateRazorpayOrder($amount, $user->id);
        if (!empty($orders)) {
            if (empty($orders->error)) {
                $order_id = $orders->id;
                $dbArray = [];
                $dbArray['user_id'] = $user->id;
                $dbArray['subscription_id'] = $request->subscription_id;
                $dbArray['amount'] = $amount;
                $dbArray['wallet'] = 0;
                $dbArray['type'] = "subscription";
                $dbArray['payment_status'] = 0;
                $dbArray['razorpay_order_id'] = $order_id;
                RazorpayOrders::insert($dbArray);
            }
        }


        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'order_id' => $order_id,
            'image' => url('public/assets/images/logo.png'),
            'keys' => CustomHelper::getRazorpayKeys(),
            'orders' => $orders,
        ], 200);
    }

    private function generateRazorpayOrder($price, $user_id)
    {
        $payment_data = [
            "amount" => (int) $price * 100,
            "currency" => "INR",
            'payment_capture' => 1,
            "notes" => [
                "user_id" => $user_id
            ]
        ];
        $settings = CustomHelper::getRazorpayKeys();
        $key = $settings['key'] ?? '';
        $secret = $settings['secret'] ?? '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payment_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($key . ':' . $secret)
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function subscription_products(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [

        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }

        return response()->json([
            'result' => true,
            'message' => "Successfully",
        ], 200);
    }

    public function getCalenderData(Request $request)
    {

        $type = $request->type ?? '';
        $order_id = $request->order_id ?? '';
        $user_id = $request->user_id ?? '';
        $calenderData = [];
        $currentYear = $request->currentYear ?? date('Y');
        $currentMonth = $request->currentMonth ?? date('m');
        $select_date = $request->startdate ?? "";
        $type = $request->type ?? "";
        $week_data = $request->week_data ?? "";
        $month_no = CustomHelper::getSettingKey('subscription_month');

        $number = $month_no ?? 2;
        $calenderArr = [];
        $currentDate = new DateTime();
        $calenderDataArrArr = [];
        $order = [];
        if (!empty($order_id)) {
            $order = SubscriptionOrder::find($order_id);
        }

        for ($i = 0; $i < $number; $i++) {
            $year = $currentDate->format('Y');
            $month = $currentDate->format('m');
            $dateObj = new DateTime();
            $dateObj->setDate($year, $month, 1);
            $dateObj->modify("+$i month");
            $date = $dateObj->format('Y-m-d');
            $currentMonth = $dateObj->format('m');
            $currentYear = $dateObj->format('Y');
            $calenderData = self::generateCalendar($currentYear, $currentMonth, $type, $select_date, $week_data);
            $calenderArr['month'] = $dateObj->format('F');
            $calenderArr['year'] = $dateObj->format('Y');
            $dbArray = [];
            if (!empty($calenderData)) {
                foreach ($calenderData as $calender) {
                    $is_cancel = 0;
                    $is_cancel = 0;
                    $exist = CancelledSubscription::where('user_id', $user_id)->where('subs_order_id', $order_id)->whereDate('date', $calender['date'])->first();
                    if (!empty($exist)) {
                        $is_cancel = 1;
                        $calender['is_selected'] = 0;
                    }
                    if (!empty($order->end_date) && !empty($calender['date'])) {
                        if (strtotime($order->end_date) < strtotime($calender['date'])) {
                            $calender['is_selected'] = 0;
                        }
                    }
                    $generate_subscription_orders = GenerateSubscriptionOrder::find($order_id);
                    $calender['is_cancel'] = $is_cancel;
                    $calender['generate_subscription_orders'] = $generate_subscription_orders;
                    $dbArray[] = $calender;
                }
            }
            $calenderArr['calenderDataMonth'] = $dbArray;
            $calenderDataArrArr[] = $calenderArr;
        }
        /////Current Month
        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'calenderData' => $calenderDataArrArr,
        ], 200);
    }


    public function generateCalendar($year, $month, $type = "", $select_date = "", $week_data = ""): array
    {

        $calendarArr = [];
        $firstDay = new DateTime("$year-$month-01");
        $numDays = (int) $firstDay->format('t');
        $startDayOfWeek = (int) $firstDay->format('N');
        $array = json_decode($week_data, true);
        $selectedDays = [];
        if (!empty($array)) {
            foreach ($array as $week_v) {
                if ((int) $week_v['qty'] > 0) {
                    $selectedDays[] = $week_v['day'];
                }
            }
        }
        $day = 1;
        $week = 1;
        $total_qty = 0;
        $alternate = 1;
        while ($day <= $numDays) {
            $dbArray = [];
            for ($i = 1; $i <= 7; $i++) {
                if (($day == 1 && $i < $startDayOfWeek) || $day > $numDays) {
                    $dbArray['day'] = "";
                    $dbArray['day_value'] = "";
                    $dbArray['date'] = "";
                    $dbArray['is_disabled'] = 1;
                    $dbArray['is_selected'] = 0;

                } else {
                    // Display the day
                    $date = $year . "-" . $month . "-" . $day;
                    $date_str = date('Y-m-d', strtotime($date));
                    $dbArray['date'] = date('Y-m-d', strtotime($date));
                    $dbArray['day'] = $day;
                    $is_diabled = 0;
                    $is_selected = 0;

                    $current_time = date('H:i:s');
                    if (strtotime(date('Y-m-d')) >= strtotime($date_str)) {
                        $is_diabled = 1;
                    }
                    if ($type == 'one_time') {
                        if (!empty($select_date)) {
                            if (strtotime($select_date) == strtotime($date_str)) {
                                $is_selected = 1;
                            }
                        }

                    }
                    if ($type == 'daily') {
                        if (!empty($select_date)) {
                            if (strtotime($select_date) <= strtotime($date_str)) {
                                $is_selected = 1;
                            }
                        }
                    }
                    if ($type == 'alternative') {
                        if (!empty($select_date)) {
                            $date_val = date('d', strtotime($select_date));
                            if ($date_val % 2 == 0) {
                                if ($alternate % 2 == 0) {
                                    if (strtotime($select_date) <= strtotime($date_str)) {
                                        $is_selected = 1;
                                    }
                                }
                            } else {
                                if (($alternate + 1) % 2 == 0) {
                                    if (strtotime($select_date) <= strtotime($date_str)) {
                                        $is_selected = 1;
                                    }
                                }
                            }
                        }
                        $alternate++;
                    }
                    if ($type == 'weekly') {
                        $day_name = date("l", strtotime($date_str));
                        $total_qty = 0;
                        $array = json_decode($week_data, true);
                        if (!empty($array)) {
                            foreach ($array as $key => $week_v) {
                                if ($week_v['day'] == $day_name && $week_v['qty'] > 0) {
                                    //                                    $is_selected = 1;
                                    $total_qty += $week_v['qty'];
                                }
                            }
                        }
                        if (in_array($day_name, $selectedDays)) {
                            if (strtotime($select_date) <= strtotime($date_str)) {
                                $is_selected = 1;
                            }
                        }
                    }
                    $dbArray['total_qty'] = $total_qty;
                    $dbArray['is_disabled'] = $is_diabled;
                    $dbArray['alternate'] = $alternate;
                    $dbArray['is_selected'] = $is_selected;
                    $dbArray['day_value'] = date('D', strtotime($date));
                    $day++;

                }
                $calendarArr[] = $dbArray;
            }
            $week++;
        }

        return $calendarArr;
    }

    public function update_cart_subscription(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'variant_id' => 'required',
            'qty' => '',
            'type' => 'required',
            //            'start_date' => 'required',
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $product_id = $request->product_id ?? '';
        $variant_id = $request->variant_id ?? '';
        $qty = $request->qty ?? '';
        $product = Product::where('id', $product_id)->first();
        $seller_id = $request->seller_id ?? $user->seller_id ?? '';
        if (empty($seller_id)) {
            return response()->json([
                'result' => false,
                'message' => 'Please Choose Seller',
            ], 200);
        }

        if (!empty($product)) {
            $check_varient = CustomHelper::checkVendorPrice($seller_id, $product_id, $variant_id);
            if (empty($check_varient)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Product Not Available',
                ], 200);
            }
            $exist = SubscriptionCart::where(['user_id' => $user->id])->first();
            $dbArray = [];
            $dbArray['product_id'] = $product_id;
            $dbArray['seller_id'] = $seller_id;
            $dbArray['variant_id'] = $variant_id;
            $dbArray['type'] = $request->type ?? '';
            $dbArray['start_date'] = $request->start_date ?? null;
            $dbArray['end_date'] = $request->end_date ?? null;
            $dbArray['user_id'] = $user->id;
            $dbArray['qty'] = $qty;
            $dbArray['week_data'] = $request->week_data ?? '';
            if (empty($exist)) {
                SubscriptionCart::insert($dbArray);
            } else {
                SubscriptionCart::where('id', $exist->id)->update($dbArray);
            }
        }

        return response()->json([
            'result' => true,
            'message' => "Successfully",
        ], 200);
    }

    public function subscription_cart_list(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [

        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $seller_id = $request->seller_id ?? $user->seller_id ?? '';
        if (empty($seller_id)) {
            return response()->json([
                'result' => false,
                'message' => 'Please Choose Seller',
            ], 200);
        }
        $coupon_code = $request->coupon_code ?? '';
        $tips = $request->tips ?? '';
        $cartValue = [];
        $cartArr = [];
        $cart_total = 0;
        $cart_grand_total = 0;
        $cart_grand_total_mrp = 0;
        $cart_discount = 0;
        $cart_qty = 0;
        $coupon_discount = 0;
        $handling_charges = 0;
        $cart_list = SubscriptionCart::where('seller_id', $seller_id)->where('user_id', $user->id)->get();
        $user_address = UserAddress::where('id', $user->addressID)->first();
        if (!empty($cart_list)) {
            foreach ($cart_list as $cart) {
                $vendor_price = CustomHelper::checkVendorPrice($cart->seller_id, $cart->product_id, $cart->variant_id);
                if (!empty($vendor_price)) {
                    $product = Product::where('id', $cart->product_id)->first();
                    $dbArray = [];
                    $dbArray['product_id'] = $cart->product_id;
                    $dbArray['varient_id'] = $cart->variant_id;
                    $dbArray['product_name'] = $product->name ?? '';
                    $dbArray['unit'] = $vendor_price->unit ?? '';
                    $dbArray['unit_value'] = $vendor_price->unit_value ?? '';
                    $dbArray['subscription_price'] = $vendor_price->subscription_price ?? '';
                    $dbArray['product_image'] = CustomHelper::getImageUrl('products', $product->image ?? '');

                    $dbArray['qty'] = $cart->qty ?? 0;
                    $dbArray['selling_price'] = $vendor_price->selling_price ?? 0;
                    $dbArray['mrp'] = $vendor_price->mrp ?? 0;
                    $total_cart_price = (int) $cart->qty * (int) $vendor_price->subscription_price;
                    $total_mrp = (int) $cart->qty * (int) $vendor_price->mrp;
                    $total_product_price = (int) $total_cart_price ?? 0;
                    $dbArray['total_mrp'] = $total_mrp;
                    $dbArray['total_product_price'] = $total_product_price;
                    $cart_qty += (int) $cart->qty;
                    $discount = (int) $vendor_price->mrp - (int) $vendor_price->subscription_price;
                    $total_discount = (int) $cart->qty * (int) $discount;

                    $dbArray['total_price'] = $total_cart_price;
                    $dbArray['type'] = $cart->type ?? '';
                    $next_day = date('Y-m-d', strtotime("+1 day"));
                    $dbArray['start_date'] = $cart->start_date ?? $next_day;
                    $dbArray['end_date'] = $cart->end_date ?? '';
                    $dbArray['week_data'] = $cart->week_data ?? '';

                    $calculateTotalSubscriptionAmount = self::calculateTotalSubsAmount($user, $cart);
                    if (!empty($calculateTotalSubscriptionAmount)) {
                        $cart_grand_total += $calculateTotalSubscriptionAmount['grand_total'] ?? 0;
                        $cart_grand_total_mrp += $calculateTotalSubscriptionAmount['grand_total_mrp'] ?? 0;
                        $cart_discount += ($total_discount * $calculateTotalSubscriptionAmount['total_days']);
                    }
                    $dbArray['calculateTotalSubscriptionAmount'] = $calculateTotalSubscriptionAmount;
                    $cartArr[] = $dbArray;
                    $cart_total += $total_cart_price;
                }
            }
        }



        $delivery_charges = 0;
        $total_price = $cart_total + $delivery_charges + (int) $tips + (int) $handling_charges;
        $cartValue['cart_grand_total'] = $cart_grand_total;
        $cartValue['cart_grand_total_mrp'] = $cart_grand_total_mrp;
        $cartValue['total_price'] = $total_price;
        $cartValue['cart_price'] = $cart_total;
        $cartValue['cart_qty'] = $cart_qty;
        $cartValue['total_discount'] = $cart_discount;
        $cartValue['delivery_charges'] = $delivery_charges;
        $cartValue['tips'] = $tips;
        $cartValue['coupon_discount'] = $coupon_discount;
        $cartValue['handling_charges'] = $handling_charges;
        $cartValue['coupon_code'] = $coupon_code;
        $cartValue['wallet'] = $user->wallet ?? 0;
        $cartValue['cashback_wallet'] = $user->cashback_wallet ?? 0;
        $cartValue['saved_delivery_fee'] = 10;
        $cartValue['actual_delivery_fee'] = 10;
        $cartValue['surge_fee'] = 10;

        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'cartValue' => $cartValue,
            'cart_list' => $cartArr,
            'user_address' => $user_address,
        ], 200);
    }


    public function calculateTotalSubsAmount($user, $cart)
    {
        $totalArr = null;
        $total_cart_price = 0;
        $total_cart_mrp = 0;
        $grand_total_mrp = 0;
        $grand_total = 0;

        $vendor_price = CustomHelper::checkVendorPrice($cart->seller_id, $cart->product_id, $cart->variant_id);

        if (!empty($vendor_price)) {
            $total_cart_price = (int) $cart->qty * (int) $vendor_price->subscription_price;
            $total_cart_mrp = (int) $cart->qty * (int) $vendor_price->mrp;

            $week_data = $cart->week_data ?? '';
            $data = [];
            $data['type'] = $cart->type ?? '';
            $data['startdate'] = $cart->start_date ?? '';
            $data['week_data'] = $week_data;
            $request = new Request(query: $data);
            $calender = self::getCalenderData($request);

            $total_days = 0;
            $last_date = '';
            $total_qty_cart = 0;
            $calender_data = $calender->original['calenderData'] ?? '';
            if (!empty($calender_data)) {
                foreach ($calender_data as $cal) {
                    $calenderDataMonth = $cal['calenderDataMonth'] ?? '';
                    if (!empty($calenderDataMonth)) {
                        foreach ($calenderDataMonth as $sel) {
                            if ($sel['is_selected'] == 1) {
                                $total_days += 1;
                                $last_date = $sel['date'] ?? '';
                                $total_qty_cart += $sel['total_qty'] ?? 0;
                            }
                        }
                    }
                }
            }


            if ($cart->type == 'weekly') {

                $grand_total = ((int) $total_qty_cart * (int) $vendor_price->subscription_price);
                $grand_total_mrp = ((int) $total_qty_cart * (int) $vendor_price->mrp);
            } else {
                $grand_total = (int) $total_cart_price * (int) $total_days;
                $grand_total_mrp = (int) $total_cart_mrp * (int) $total_days;
            }
            $totalArr['total_days'] = $total_days;
            $totalArr['grand_total_mrp'] = $grand_total_mrp;
            $totalArr['grand_total'] = $grand_total;
            $totalArr['total_qty_cart'] = $total_qty_cart;
            $totalArr['last_date'] = $last_date;
            $totalArr['total_cart_price'] = $total_cart_price;
        }

        return $totalArr;
    }


    public function place_subscription_order(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "address_id" => "required"
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }

        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $seller_id = $request->seller_id ?? $user->seller_id ?? '';
        $cart_list = SubscriptionCart::where('seller_id', $seller_id)->where('user_id', $user->id)->first();
        $payment_data = null;
        $product_id = $request->product_id ?? '';
        $varient_id = $request->varient_id ?? '';
        $taken_subscription_id = $request->taken_subscription_id ?? '';
        $type = $request->type ?? '';
        $subscription_order_id = 0;
        $subscription_data = $request->subscription_data ?? '';
        $qty = 0;
        if ($type == 'weekly') {
            $subscription_data = json_decode($subscription_data);
            if (!empty($subscription_data)) {
                foreach ($subscription_data as $key) {
                    if ((int) $key->qty > 0) {
                        $qty += (int) $key->qty;
                    }
                }
            }
        } else {
            $qty = $request->qty;
        }
        $start_date = $request->start_date ?? '';
        $end_date = $request->last_date ?? '';
        if ($type == 'one_time') {
            //            $end_date = $start_date;
        }
        if ($type == 'daily' || $type == 'alternative') {
            //$end_date = date("Y-m-t", strtotime($start_date));;
        }
        if ($type == 'weekly') {
            $start_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
            //$end_date = date("Y-m-t", strtotime($start_date));
        }
        $vendor_price = CustomHelper::checkVendorPrice($cart_list->seller_id, $cart_list->product_id, $cart_list->variant_id);
        if (!empty($vendor_price)) {
            $product = Product::where('id', $cart_list->product_id)->first();
            $dbArray = [];
            $dbArray['order_status'] = 1;
            $dbArray['product_id'] = $cart_list->product_id ?? '';
            $dbArray['varient_id'] = $cart_list->variant_id ?? '';
            $dbArray['seller_id'] = $cart_list->seller_id ?? '';
            $dbArray['unit'] = $vendor_price->unit ?? '';
            $dbArray['unit_value'] = $vendor_price->unit_value ?? '';
            $dbArray['subscription_price'] = $vendor_price->subscription_price ?? '';
            $dbArray['qty'] = $qty;
            $dbArray['selling_price'] = $vendor_price->selling_price;
            $dbArray['mrp'] = $vendor_price->mrp;
            $total_cart_price = (int) $qty * (int) $vendor_price->subscription_price;
            $discount = (int) $vendor_price->mrp - (int) $vendor_price->subscription_price;
            $dbArray['total_price'] = $total_cart_price;
            $dbArray['user_id'] = $user->id;
            $dbArray['subscription_id'] = $user->subscription_id ?? '';
            $dbArray['type'] = $request->type ?? '';
            $dbArray['start_date'] = $start_date ?? '';
            $dbArray['end_date'] = $end_date ?? '';
            $dbArray['address_id'] = $request->address_id ?? '';
            $dbArray['subscription_data'] = $request->subscription_data ?? '';
            $dbArray['taken_subscription_id'] = $request->taken_subscription_id ?? '';
            $subscription_order_id = SubscriptionOrder::insertGetId($dbArray);
        }

        $data = [
            "title" => "Order Placed Successfully",
            "body" => "New Subscription Order Placed Successfully",
            "type" => "subscription"
        ];
        $success = CustomHelper::fcmNotification($user->device_token, $data);

        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'payment_data' => $payment_data,
            'subscription_order_id' => $subscription_order_id,
        ], 200);
    }


    public function my_subscription_products(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [

        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }


        ////////////
        $current_date = date('Y-m-d');
        $date = $request->date ?? $current_date;
        $subscription_ordersArr = [];
        $subscription_orders = SubscriptionOrder::where('user_id', $user->id)->where('is_delete', 0)->latest()->get();
        if (!empty($subscription_orders)) {
            foreach ($subscription_orders as $orders) {
                if (strtotime($orders->end_date) < strtotime(date('Y-m-d'))) {
                    $orders->order_status = 0;
                    $orders->save();
                }
                $check = DB::table('subscribtion_cancel')->where('user_id', $user->id)->where('subs_order_id', $orders->id)->whereDate('date', date('Y-m-d'))->first();
                if (!empty($check)) {
                    $orders->order_status = 3;
                }
                $start_date = '';
                $end_date = '';
                $paused_data = DB::table('subscribtion_cancel')->where('user_id', $user->id)->where('subs_order_id', $orders->id)->whereDate('date', '>=', date('Y-m-d'))->get();
                if (!empty($paused_data)) {
                    $start_date = $paused_data[0]->date ?? '';
                    $count = count($paused_data);
                    $end_date = $paused_data[$count - 1]->date ?? '';
                }

                $orders->pause_start_date = $start_date;
                $orders->pause_end_date = $end_date;

                $products = Product::where('id', $orders->product_id)->first();
                $orders->product_name = $products->name ?? '';
                $vendor_price = CustomHelper::checkVendorPrice($orders->seller_id, $orders->product_id, $orders->variant_id);
                $orders->product_image = CustomHelper::getImageUrl('products', $products->image ?? '');
                $orders->variant_id = $orders->varient_id ?? '';
                $orders->week_data = $orders->subscription_data ?? '';
                $calculateTotalSubscriptionAmount = self::calculateTotalSubsAmount($user, $orders);
                if (!empty($vendor_price)) {
                    $discount = (int) $vendor_price->mrp - (int) $vendor_price->subscription_price;
                    $total_discount = (int) $orders->qty * (int) $discount;
                }
                $orders->calculateTotalSubscriptionAmount = $calculateTotalSubscriptionAmount;
                $subscription_ordersArr[] = $orders;
            }
        }

        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'subscription_order' => $subscription_ordersArr,
        ], status: 200);
    }

    public function my_subscription_product_details(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "order_id" => "required"
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }


        ////////////
        $current_date = date('Y-m-d');
        $date = $request->date ?? $current_date;
        $subscription_ordersArr = null;
        $subscription_order_generated = null;
        $orders = SubscriptionOrder::where('user_id', $user->id)->where('is_delete', 0)->where('id', $request->order_id)->first();
        if (!empty($orders)) {
            $products = Product::where('id', $orders->product_id)->first();
            $orders->product_name = $products->name ?? '';
            $vendor_price = CustomHelper::checkVendorPrice($orders->seller_id, $orders->product_id, $orders->variant_id);
            $orders->product_image = CustomHelper::getImageUrl('products', $products->image ?? '');
            $orders->variant_id = $orders->varient_id ?? '';
            $orders->week_data = $orders->subscription_data ?? '';
            $calculateTotalSubscriptionAmount = self::calculateTotalSubsAmount($user, $orders);
            $orders->calculateTotalSubscriptionAmount = $calculateTotalSubscriptionAmount;
            $check = DB::table('subscribtion_cancel')->where('user_id', $user->id)->where('subs_order_id', $orders->id)->whereDate('date', date('Y-m-d'))->first();
            if (!empty($check)) {
                $orders->order_status = 3;
            }
            $start_date = '';
            $end_date = '';
            $paused_data = DB::table('subscribtion_cancel')->where('user_id', $user->id)->where('subs_order_id', $orders->id)->whereDate('date', '>=', date('Y-m-d'))->get();
            if (!empty($paused_data)) {
                $start_date = $paused_data[0]->date ?? '';
                $count = count($paused_data);
                $end_date = $paused_data[$count - 1]->date ?? '';
            }
            $orders->pause_start_date = $start_date;
            $orders->pause_end_date = $end_date;
            $data = [];

            $data['currentYear'] = date('Y', strtotime($orders->start_date));
            $data['currentMonth'] = date('m', strtotime($orders->start_date));
            $data['type'] = $orders->type ?? '';
            $data['startdate'] = $orders->start_date ?? '';
            $data['week_data'] = $orders->subscription_data ?? '';
            $data['order_id'] = $orders->id ?? '';
            $data['user_id'] = $user->id ?? '';

            $request = new Request($data);

            $calender = self::getCalenderData($request);
            $calender_data = $calender->original['calenderData'] ?? '';
            $orders->calender = $calender_data;

            $subscription_ordersArr = $orders;

            $subscription_order_generated = GenerateSubscriptionOrder::where('user_id', $user->id)->where('subscription_id', $orders->id)->get();
            if (!empty($subscription_order_generated)) {
                foreach ($subscription_order_generated as $subs) {

                }
            }

        }


        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'subscription_order' => $subscription_ordersArr,
            'subscription_order_generated' => $subscription_order_generated,
        ], status: 200);
    }

    public function cancel_single_date(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "order_id" => "required",
            "date" => "required",
            "end_date" => "required",
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $date = $request->date ?? '';
        $end_date = $request->end_date ?? '';

        ////////////
        $success = null;
        $dateArray = [];
        $existArr = [];
        $orders = SubscriptionOrder::where('user_id', $user->id)->where('is_delete', 0)->where('id', $request->order_id)->first();
        if (!empty($orders)) {

            $startDate = new DateTime($date);
            $endDate = new DateTime($end_date);

            while ($startDate <= $endDate) {
                $date_val = $startDate->format('Y-m-d');
                $check = CancelledSubscription::where('subs_order_id', $request->order_id)->where('user_id', $user->id)->whereDate('date', $date_val)->first();
                if (empty($check)) {
                    $dbArray = [];
                    $dbArray['user_id'] = $user->id ?? '';
                    $dbArray['subs_order_id'] = $orders->id ?? '';
                    $dbArray['date'] = $date_val ?? '';
                    $dbArray['reason'] = $request->reason ?? '';
                    CancelledSubscription::insert($dbArray);
                } else {
                    $dateArray[] = $date_val;
                }
                $startDate->modify('+1 day');
            }
            if (!empty($existArr)) {
                $data = [
                    "title" => "Subscription Paused",
                    "body" => "Subscription is Paused For " . implode(",", $existArr),
                    "type" => "cancel_subscription"
                ];
                $success = CustomHelper::fcmNotification($user->device_token, $data);
            }
        }

        $message = 'Successfully';
        if (!empty($dateArray)) {
            $message = "You Have already canced for date " . implode(",", $dateArray);
        }
        return response()->json([
            'result' => true,
            'message' => $message,
            'success' => $success,
            'dateArray' => $dateArray,
        ], status: 200);
    }
    public function cancel_subscription(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "order_id" => "required",
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $date = $request->date ?? '';
        $end_date = $request->end_date ?? '';

        ////////////
        $success = null;
        $orders = SubscriptionOrder::where('user_id', $user->id)->where('is_delete', 0)->where('id', $request->order_id)->first();
        if (!empty($orders)) {
            $orders->order_status = 2;
            $orders->save();
            $data = [
                "title" => "Cancelled",
                "body" => "Your Subscription is Cancelled ",
                "type" => "cancel_subscription"
            ];
            $success = CustomHelper::fcmNotification($user->device_token, $data);
        }

        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'success' => $success
        ], status: 200);
    }

    public function my_subscription_order_details(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "subscription_id" => "required"
        ]);
        $user = null;
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => json_encode($validator->errors()),
            ], 400);
        }
        $user = auth()->user();
        if (empty($user)) {
            return response()->json([
                'result' => false,
                'message' => '',
            ], 401);
        }
        $subscription_data = [];
        $subscription__orders = GenerateSubscriptionOrder::where('subscription_id', $request->subscription_id)->where('user_id', $user->id)->orderBy('date')->get();
        if (!empty($subscription__orders)) {
            foreach ($subscription__orders as $subscription__order) {
                $subscription_data[] = $subscription__order;
            }
        }
        return response()->json([
            'result' => true,
            'message' => "Successfully",
            'subscription_data' => $subscription_data,
        ], 200);
    }



}
