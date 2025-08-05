<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use App\Models\Category;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Storage;
use Validator;


class WithdrawController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }


    public function index(Request $request)
    {
        $data = [];
        $search = $request->search ?? '';
        $withdraw_request = DB::table('delivery_agents_transactions')->where('type', 'withdraw')->orderBy('id', 'desc');

        $withdraw_request = $withdraw_request->paginate(20);
        $data['withdraw_request'] = $withdraw_request;
        return view('withdraw_request.index', $data);
    }
    public function  update_status(Request $request)
    {
        $id = $request->withdraw_id??'';
        $status = $request->status??'';
        $remarks = $request->remarks??'';
        DB::table('delivery_agents_transactions')->where('id',$id)->update(['status'=>$status,'remarks'=>$remarks]);
        return back();
    }


}
