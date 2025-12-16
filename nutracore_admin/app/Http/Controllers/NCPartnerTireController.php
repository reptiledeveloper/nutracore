<?php

namespace App\Http\Controllers;

use App\Models\NCPartnerTireSystem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Auth;
use Validator;
use App\Models\Category;
use App\Models\FreeProduct;
use App\Models\LoyalitySystem;

use Storage;
use DB;
use Illuminate\Support\Facades\Schema;

use Hash;


class NCPartnerTireController extends Controller
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
        $nc_partner_tire = NCPartnerTireSystem::where('is_delete', 0)->orderBy('id', 'desc');
        if (!empty($search)) {
            //$nc_partner_tire->where('name', 'like', '%' . $search . '%');
        }
        $nc_partner_tire = $nc_partner_tire->paginate(10);
        $data['nc_partner_tire'] = $nc_partner_tire;
        return view('nc_partner_tire.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];
        $id = (isset($request->id)) ? $request->id : 0;

        $nc_partner_tire = '';
        if (is_numeric($id) && $id > 0) {

            $nc_partner_tire = NCPartnerTireSystem::find($id);
            if (empty($nc_partner_tire)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/nc_partner_tire');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/nc_partner_tire';
            }

            $name = (isset($request->name)) ? $request->name : '';


            $rules = [];
            if (is_numeric($id) && $id > 0) {


            } else {

            }
            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'NCPartnerTireSystem has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'NCPartnerTireSystem has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or emails the administrator.');
            }
        }


        $page_heading = 'Add NCPartnerTireSystem';

        if (is_numeric($id) && $id > 0) {
            $page_heading = 'Update NCPartnerTireSystem ';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['nc_partner_tire'] = $nc_partner_tire;

        return view('nc_partner_tire.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image', 'password', 'holiday_date', 'holiday_title']);


        $oldImg = '';

        $categories = new NCPartnerTireSystem;

        if (is_numeric($id) && $id > 0) {
            $exist = NCPartnerTireSystem::find($id);
            if (isset($exist->id) && $exist->id == $id) {
                $categories = $exist;
                $oldImg = $exist->image;
            }
        }
        //prd($oldImg);

        foreach ($data as $key => $val) {
            $categories->$key = $val;
        }

        $isSaved = $categories->save();

        if ($isSaved) {
           // $this->saveImage($request, $categories, $oldImg);
        }

        return $isSaved;
    }

    private function saveImage($request, $categories, $oldImg = '')
    {

        // $file = $request->file('logo');
        // if ($file) {
        //     $fileName = "logo" . time() . $file->getClientOriginalName();
        //     $filePath = 'company/' . $fileName;
        //     $path = Storage::disk('s3')->put($filePath, file_get_contents($file));
        //     $users->logo = $fileName;
        //     $users->save();
        // }

        $file = $request->file('image');
        if ($file) {
            $path = 'categories';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            $categories->image = $uploaded_data;
            $categories->save();
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = NCPartnerTireSystem::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'NCPartnerTireSystem has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
