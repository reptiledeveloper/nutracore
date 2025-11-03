<?php

namespace App\Http\Controllers;

use App\Imports\ProductImport;
use App\Models\Company;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\Models\User;
use App\Imports\UserImport;
use App\Models\Banner;
use App\Models\Admin;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class ConsultationController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function enquiry(Request $request)
    {
        $search = $request->search ?? '';
        $type = $request->type ?? '';
        $is_ban = $request->is_ban ?? '';
        $users = User::where('is_delete', 0)->latest();
        if (!empty($search)) {
            $users->where('name', 'like', '%' . $search . '%');
            $users->orWhere('phone', 'like', '%' . $search . '%');
        }
        if (!empty($type)) {
            $users->where('type', $type);
        }
        if (isset($is_ban)) {
            $users->where('is_ban', $is_ban);
        }
        $users->whereNotNull('gender')
            ->where('gender', '!=', '')
            ->whereNotNull('height')
            ->where('height', '!=', '')
            ->whereNotNull('weight')
            ->where('weight', '!=', '')
            ->whereNotNull('health_profile')
            ->where('health_profile', '!=', '')
            ->whereNotNull('activity')
            ->where('activity', '!=', '')
            ->whereNotNull('food_choice')
            ->where('food_choice', '!=', '');
        $users = $users->paginate(20);
        $data['users'] = $users;
        return view('consultation.index', $data);
    }


    public function update_user(Request $request)
    {
        $lead_status = $request->lead_status??'';
        $user_id = $request->user_id??'';
        User::where('id',$user_id)->update(['lead_status'=>$lead_status]);
        return back();

    }

}
