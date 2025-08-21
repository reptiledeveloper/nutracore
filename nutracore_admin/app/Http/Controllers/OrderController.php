<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Company;
use App\Models\DeliveryAgents;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\OrderStatus;
use App\Models\ProductVarient;
use App\Models\Subscriptions;
use App\Models\User;
use App\Models\Varients;
use App\Models\Products;
use App\Models\Vendors;
use Auth;
use DB;
use Google\Service\Monitoring\Custom;
use Hash;
use Illuminate\Http\Request;
use PDF;
use Storage;
use Validator;


class OrderController extends Controller
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
        $orders = Order::where('is_delete', 0)->orderBy('id', 'desc');
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

    public function view(Request $request)
    {
        $data = [];
        $id = $request->id ?? '';
        $orders = Order::where('id', $id)->first();
        $seller = Vendors::where('id', $orders->vendor_id)->first();
        $data['orders'] = $orders;
        $data['seller'] = $seller;
        return view('orders.view', $data);
    }

    public function update_order_statusold(Request $request): \Illuminate\Http\RedirectResponse
    {
        $order_id = $request->order_id ?? '';
        $status = $request->status ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $order = Order::where('id', $order_id)->first();
        if (!empty($order)) {
            if (!empty($vendor_id)) {
                Order::where('id', $order_id)->update(['vendor_id' => $vendor_id]);
            } else {
                Order::where('id', $order_id)->update(['status' => $status]);
                OrderItems::where('order_id', $order_id)->where('status', '!=', 'CANCEL')->update(['status' => $status]);
            }

        }
        return back();
    }


    public function delete_items(Request $request)
    {
        $order_id = $request->order_id ?? '';
        $items_id = $request->items_id ?? '';
        OrderItems::where('id', $items_id)->where('order_id', $order_id)->update(['is_delete' => 1]);
        $this->updateOrder($order_id);
        echo 1;
    }

    public function update_items(Request $request)
    {
        $order_id = $request->order_id ?? '';
        $items_id = $request->items_id ?? '';
        $varient_id = $request->varient_id ?? '';
        $qty = $request->qty ?? '';
        $varients = Varients::where('id', $varient_id)->first();
        if (!empty($varients)) {
            $dbArray = [];
            $dbArray['order_id'] = $order_id;
            $dbArray['product_id'] = $varients->product_id;
            $dbArray['variant_id'] = $varient_id;
            $dbArray['qty'] = $qty;
            $dbArray['price'] = $varients->selling_price ?? 0;
            $dbArray['net_price'] = (int) $varients->selling_price * (int) $qty;
            $dbArray['status'] = 'PLACED';
            if (empty($items_id)) {
                OrderItems::insert($dbArray);
            } else {
                OrderItems::where('id', $items_id)->update($dbArray);
            }
        }
        $this->updateOrder($order_id);

        return back();

    }

    public function updateOrder($order_id): void
    {
        $order = Order::where('id', $order_id)->first();
        if (!empty($order)) {
            $cart_data = CustomHelper::updatedCartData($order->userID, $order_id, $order->coupon_code);
            if (!empty($cart_data)) {
                $cartValue = $cart_data['cartValue'] ?? '';
                $cart_list = $cart_data['cart_list'] ?? '';
                $dbArray = [];
                if ($order->payment_method == 'cod' || $order->payment_method == 'COD') {
                    $dbArray['coupon_discount'] = $cartValue['coupon_discount'] ?? '';
                    $dbArray['delivery_charges'] = $cartValue['delivery_charges'] ?? '';
                    $dbArray['order_amount'] = $cartValue['cart_price'] ?? '';
                    $dbArray['total_amount'] = $cartValue['total_price'] ?? '';
                }
                if ($order->payment_method == 'online' || $order->payment_method == 'ONLINE') {
                    $online_amount = $order->online_amount ?? 0;
                    if ($online_amount <= $cartValue['total_price']) {
                        $dbArray['coupon_discount'] = $cartValue['coupon_discount'] ?? '';
                        $dbArray['delivery_charges'] = $cartValue['delivery_charges'] ?? '';
                        $dbArray['order_amount'] = $cartValue['cart_price'] ?? '';
                        $dbArray['total_amount'] = $cartValue['total_price'] ?? '';
                        $dbArray['online_amount'] = $online_amount ?? 0;
                        $dbArray['cod_amount'] = (int) $cartValue['total_price'] - (int) $online_amount;
                    }
                    if ($online_amount > $cartValue['total_price']) {
                        $dbArray['coupon_discount'] = $cartValue['coupon_discount'] ?? '';
                        $dbArray['delivery_charges'] = $cartValue['delivery_charges'] ?? '';
                        $dbArray['order_amount'] = $cartValue['cart_price'] ?? '';
                        $dbArray['total_amount'] = $cartValue['total_price'] ?? '';
                        $dbArray['online_amount'] = $cartValue['total_price'] ?? 0;
                        $dbArray['cod_amount'] = 0;
                        ////Refund To User////
                        $user = User::where('id', $order->userID)->first();
                        if (!empty($user)) {
                            $amount = $online_amount - $cartValue['total_price'];
                            $wallet = $user->wallet ?? 0;
                            $new_wallet = (int) $wallet + $amount;
                            $user->wallet = $new_wallet;
                            $user->save();
                            $dbArray1 = [];
                            $dbArray1['userID'] = $order->userID;
                            $dbArray1['txn_no'] = rand(1111, 9999999);
                            $dbArray1['amount'] = $amount;
                            $dbArray1['type'] = 'CREDIT';
                            $dbArray1['note'] = 'Refund Order Updated';
                            $dbArray1['against_for'] = 'wallet';
                            $dbArray1['paid_by'] = 'admin';
                            $dbArray1['orderID'] = $order_id;
                            CustomHelper::SaveTransaction($dbArray1);
                        }
                    }
                }
                if (!empty($dbArray)) {
                    Order::where('id', $order_id)->update($dbArray);
                }
                //////////////////////////////////////////////////////////////////////
            }
        }
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $orders = '';
        if (is_numeric($id) && $id > 0) {

            $orders = Order::find($id);
            if (empty($orders)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/orders');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/orders';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {

            } else {

            }
            $this->validate($request, $rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Order has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Order has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Order';
        $verients = [];
        $product_images = [];
        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update Order ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['orders'] = $orders;
        return view('orders.form', $data);

    }


    public function save(Request $request, $id = 0)
    {
        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';
        $order = new Order;
        if (is_numeric($id) && $id > 0) {
            $exist = Order::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $order = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);
        foreach ($data as $key => $val) {
            $order->$key = $val;
        }

        $isSaved = $order->save();

        if ($isSaved) {


        }

        return $isSaved;
    }


    public function generateInvoicePdf(Request $request)
    {
        $orderID = $request->id;
        $orders = Order::where('id', $orderID)->first();
        $seller_details = Vendors::where('id', $orders->id)->first();
        $data = ['orders' => $orders, 'seller_details' => $seller_details];

        $pdf = PDF::loadView('orders.saleinvoice_80', $data);
        $pdf->setPaper([0, 0, 226, 9999]);
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ]);

        $filename = 'Invoice_' . $orderID . 'order' . rand(111, 999999) . time() . '.pdf';
        return $pdf->stream($filename);
    }


    public function get_varients(Request $request){
        $product_id = $request->product_id??'';
        $html = '<option value="" selected>Choose Varient</option>';
        $varients = CustomHelper::getProductVarients($product_id);
        if(!empty($varients)){
            foreach($varients as $varient){
                $html.='<option value='.$varient->id.'>'.$varient->unit.'</option>';
            }
        }
        echo $html;
    }
     public function get_varient_detail(Request $request){
        $varient_id = $request->varient_id??'';
        $varient = ProductVarient::find($varient_id);
        return json_encode(['varient'=>$varient]);
    }

    public function update_order(Request $request){
        $id = $request->id??'';
        $product_id = $request->product_id??'';
        $varient_id = $request->varient_id??'';
        $price = $request->price??'';
        $qty = $request->qty??'';
        $order = Order::find($id);
        $varient = ProductVarient::find($id);
        $selling_price = $varient->selling_price??'';
        $order_amount = $order->order_amount??'';
        $total_amount = $order->total_amount??'';
        $order_amount = (int)$order_amount + (int)$selling_price;
        $total_amount = (int)$total_amount + (int)$selling_price;
        $dbArray = [];
        $dbArray['order_amount'] = $order_amount;
        $dbArray['total_amount'] = $total_amount;
        Order::where('id',$id)->update($dbArray);

        $order_items = [];
        $order_items['order_id'] = $id;
        $order_items['product_id'] = $product_id;
        $order_items['variant_id'] = $varient_id;
        $order_items['qty'] = $qty;
        $order_items['price'] = $selling_price;
        $order_items['net_price'] = (int)$selling_price * (int)$qty;
        $order_items['subscription_price'] = $varient->subscription_price??'';
        DB::table('order_items')->insert($order_items);
        return back();
    }


    public function update_order_status(Request $request)
    {
        $status = $request->status ?? '';
        $order_id = $request->order_id ?? '';
        $item_id = $request->item_id ?? '';
        $delivery_boy = $request->delivery_boy ?? '';
        $vendor_id = $request->vendor_id ?? '';

        if (!empty($status)) {
            $dbArray = [];
            $dbArray['order_id'] = $order_id;
            $dbArray['status'] = $status;
            $dbArray['updated_by'] = 'admin';
            OrderStatus::where('order_id', $order_id)->insert($dbArray);
        }
        if (!empty($vendor_id)) {
            Order::where('id', $order_id)->update(['vendor_id' => $vendor_id]);
        }
        if (!empty($item_id) && !empty($status)) {
            CustomHelper::updateOrderStatus($order_id, $status);
            OrderItems::where('id', $item_id)->where('order_id', $order_id)->where('status', '!=', 'CANCEL')->where('status', '!=', 'DELIVERED')->update(['status' => $status]);
            ///Send Notification to User & Delivery Boy
        } else if (!empty($order_id) && empty($item_id) && !empty($status)) {
            CustomHelper::updateOrderStatus($order_id, $status);
            Order::where('id', $order_id)->where('status', '!=', 'CANCEL')->where('status', '!=', 'DELIVERED')->update(['status' => $status]);
            OrderItems::where('order_id', $order_id)->where('status', '!=', 'CANCEL')->where('status', '!=', 'DELIVERED')->update(['status' => $status]);
            ///Send Notification to User & Delivery Boy
        } else if (isset($delivery_boy)) {
            Order::where('id', $order_id)->update(['agent_id' => $delivery_boy]);
            ///Send Notification to User & Delivery Boy
            $order = Order::where('id', $order_id)->first();
            $address = $order->house_no ?? '';
            $address .= $order->apartment ?? '';
            $address .= $order->landmark ?? '';
            $count = OrderItems::where('order_id', $order_id)->where('status', '!=', 'CANCEL')->sum('qty');
            $data = [
                'title' => 'BuyBuyCart',
                'body' => 'A New Order Placed',
                'type' => 'order',
                'total_item' => $count,
                'total_amount' => $order->total_amount ?? 0,
                'address' => $address,
                'order_status' => $order->status ?? '',
                'image' => '',
            ];
            $agent = DeliveryAgents::find($delivery_boy);
            $token = $agent->deviceToken ?? '';
            if (!empty($token)) {
                //$success = CustomHelper::fcmNotification($token, $data);
                //                print_r($success);
            }
        }

        if ($status == 'CONFIRM') {
            $order = Order::where('id', $order_id)->first();
            $user = User::where('id', $order->userID)->first();
            $token = $user->device_token ?? '';
            $not = CustomHelper::getNotifyData('order_confirm');
            $description = $not->description ?? '';
            $description = str_replace("##order_id##", $order_id, $description);
            if (!empty($token)) {
                $data = [
                    'orderID' => $order_id,
                    'title' => $not->title ?? '',
                    'body' => $description ?? '',
                ];
                //CustomHelper::fcmNotification($token, $data);
            }
        }

        if ($status == 'OUT_FOR_DELIVERY') {
            $order = Order::where('id', $order_id)->first();
            $user = User::where('id', $order->userID)->first();
            $token = $user->device_token ?? '';
            $not = CustomHelper::getNotifyData('out_for_delivery');
            $description = $not->description ?? '';
            $description = str_replace("##order_id##", $order_id, $description);
            if (!empty($token)) {
                $data = [
                    'orderID' => $order_id,
                    'title' => $not->title ?? '',
                    'body' => $description ?? '',
                ];
               // CustomHelper::fcmNotification($token, $data);
            }
        }

        if ($status == 'DELIVERED') {
            $order = Order::where('id', $order_id)->first();
            $user = User::where('id', $order->userID)->first();
            $token = $user->device_token ?? '';
            $not = CustomHelper::getNotifyData('delivered');
            $description = $not->description ?? '';
            $description = str_replace("##order_id##", $order_id, $description);
            if (!empty($token)) {
                $data = [
                    'orderID' => $order_id,
                    'title' => $not->title ?? '',
                    'body' => $description ?? '',
                ];
               // CustomHelper::fcmNotification($token, $data);
            }
            ////Credit NC Cash
            $this->creditNcCash($order);
        }
        echo 1;
    }


    public function creditNcCash($order)
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
        $dbArray1['note'] = "Earn NC Cash From Order ".$order->id??'';
        $dbArray1['against_for'] = 'cashback_wallet';
        $dbArray1['paid_by'] = 'order';
        $dbArray1['orderID'] = 0;
        CustomHelper::SaveTransaction($dbArray1);
    }


    public function getNcCashPercent($user, $amount)
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
        $active_loyalty = DB::table('loyality_system')
            ->where('status', 1)
            ->where('type', $type)
            ->where('from_amount', '<=', $amount)
            ->where('to_amount', '>=', $amount)
            ->first();
        if (!empty($active_loyalty)) {
            return round(($amount * (int)$active_loyalty->cashback) / 100);
        }
        return 0;

    }
    public function update_logistics(Request $request)
    {

        $order_id = $request->order_id ?? '';
        $logistics = $request->logistics ?? '';
        if (!empty($logistics)) {
            Order::where('id', $order_id)->update(['logistics' => $logistics]);
        }
        echo 1;
    }

    public function bookshipment_shiprocket(Request $request)
    {
        $id = $request->id ?? '';
        $length = $request->length ?? '';
        $breadth = $request->breadth ?? '';
        $height = $request->height ?? '';
        $weight = $request->weight ?? '';
        $logistics = $request->logistics ?? '';
        $courier_id = $request->courier_id ?? '';

        $order = Order::find($id);

        $order_courier = DB::table('order_courier')->where('order_id', $order->id)->first();
        if (empty($order_courier)) {
            $dbArray = [];
            $dbArray['order_id'] = $id;
            $dbArray['logistics'] = $order->logistics ?? '';
            $dbArray['courier_id'] = $courier_id;
            $dbArray['length'] = $length;
            $dbArray['breadth'] = $breadth;
            $dbArray['height'] = $height;
            $dbArray['weight'] = $weight;
            $order_courier_id = DB::table('order_courier')->insertGetId($dbArray);
        } else {
            $dbArray = [];
            $dbArray['length'] = $length;
            $dbArray['breadth'] = $breadth;
            $dbArray['height'] = $height;
            $dbArray['weight'] = $weight;
            DB::table('order_courier')->where('id', $order_courier->id)->update($dbArray);
            $order_courier_id = $order_courier->id ?? '';

        }

        $success = $this->bookShipRocket($order_courier_id);
        return back();
    }

    public function getStateNameFromPincode($pincode)
    {
        $state_name = '';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://www.postalpincode.in/api/pincode/' . $pincode,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if (!empty($response)) {
            if ($response->Status == 'Success') {
                $PostOffice = $response->PostOffice[0] ?? '';
                if (!empty($PostOffice)) {
                    $state_name = $PostOffice->State ?? '';
                }
            }
        }
        return $state_name;
    }
    public function bookShipRocket($order_courier_id)
    {
        $order_courier = DB::table('order_courier')->where('id', $order_courier_id)->first();
        $order = Order::find($order_courier->order_id);
        $user = User::where('id', $order->userID)->first();
        $vendor = Vendors::where('id', $order->vendor_id)->first();

        $address = DB::table('user_address')->where('id', $order->address_id)->first();
        $pincode = $address->pincode ?? '';
        $address = $order->house_no ?? '';
        $address .= $order->apartment ?? '';
        $address .= $order->landmark ?? '';
        $order_itemsArr = [];
        $order_items = OrderItems::where('order_id', $order->id)->get();
        if (!empty($order_items)) {
            foreach ($order_items as $item) {
                $product = Products::where('id', $item->product_id)->first();
                $dbArray = [];
                $dbArray['name'] = $product->name ?? '';
                $dbArray['sku'] = $product->sku ?? '';
                $dbArray['units'] = $item->qty ?? '';
                $dbArray['selling_price'] = $item->price ?? '';
                $dbArray['discount'] = '';
                $dbArray['tax'] = '';
                $dbArray['hsn'] = $product->hsn ?? '';
                $order_itemsArr[] = $dbArray;
            }
        }


        $arrayVar = [
            "order_id" => $order->id ?? '',
            "order_date" => $order->created_at ?? '',
            "pickup_location" => $vendor->name ?? '',
            "comment" => "",
            "billing_customer_name" => $order->customer_name ?? '',
            "billing_last_name" => "",
            "billing_address" => $address,
            "billing_address_2" => $order->landmark ?? '',
            "billing_city" => '',
            "billing_pincode" => (int) $pincode,
            "billing_state" => self::getStateNameFromPincode($pincode),
            "billing_country" => "India",
            "billing_email" => $user->email ?? '',
            "billing_phone" => $user->phone ?? '',
            "shipping_is_billing" => true,
            "shipping_customer_name" => "",
            "shipping_last_name" => "",
            "shipping_address" => "",
            "shipping_address_2" => "",
            "shipping_city" => "",
            "shipping_pincode" => "",
            "shipping_country" => "",
            "shipping_state" => "",
            "shipping_email" => "",
            "shipping_phone" => "",
            "order_items" => $order_itemsArr,
            "payment_method" => "Prepaid",
            "shipping_charges" => 0,
            "giftwrap_charges" => 0,
            "transaction_charges" => 0,
            "total_discount" => 0,
            "sub_total" => $order->order_amount ?? 0,
            "length" => (int) $order_courier->length ?? 0,
            "breadth" => (int) $order_courier->breadth ?? 0,
            "height" => (int) $order_courier->height ?? 0,
            "weight" => (float) $order_courier->weight ?? 0,
        ];
        $curl = curl_init();
        $login_data = CustomHelper::loginShipRocket();
        $token = $login_data->token ?? '';
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($arrayVar),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        DB::table('order_courier')->where('id', $order_courier_id)->update([
            'ship_rocket_order_id' => $response->order_id ?? '',
            'shipment_id' => $response->shipment_id ?? '',
            'channel_order_id' => $response->channel_order_id ?? '',
        ]);
        $this->assignCourier($order_courier_id);
    }


    public function assignCourier($order_courier_id)
    {
        $order_courier = DB::table('order_courier')->where('id', $order_courier_id)->first();
        $login_data = CustomHelper::loginShipRocket();
        $token = $login_data->token ?? '';
        $curl = curl_init();
        $dbArray = [
            "shipment_id" => $order_courier->shipment_id ?? '',
            "courier_id" => $order_courier->courier_id ?? '',
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/assign/awb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dbArray),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        if (!empty($response)) {
            $awb_assign_status = $response->awb_assign_status ?? '';
            $response = $response->response ?? '';
            $data = $response->data ?? '';
            $awb_code = $data->awb_code ?? '';
            DB::table('order_courier')->where('id', $order_courier_id)->update([
                'awb_no' => $awb_code ?? '',
                'awb_assign_status' => $awb_assign_status ?? '',
            ]);
        }
    }

    public function update_address(Request $request)
    {
        $order_id = $request->id ?? '';
        $address_id = $request->address_id ?? '';
        $dbArray['flat_no'] = $request->flat_no ?? '';
        $dbArray['building_name'] = $request->building_name ?? '';
        $dbArray['landmark'] = $request->landmark ?? '';
        $dbArray['pincode'] = $request->pincode ?? '';
        DB::table('user_address')->where('id', $address_id)->update($dbArray);

        $dbArray1 = [];
        $dbArray1['house_no'] = $request->flat_no ?? '';
        $dbArray1['apartment'] = $request->building_name ?? '';
        $dbArray1['landmark'] = $request->landmark ?? '';
        $dbArray1['pincode'] = $request->pincode ?? '';
        DB::table('orders')->where('id', $order_id)->update($dbArray1);
        return back();
    }
}
