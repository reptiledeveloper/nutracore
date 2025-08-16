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
        $abandonedCarts = DB::table('product_cart as c')
            ->join('users as u', 'u.id', '=', 'c.user_id')
            ->join('products as p', 'p.id', '=', 'c.product_id')
            ->join('product_varients as v', 'v.id', '=', 'c.variant_id')
            ->leftJoin('orders as o', function ($join) {
                $join->on('o.user_id', '=', 'u.id');
            })
            ->whereNull('o.id') // means no order placed (abandoned)
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                'u.email as user_email',
                DB::raw("GROUP_CONCAT(CONCAT(p.name, ' (x', c.qty, ')') SEPARATOR ', ') as product_list"),
                DB::raw("SUM(v.price * c.qty) as total_amount"),
                DB::raw("MAX(c.created_at) as last_added_at")
            )
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('last_added_at')
            ->paginate(10);


        $data['abandoned_carts'] = $abandonedCarts;
        return view('abandoned_carts.index', $data);
    }



}
