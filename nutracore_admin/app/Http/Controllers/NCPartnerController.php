<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\PartnerCommission;
use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use Carbon\Carbon;
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
use App\Models\NCPartner;
use App\Models\Admin;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class NCPartnerController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function orders(Request $request)
    {
        $data = [];
        $order_status = $request->order_status ?? '';
        $search = $request->search ?? '';
        $vendor_id = $request->vendor_id ?? '';
        $orderID = $request->orderID ?? '';
        $date = $request->date ?? '';
        $agent_id = $request->agent_id ?? '';
        $pos_cancel_type = $request->pos_cancel_type ?? '';
        $payment_method = $request->payment_method ?? '';


        $partner_coupons = DB::table('partner_applications')->where('coupon_code', '!=', null)->pluck('coupon_code')->toArray();


        $orders = Order::where('is_delete', 0)->whereIn('coupon_code', $partner_coupons)->orderBy('id', 'desc');
        if (!empty($order_status)) {
            $orders->where('status', $order_status);
        }
        if (!empty($search)) {
            $orders->where('unique_id', $search);
        }
        if (!empty($vendor_id)) {
            $orders->where('vendor_id', $vendor_id);
        }
        if (!empty($agent_id)) {
            $orders->where('agent_id', $agent_id);
        }
        if (!empty($pos_cancel_type)) {
            $orders->where('pos_cancel_type', $pos_cancel_type);
        }
        if (!empty($payment_method)) {
            $orders->where('payment_method', $payment_method);
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

    public function index(Request $request)
    {
        $nc_partners = NCPartner::latest()->paginate(10);
        $data['nc_partners'] = $nc_partners;
        return view('nc_partners.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;
        $nc_partners = '';
        if (is_numeric($id) && $id > 0) {
            $nc_partners = NCPartner::find($id);
            if (empty($nc_partners)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/nc_partners');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/nc_partners';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'NCPartner has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'NCPartner has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or emails the administrator.');
            }
        }


        $page_heading = 'Add NCPartner';

        if (!empty($nc_partners)) {
            $page_heading = 'Update NCPartner';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['nc_partners'] = $nc_partners;


        return view('nc_partners.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $settings = DB::table('settings')->where('id',1)->first();
        $data = $request->except(['_token', 'back_url', 'image', 'image_text', 'product_id']);
        $oldImg = '';
        $oldStatus = null;
        $admin = new NCPartner();
        if (is_numeric($id) && $id > 0) {
            $exist = NCPartner::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $admin = $exist;
                $oldImg = $exist->image;
                $oldStatus = $exist->status;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $admin->$key = $val;
        }

        $isSaved = $admin->save();

        if ($isSaved) {
            if ($oldStatus !== $admin->status) {
                if ($request->status == 'Approved') {
                    self:: sendNC_Partner_Approved($admin->mobile_number ?? '');

                }
                if ($request->status == 'Rejected') {
                    self:: sendNC_Partner_Rejected($admin->mobile_number ?? '');
                }
            }
            if(!empty($settings) && $settings->partner_subscription_id != 0){
                $this->assignSubscription($admin);
            }

        }

        return $isSaved;
    }

    public function assignSubscription($partner)
    {
        if($partner->is_activate_subscription == 1){
            return '';
        }
        $user = User::where('phone', $partner->mobile_number)->first();
        $subscription_start = $user->subscription_start ?? '';
        $subscription_end = $user->subscription_end ?? '';

        $settings = DB::table('settings')->where('id',1)->first();

        $subscription_plans = SubscriptionPlans::where('id', $settings->partner_subscription_id)->first();
        if (!empty($subscription_plans)) {
            $duration = (int)$subscription_plans->duration ?? 0;
            if (empty($subscription_start)) {
                $subscription_start = date('Y-m-d');
                $subscription_end = date('Y-m-d', strtotime("+" . $duration . " months", strtotime(date('Y-m-d'))));
            } else {
                $subscription_end = date('Y-m-d', strtotime("+" . $duration . " months", strtotime($subscription_end)));
            }
            $discount = $subscription_plans->max_discount ?? 0;
            $txn_id = 'NCPARTSUBS' . rand(111111, 99999999);
            User::where('id', $user->id)->update(['subscription_start' => $subscription_start, 'subscription_end' => $subscription_end, 'subscription_id' => $subscription_plans->id ?? '']);
            $subsc = new Subscriptions();
            $subsc->user_id = $user->id ?? '';
            $subsc->subscription_id = $subscription_plans->id ?? '';
            $subsc->txn_id = $txn_id ?? '';
            $subsc->paid_status = 1;
            $subsc->taken_by = "Admin";
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
            $data['amount'] = 0;
            $data['type'] = 'DEBIT';
            $data['note'] = 'Take Subscription';
            $data['against_for'] = 'subscription';
            $data['paid_by'] = 'admin';
            $data['orderID'] = 0;
            CustomHelper::saveTransaction($data);

            $event = 'NutraPass Activated';
            $traits = [

            ];
            CustomHelper::trackEvent($user->id, $event, $traits);

            $partner->is_activate_subscription = 1;
            $partner->save();
        }
    }


    public function sendNC_Partner_Approved($mobile)
    {


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"693a54a7169ed9024c2ba2f5\",\n  \"sender\": \"NUTRCR\",\n  \"mobiles\": \"91$mobile\"}",
            CURLOPT_HTTPHEADER => [
                "authkey: 431621ABncLfiKpzo6875ff9bP1",
                "content-type: application/JSON"
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
//        print_r($response);
//        die;
        return $response;
    }

    public function sendNC_Partner_Rejected($mobile)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"693c016069251d4b3b0367cb\",\n  \"sender\": \"NUTRCR\",\n  \"mobiles\": \"91$mobile\"}",
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


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = NCPartner::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'NCPartner has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }

    public function view(Request $request)
    {
        $id = $request->id ?? '';
        $nc_partners = NCPartner::find($id);
        $data['nc_partners'] = $nc_partners;
        $orders = Order::where('coupon_code', $nc_partners->coupon_code)->latest()->paginate(20);
        $data['orders'] = $orders;

//        self::partnerCommissionForOrder(3, $id);
        return view('nc_partners.view', $data);
    }

    public function commission(Request $request)
    {
        $id = $request->id ?? '';
        $nc_partners = NCPartner::find($id);
        $data['nc_partners'] = $nc_partners;
        $commissions = PartnerCommission::where('partner_id', $id)
            ->latest()->paginate(100);
        $data['commissions'] = $commissions;
        return view('nc_partners.commission', $data);
    }

    public function partnerCommissionForOrder($order_id, $partnerId)
    {
        // Fetch necessary models
        $order = Order::find($order_id);
        $partner = NCPartner::find($partnerId); // Assuming NCPartner is your Partner model

        // Basic validation
        if (!$order || !$partner) {
            // Handle case where order or partner is not found
            return ['error' => 'Order or Partner not found.'];
        }

        $orderAmount = $order->total_amount;
        $orderDate = $order->created_at;

        // 1. Get Start & End of Month
        $startOfMonth = Carbon::parse($orderDate)->startOfMonth();
        $endOfMonth = Carbon::parse($orderDate)->endOfMonth();

        // 2. Calculate Total Sales This Month Till Current Order
        $totalSales = DB::table('orders')
            ->where('coupon_code', $partner->coupon_code)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('id', '<=', $order->id)
            ->sum('total_amount');

        // 3. Determine Commission Percent
        $tierData = DB::table('nc_partner_tire_system')
            ->where('from_amount', '<=', $totalSales)
            ->where('to_amount', '>=', $totalSales)
            ->first();

        if ($tierData) {
            $commissionPercent = $tierData->cashback;
            $tierTitle = $tierData->title;
        } else {
            $commissionPercent = 0;
            $tierTitle = 'Unknown';
        }

        // 4. Calculate Commission Amount For This Order
        $commissionAmount = ($orderAmount * $commissionPercent) / 100;

        // --- 5. Save/Update Commission Record and Mark as SETTLED ---

        $dataToSave = [
            'partner_id' => $partnerId,
            'date' => $orderDate,
            'order_amount' => $orderAmount,
            'total_order_amount_till_date' => $totalSales,
            'commission_percent' => $commissionPercent,
            'commission' => $commissionAmount,
            'status' => 1,
            'is_delete' => 0,
            'is_setteled' => 1, // <<< SETTLED HERE
            'updated_at' => now(),
        ];

        $existingCommission = DB::table('partner_commissions')
            ->where('order_id', $order->id)
            ->first();

        if ($existingCommission) {
            DB::table('partner_commissions')
                ->where('id', $existingCommission->id)
                ->update($dataToSave);
            $action = 'Updated & Settled';
        } else {
            $dataToSave['order_id'] = $order->id;
            $dataToSave['created_at'] = now();
            DB::table('partner_commissions')->insert($dataToSave);
            $action = 'Inserted & Settled';
        }

        // --- 6. Add Commission to Partner's Wallet/Balance ---

        // *Crucial Check*: Only update the wallet if the commission was NOT previously settled.
        // If $existingCommission was found AND it was already settled, we skip the wallet update.
        $walletUpdateNeeded = !$existingCommission || ($existingCommission && $existingCommission->is_setteled == 0);

        $walletUpdated = false;

        if ($walletUpdateNeeded && $commissionAmount > 0) {
            // Ensure the model is being used correctly and the column name ('wallet') is accurate.
            $wallet = $partner->wallet ?? 0;
            $new_wallet = floatval($wallet) + floatval($commissionAmount);
            $partnerUpdated = NCPartner::where('id', $partnerId)
                ->update(['wallet' => $new_wallet]);

            $walletUpdated = (bool)$partnerUpdated; // The increment method returns the number of affected rows (1 or 0)
        }


        return [
            'action' => $action,
            'tier' => $tierTitle,
            'commission_percent' => $commissionPercent,
            'commission' => $commissionAmount,
            'total_sales' => $totalSales,
            'wallet_status' => $walletUpdated ? 'Balance Added' : 'No Balance Change Needed',
        ];
    }

    public function withdrawal(Request $request)
    {
        $data = [];

        $withdrawals = DB::table('partner_withdrawals')
            ->orderBy('created_at', 'desc')
            ->paginate(100);
        $data['withdrawals'] = $withdrawals;
        return view('nc_partners.withdrawal', $data);
    }

    public function partner_withdrawal(Request $request)
    {
        $data = [];
        $id = $request->id ?? '';
        $nc_partners = NCPartner::find($id);
        $data['nc_partners'] = $nc_partners;
        $withdrawals = DB::table('partner_withdrawals')
            ->where('partner_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(100);
        $data['withdrawals'] = $withdrawals;
        return view('nc_partners.partner_withdrawal', $data);
    }

    public function with_draw_approve(Request $request)
    {
        $id = $request->id ?? '';
        DB::transaction(function () use ($id) {

            $withdraw = DB::table('partner_withdrawals')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$withdraw || $withdraw->status !== 'pending') {
                return;
            }

            DB::table('partner_withdrawals')
                ->where('id', $id)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now(),
                ]);
        });

        return redirect()->back()->with('success', 'Withdrawal approved successfully.');
    }

    public function with_draw_reject(Request $request)
    {
        $id = $request->id ?? '';
        DB::transaction(function () use ($id) {

            $withdraw = DB::table('partner_withdrawals')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$withdraw || $withdraw->status !== 'pending') {
                return;
            }

            // Refund wallet
            DB::table('partner_applications')
                ->where('id', $withdraw->partner_id)
                ->increment('wallet', $withdraw->amount);

            DB::table('partner_withdrawals')
                ->where('id', $id)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now(),
                ]);
        });

        return redirect()->back()->with('success', 'Withdrawal rejected and amount refunded.');
    }

}
