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
use App\Models\Blocks;
use App\Models\NewUpdates;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class TestimonialController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $new_updates = NewUpdates::where('is_delete', 0)->latest()->paginate(10);
        $data['new_updates'] = $new_updates;
        return view('new_updates.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;
        $new_updates = '';
        if (is_numeric($id) && $id > 0) {
            $new_updates = NewUpdates::find($id);
            if (empty($new_updates)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/new_updates');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/new_updates';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'new_updates has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'new_updates has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Wellness Series';

        if (!empty($new_updates)) {
            $page_heading = 'Update Wellness Series';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['new_updates'] = $new_updates;

        return view('new_updates.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url']);
        $oldImg = '';
       
        $admin = new NewUpdates();

        if (is_numeric($id) && $id > 0) {
            $exist = NewUpdates::find($id);
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
           // $this->saveImage($request, $admin, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $banner, $oldImg = '')
    {

        $image_text = $request->image_text??'';
        if(!empty($image_text)){
            $image_val = $image_text[0]??"";
            if(!empty($image_val)){
                $banner->banner_img = $image_val;
                $banner->save();
            }
        }
        $file = $request->file('image');
        if ($file) {
            $path = 'banners';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $banner->banner_img = $uploaded_data;
                $banner->save();
            }
        }
    }


    public function delete(Request $request)
    {

        //prd($request->toArray());

        $id = (isset($request->id)) ? $request->id : 0;

        $is_delete = '';

        if (is_numeric($id) && $id > 0) {
            $is_delete = NewUpdates::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'NewUpdates has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
