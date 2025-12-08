<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\PartnerCommission;
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

        $data = $request->except(['_token', 'back_url', 'image', 'image_text', 'product_id']);
        $oldImg = '';

        $admin = new NCPartner();
        if (is_numeric($id) && $id > 0) {
            $exist = NCPartner::find($id);
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

        }

        return $isSaved;
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

        self::partnerCommissionForOrder(3, $id);
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


}
