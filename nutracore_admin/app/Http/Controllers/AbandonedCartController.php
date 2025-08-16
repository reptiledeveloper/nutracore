<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
use App\Models\Cart;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;
use Carbon\Carbon;


class AbandonedCartController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $minutesToConsiderAbandoned = 30;

        // Step 1: get users who have abandoned carts
        $users = DB::table('product_cart as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('orders as o', 'o.userID', '=', 'u.id')
            ->whereNull('o.id')
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                'u.email as user_email',
                'u.phone as user_phone',
                DB::raw("SUM(c.qty * (SELECT v.selling_price FROM product_varients v WHERE v.id = c.variant_id)) as total_amount"),
                DB::raw("MAX(c.created_at) as last_added_at")
            )
            ->groupBy('u.id', 'u.name', 'u.email', 'u.phone')
            ->orderByDesc('last_added_at')
            ->paginate(10);

        // Step 2: fetch products for only these users
        $userIds = $users->pluck('user_id');

        $products = DB::table('product_cart as c')
            ->join('products as p', 'p.id', '=', 'c.product_id')
            ->join('product_varients as v', 'v.id', '=', 'c.variant_id')
            ->whereIn('c.user_id', $userIds)
            ->select(
                'c.user_id',
                'p.name as product_name',
                'v.unit as varient_name',
                'c.qty',
                'v.selling_price',
                DB::raw('(c.qty * v.selling_price) as line_total')
            )
            ->get()
            ->groupBy('user_id');

        // Step 3: attach product list to users
        foreach ($users as $user) {
            $user->products = $products[$user->user_id] ?? collect();
        }

        return view('abandoned_carts.index', [
            'abandonedCarts' => $users
        ]);
    }




}
