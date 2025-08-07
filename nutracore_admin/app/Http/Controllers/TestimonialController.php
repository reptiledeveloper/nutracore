<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Testimonial;
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
        $testimonials = Testimonial::where('is_delete', 0)->latest()->paginate(10);
        $data['testimonials'] = $testimonials;
        return view('testimonials.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;
        $testimonial = '';
        if (is_numeric($id) && $id > 0) {
            $testimonial = Testimonial::find($id);
            if (empty($testimonial)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/testimonial');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/testimonial';
            }
            $rules = [];

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'Testimonial has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'Testimonial has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add Testimonial';

        if (!empty($testimonial)) {
            $page_heading = 'Update Testimonial';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['testimonial'] = $testimonial;

        return view('testimonials.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url']);
        $oldImg = '';

        $admin = new Testimonial();

        if (is_numeric($id) && $id > 0) {
            $exist = Testimonial::find($id);
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
            $this->saveImage($request, $admin, $oldImg);
        }

        return $isSaved;
    }


    private function saveImage($request, $banner, $oldImg = '')
    {

        $file = $request->file('image');
        if ($file) {
            $path = 'testimonials';
            $uploaded_data = CustomHelper::UploadImage($file, $path);
            if ($uploaded_data) {
                $banner->image = $uploaded_data;
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
            $is_delete = Testimonial::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'Testimonial has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
