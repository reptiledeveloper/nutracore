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
use App\Models\Rating;
use App\Models\Banner;
use App\Models\FAQ;
use App\Models\Admin;
use App\Models\Blocks;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class RatingController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $ratings = Rating::latest()->paginate(10);
        $data['ratings'] = $ratings;
        return view('ratings.index', $data);
    }


    public function update_status(Request $request)
    {

        //prd($request->toArray());
        $id = (isset($request->id)) ? $request->id : 0;
        $is_delete = '';
        if (is_numeric($id) && $id > 0) {
            $is_delete = Rating::where('id', $id)->update(['status' => $request->status]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Rating has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
