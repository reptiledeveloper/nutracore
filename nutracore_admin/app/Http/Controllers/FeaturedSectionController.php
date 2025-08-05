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
use App\Models\FeaturedSection;
use App\Models\Roles;
use Yajra\DataTables\DataTables;
use Storage;
use DB;
use Hash;


class FeaturedSectionController extends Controller
{


    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }


    public function index(Request $request)
    {
        $featured_sections = FeaturedSection::where('is_delete', 0)->orderBy('priority')->paginate(50);
        $data['featured_sections'] = $featured_sections;
        return view('featured_sections.index', $data);
    }


    public function add(Request $request)
    {
        $data = [];

        $id = (isset($request->id)) ? $request->id : 0;

        $featured_sections = '';
        if (is_numeric($id) && $id > 0) {
            $featured_sections = FeaturedSection::find($id);
            if (empty($featured_sections)) {
                return redirect($this->ADMIN_ROUTE_NAME . '/featured_section');
            }
        }

        if ($request->method() == 'POST' || $request->method() == 'post') {

            if (empty($back_url)) {
                $back_url = $this->ADMIN_ROUTE_NAME . '/featured_section';
            }
            $rules = [];
            $rules['priority'] = 'required';

            $request->validate($rules);

            $createdCat = $this->save($request, $id);

            if ($createdCat) {
                $alert_msg = 'FeaturedSection has been added successfully.';
                if (is_numeric($id) && $id > 0) {
                    $alert_msg = 'FeaturedSection has been updated successfully.';
                }
                return redirect(url($back_url))->with('alert-success', $alert_msg);
            } else {
                return back()->with('alert-danger', 'something went wrong, please try again or contact the administrator.');
            }
        }


        $page_heading = 'Add ';

        if (!empty($featured_sections)) {
            $page_heading = 'Update';
        }

        $data['page_heading'] = $page_heading;
        $data['id'] = $id;
        $data['featured_sections'] = $featured_sections;

        return view('featured_sections.form', $data);

    }


    public function save(Request $request, $id = 0)
    {

        $data = $request->except(['_token', 'back_url', 'image']);
        $oldImg = '';
        if (!empty($request->category_ids)) {
            $data['category_ids'] = implode(",", $request->category_ids);
        }
        if (!empty($request->product_ids)) {
            $data['product_ids'] = implode(",", $request->product_ids);
        }
        $admin = new FeaturedSection();

        if (is_numeric($id) && $id > 0) {
            $exist = FeaturedSection::find($id);
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
        $fileArr = [];
        $files = $request->file('image');
        if (!empty($banner->image)) {
            $images = $banner->image ?? '';
            $images = explode(",", $images);
            foreach ($images as $image) {
                $fileArr[] = $image;
            }
        }
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = 'featured_section';
                $uploaded_data = CustomHelper::UploadImage($file, $path);
                $fileArr[] = $uploaded_data;
            }
            if (!empty($fileArr)) {
                $banner->image = implode(",", $fileArr);
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
            $is_delete = FeaturedSection::where('id', $id)->update(['is_delete' => 1]);
        }

        if (!empty($is_delete)) {
            return back()->with('alert-success', 'FeaturedSection has been deleted successfully.');
        } else {
            return back()->with('alert-danger', 'something went wrong, please try again...');
        }
    }


}
