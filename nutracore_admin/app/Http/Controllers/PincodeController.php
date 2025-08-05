<?php

namespace App\Http\Controllers;

use App\Models\City;
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
use App\Models\Pincode;
use App\Models\Roles;
use Storage;
use DB;
use Hash;


class PincodeController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $pincodes = Pincode::where('is_delete', 0)->orderBy('id','DESC');
        if (!empty($cities)) {
            $pincodes->where('pincode', 'like', '%' . $search . '%');
        }
        $pincodes = $pincodes->paginate(50);
        $data['pincodes'] = $pincodes;
        return view('pincodes.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $pincode = '';
        if (is_numeric($id) && $id > 0) {
            $pincode = Pincode::find($id);
            if (empty($pincode)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/pincode');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/pincode';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Pincode has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Pincode has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Pincode';

        if (!empty($pincode)) {
            $page_heading = 'Update Pincode';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['pincode'] = $pincode;

        return view('pincodes.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';

        $admin = new Pincode();

        if (is_numeric($id) && $id > 0) {
            $exist = Pincode::find($id);
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
            $is_delete = Pincode::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Pincode has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
